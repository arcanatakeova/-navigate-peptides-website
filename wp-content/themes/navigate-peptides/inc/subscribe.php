<?php
/**
 * Newsletter / Email Capture
 *
 * - Registers the `nav_subscriber` custom post type (admin-only, for
 *   storage + export; not exposed publicly).
 * - Registers the REST endpoint `/wp-json/nav/v1/subscribe` that accepts
 *   the footer newsletter form submission.
 * - Stores each subscriber as a post keyed by their email, with IP and
 *   consent timestamp in post meta for compliance audit.
 *
 * @package NavigatePeptides
 */

defined('ABSPATH') || exit;

/* ------------------------------------------------------------------
 * CPT: nav_subscriber (admin-only)
 * ----------------------------------------------------------------*/
add_action('init', function () {
    register_post_type('nav_subscriber', [
        'labels' => [
            'name'          => __('Subscribers', 'navigate-peptides'),
            'singular_name' => __('Subscriber', 'navigate-peptides'),
            'add_new'       => __('Add Subscriber', 'navigate-peptides'),
            'add_new_item'  => __('Add New Subscriber', 'navigate-peptides'),
            'edit_item'     => __('Edit Subscriber', 'navigate-peptides'),
            'search_items'  => __('Search Subscribers', 'navigate-peptides'),
            'menu_name'     => __('Subscribers', 'navigate-peptides'),
        ],
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => false,
        'exclude_from_search' => true,
        'publicly_queryable'  => false,
        'capability_type'     => 'post',
        'capabilities'        => [
            'create_posts' => 'do_not_allow', // only via public form
        ],
        'map_meta_cap'        => true,
        'menu_icon'           => 'dashicons-email-alt',
        'menu_position'       => 26,
        'supports'            => ['title', 'custom-fields'],
    ]);
});

/* ------------------------------------------------------------------
 * Admin list columns
 * ----------------------------------------------------------------*/
add_filter('manage_nav_subscriber_posts_columns', function ($cols) {
    return [
        'cb'    => $cols['cb'] ?? '',
        'title' => __('Email', 'navigate-peptides'),
        'date'  => __('Subscribed', 'navigate-peptides'),
        'source' => __('Source', 'navigate-peptides'),
        'ip'    => __('IP', 'navigate-peptides'),
    ];
});

add_action('manage_nav_subscriber_posts_custom_column', function ($column, $post_id) {
    if ($column === 'source') {
        echo esc_html((string) get_post_meta($post_id, '_nav_source', true) ?: '—');
    } elseif ($column === 'ip') {
        echo esc_html((string) get_post_meta($post_id, '_nav_ip', true) ?: '—');
    }
}, 10, 2);

/* ------------------------------------------------------------------
 * REST endpoint: POST /wp-json/nav/v1/subscribe
 * ----------------------------------------------------------------*/
add_action('rest_api_init', function () {
    register_rest_route('nav/v1', '/subscribe', [
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'nav_handle_subscribe',
        'permission_callback' => '__return_true',
        'args'                => [
            'email' => [
                'required' => true,
                'type'     => 'string',
                'sanitize_callback' => 'sanitize_email',
                'validate_callback' => fn($value) => is_email($value) ? true : new WP_Error('invalid_email', __('Please provide a valid email address.', 'navigate-peptides')),
            ],
            'source' => [
                'required' => false,
                'type'     => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            // Honeypot — should always be empty for humans
            'nav_hp' => [
                'required' => false,
                'type'     => 'string',
            ],
        ],
    ]);
});

function nav_handle_subscribe(WP_REST_Request $request) {
    // Honeypot check — silently accept spam so bots don't adapt, but
    // never actually persist the record.
    $hp = (string) $request->get_param('nav_hp');
    if ($hp !== '') {
        return rest_ensure_response([
            'success' => true,
            'message' => __('Thanks — you\'re on the list.', 'navigate-peptides'),
        ]);
    }

    $ip = function_exists('nav_contact_client_ip') ? nav_contact_client_ip() : (string) ($_SERVER['REMOTE_ADDR'] ?? '');

    // Rate limit: no more than 5 submissions per IP per hour.
    $rate_key = 'nav_subscribe_rl_' . md5($ip);
    $count = (int) get_transient($rate_key);
    if ($count >= 5) {
        return new WP_Error(
            'rate_limited',
            __('Too many signups from this address. Try again later.', 'navigate-peptides'),
            ['status' => 429]
        );
    }
    set_transient($rate_key, $count + 1, HOUR_IN_SECONDS);

    $email  = sanitize_email((string) $request->get_param('email'));
    $source = sanitize_text_field((string) $request->get_param('source')) ?: 'footer';

    if (!is_email($email)) {
        return new WP_Error('invalid_email', __('Please provide a valid email address.', 'navigate-peptides'), ['status' => 400]);
    }

    // Dedupe by email — matched against post_title.
    $existing = get_posts([
        'post_type'   => 'nav_subscriber',
        'post_status' => ['publish', 'private'],
        'title'       => $email,
        'numberposts' => 1,
        'fields'      => 'ids',
    ]);
    if (!empty($existing)) {
        return rest_ensure_response([
            'success' => true,
            'message' => __('You\'re already on the list — thanks.', 'navigate-peptides'),
        ]);
    }

    $post_id = wp_insert_post([
        'post_type'   => 'nav_subscriber',
        'post_status' => 'private',
        'post_title'  => $email,
    ], true);

    if (is_wp_error($post_id) || !$post_id) {
        error_log('[nav_subscribe] wp_insert_post failed: ' . (is_wp_error($post_id) ? $post_id->get_error_message() : 'unknown'));
        return new WP_Error('save_failed', __('Could not save your email. Please try again.', 'navigate-peptides'), ['status' => 500]);
    }

    update_post_meta($post_id, '_nav_source', $source);
    update_post_meta($post_id, '_nav_ip', $ip);
    update_post_meta($post_id, '_nav_consent_ts', current_time('mysql', true));
    update_post_meta($post_id, '_nav_user_agent', substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 200));

    /**
     * Hook for forwarders (Mailchimp, ActiveCampaign, etc.).
     *
     * @param string $email   Subscriber email.
     * @param string $source  Originating form (e.g. "footer").
     * @param int    $post_id Subscriber post ID.
     */
    do_action('nav_new_subscriber', $email, $source, $post_id);

    // Notify admin — useful while volume is low; add_filter to disable.
    if (apply_filters('nav_subscribe_notify_admin', true, $email, $source)) {
        $admin = get_option('admin_email');
        if ($admin && is_email($admin)) {
            wp_mail(
                $admin,
                sprintf(
                    /* translators: %s: site name */
                    __('[%s] New newsletter subscriber', 'navigate-peptides'),
                    get_bloginfo('name')
                ),
                sprintf(
                    "Email: %s\nSource: %s\nIP: %s\nTime (UTC): %s\n",
                    $email,
                    $source,
                    $ip,
                    current_time('mysql', true)
                )
            );
        }
    }

    return rest_ensure_response([
        'success' => true,
        'message' => __('Thanks — you\'re on the list.', 'navigate-peptides'),
    ]);
}
