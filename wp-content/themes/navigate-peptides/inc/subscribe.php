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
        // Anyone can POST — but the footer form ships a `wp_rest` nonce
        // in the X-WP-Nonce header, verified inside the handler. The
        // permission callback has to return true for public signups;
        // nonce verification protects against CSRF of logged-in admins
        // (which would otherwise enroll arbitrary emails with the admin's
        // IP stored as the subscriber record).
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
    // Resolve IP first so honeypot hits + rate-limit decisions are
    // attributed to the originating address for log analysis.
    $ip = function_exists('nav_contact_client_ip')
        ? nav_contact_client_ip()
        : (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');

    $rate_key = 'nav_subscribe_rl_' . md5($ip);
    $count    = (int) get_transient($rate_key);
    // Always increment first — a tripped honeypot or repeated invalid
    // attempts should count toward the limit. Previously the honeypot
    // short-circuit returned before the counter bumped, letting bots
    // enjoy unlimited 200 OK responses.
    set_transient($rate_key, $count + 1, HOUR_IN_SECONDS);
    if ($count >= 5) {
        error_log(sprintf('[nav_subscribe] rate-limited ip=%s', $ip));
        return new WP_Error(
            'rate_limited',
            __('Too many signups from this address. Try again later.', 'navigate-peptides'),
            ['status' => 429]
        );
    }

    // Honeypot trip — log and return a neutral success so bots don't
    // adapt, but the attempt is now rate-counted + recorded.
    $hp = (string) $request->get_param('nav_hp');
    if ($hp !== '') {
        error_log(sprintf('[nav_subscribe] honeypot tripped ip=%s hp=%s', $ip, substr($hp, 0, 80)));
        return rest_ensure_response([
            'success' => true,
            'message' => __('Thanks — you\'re on the list.', 'navigate-peptides'),
        ]);
    }

    // CSRF: require the wp_rest nonce emitted by the footer form. Public
    // signups are allowed, but the nonce prevents a logged-in admin from
    // being CSRFed into enrolling arbitrary emails with their IP.
    $nonce_header = $request->get_header('x_wp_nonce');
    $nonce_param  = (string) $request->get_param('_wpnonce');
    $nonce        = $nonce_header ?: $nonce_param;
    if (!$nonce || !wp_verify_nonce($nonce, 'wp_rest')) {
        error_log(sprintf('[nav_subscribe] nonce failed ip=%s', $ip));
        return new WP_Error(
            'invalid_nonce',
            __('Security check failed. Refresh the page and try again.', 'navigate-peptides'),
            ['status' => 403]
        );
    }

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

    // Meta writes: consent_ts is compliance-critical — if it fails, the
    // subscriber row has no audit trail. Roll back and surface a 500.
    $consent_ok = update_post_meta($post_id, '_nav_consent_ts', current_time('mysql', true));
    if ($consent_ok === false) {
        error_log(sprintf('[nav_subscribe] consent_ts write failed post=%d', $post_id));
        wp_delete_post($post_id, true);
        return new WP_Error(
            'consent_save_failed',
            __('Could not record consent. Please try again.', 'navigate-peptides'),
            ['status' => 500]
        );
    }

    // Soft meta — log but don't fail the request.
    foreach ([
        '_nav_source'     => $source,
        '_nav_ip'         => $ip,
        '_nav_user_agent' => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 200),
    ] as $key => $value) {
        if (update_post_meta($post_id, $key, $value) === false) {
            error_log(sprintf('[nav_subscribe] meta write failed post=%d key=%s', $post_id, $key));
        }
    }

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
