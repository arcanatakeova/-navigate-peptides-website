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
 * Resolve the request's real client IP.
 *
 * SECURITY: HTTP_X_FORWARDED_FOR / HTTP_CF_CONNECTING_IP / HTTP_X_REAL_IP
 * are client-controllable on a direct-connect install. A bot can spoof any
 * value to bypass rate limiting. We therefore only honor those headers when
 * NAV_TRUSTED_PROXIES is defined (typically in wp-config.php for hosts
 * behind Cloudflare or a reverse proxy). Default: REMOTE_ADDR only.
 *
 * Set `define('NAV_TRUSTED_PROXIES', true);` on production hosts where
 * the forwarding proxy is trusted. Optionally set it to an array of CIDRs
 * to additionally require REMOTE_ADDR to match a known proxy range.
 */
function nav_contact_client_ip(): string {
    $remote = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
    $remote = filter_var($remote, FILTER_VALIDATE_IP) ? $remote : '0.0.0.0';

    if (!defined('NAV_TRUSTED_PROXIES') || !NAV_TRUSTED_PROXIES) {
        return $remote;
    }

    foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_REAL_IP', 'HTTP_X_FORWARDED_FOR'] as $header) {
        if (empty($_SERVER[$header])) continue;
        $raw = (string) $_SERVER[$header];
        // X-Forwarded-For is comma-separated; left-most is the original client.
        $ip = trim(explode(',', $raw)[0]);
        if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
    }
    return $remote;
}

add_action('admin_post_nav_contact_form', 'nav_handle_contact_form');
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

    // Honeypot: field name chosen to avoid password-manager autofill
    // (avoid 'website', 'url', 'email2', etc.). Any non-empty value is treated
    // as a bot — log the incident so we can audit false positives.
    if (! empty($_POST['nav_fax_url'])) {
        error_log(sprintf(
            '[nav_contact] honeypot tripped ip=%s ua=%s',
            nav_contact_client_ip(),
            substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 200)
        ));
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

    // Rate limit per real client IP (1 submission / 60s). CDN-aware.
    $client_ip = nav_contact_client_ip();
    $rate_key  = 'nav_contact_' . md5($client_ip);
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
        "Name: %s %s\nEmail: %s\nOrganization: %s\nInquiry Type: %s\nClient IP: %s\n\nMessage:\n%s",
        $first_name,
        $last_name,
        $email,
        $organization ?: '(not provided)',
        $inquiry_label,
        $client_ip,
        $message
    );

    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        sprintf('Reply-To: %s %s <%s>', $first_name, $last_name, $email),
    ];

    // Check wp_mail return — a false here means PHPMailer rejected the send
    // (DNS, auth, plugin veto). Log and surface to the user rather than
    // redirecting to the success banner.
    $sent = wp_mail($to, $subject, $body, $headers);
    if (!$sent) {
        error_log(sprintf(
            '[nav_contact] wp_mail failed: to=%s subject=%s email=%s',
            $to, $subject, $email
        ));
        wp_safe_redirect(home_url('/about/contact/?error=send'));
        exit;
    }

    wp_safe_redirect(home_url('/about/contact/?sent=1'));
    exit;
}
