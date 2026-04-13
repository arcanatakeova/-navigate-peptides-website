<?php
/**
 * Contact Form Handler
 *
 * Processes the fallback contact form when Contact Form 7 is not installed.
 *
 * @package NavigatePeptides
 */

defined('ABSPATH') || exit;

/**
 * Handle contact form submission (logged-in users).
 */
add_action('admin_post_nav_contact_form', 'nav_handle_contact_form');

/**
 * Handle contact form submission (non-logged-in users).
 */
add_action('admin_post_nopriv_nav_contact_form', 'nav_handle_contact_form');

function nav_handle_contact_form(): void {
    // Verify nonce
    if (
        ! isset($_POST['nav_nonce']) ||
        ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nav_nonce'])), 'nav_contact_nonce')
    ) {
        wp_die(
            esc_html__('Security check failed. Please go back and try again.', 'navigate-peptides'),
            esc_html__('Error', 'navigate-peptides'),
            ['response' => 403, 'back_link' => true]
        );
    }

    // Honeypot: reject if hidden field is filled (anti-spam)
    if (! empty($_POST['nav_website'])) {
        wp_safe_redirect(home_url('/about/contact/?sent=1'));
        exit;
    }

    // Sanitize inputs
    $first_name   = sanitize_text_field(wp_unslash($_POST['first_name'] ?? ''));
    $last_name    = sanitize_text_field(wp_unslash($_POST['last_name'] ?? ''));
    $email        = sanitize_email(wp_unslash($_POST['email'] ?? ''));
    $organization = sanitize_text_field(wp_unslash($_POST['organization'] ?? ''));
    $inquiry_type = sanitize_text_field(wp_unslash($_POST['inquiry_type'] ?? 'general'));
    $message      = sanitize_textarea_field(wp_unslash($_POST['message'] ?? ''));

    // Validate required fields
    if (empty($first_name) || empty($email) || empty($message)) {
        wp_safe_redirect(home_url('/about/contact/?error=required'));
        exit;
    }

    if (! is_email($email)) {
        wp_safe_redirect(home_url('/about/contact/?error=email'));
        exit;
    }

    // Rate limiting via transient (1 submission per IP per 60 seconds)
    $rate_key = 'nav_contact_' . md5($_SERVER['REMOTE_ADDR'] ?? '');
    if (get_transient($rate_key)) {
        wp_safe_redirect(home_url('/about/contact/?error=rate'));
        exit;
    }
    set_transient($rate_key, 1, 60);

    // Format inquiry type for display
    $inquiry_labels = [
        'general'   => 'General Inquiry',
        'wholesale' => 'Wholesale / Bulk Pricing',
        'account'   => 'Account Access Request',
        'technical' => 'Technical / Product Question',
        'coa'       => 'COA Request',
    ];
    $inquiry_label = $inquiry_labels[$inquiry_type] ?? 'General Inquiry';

    // Build email
    $to      = get_option('admin_email');
    $subject = sprintf('[Navigate Peptides] %s from %s %s', $inquiry_label, $first_name, $last_name);
    $body    = sprintf(
        "Name: %s %s\nEmail: %s\nOrganization: %s\nInquiry Type: %s\n\nMessage:\n%s",
        $first_name,
        $last_name,
        $email,
        $organization ?: '(not provided)',
        $inquiry_label,
        $message
    );

    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        sprintf('Reply-To: %s %s <%s>', $first_name, $last_name, $email),
    ];

    wp_mail($to, $subject, $body, $headers);

    wp_safe_redirect(home_url('/about/contact/?sent=1'));
    exit;
}
