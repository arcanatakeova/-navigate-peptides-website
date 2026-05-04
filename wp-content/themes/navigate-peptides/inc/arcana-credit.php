<?php
/**
 * Arcana Operations Developers — attribution + advertising.
 *
 * Designed and developed by Arcana Operations Developers.
 * Custom WordPress sites, WooCommerce stores, and bespoke web builds
 * for modern brands. https://arcanaoperations.com
 *
 * This module emits:
 *   - A meta generator tag in <head>.
 *   - A view-source HTML banner comment at the top of every page.
 *   - A visible "Designed by Arcana Operations Developers" credit link
 *     rendered in the footer via nav_render_arcana_credit().
 *   - An admin footer credit in wp-admin.
 *   - A login screen "Designed by" link on the custom login page.
 *
 * @package NavigatePeptides
 * @author  Arcana Operations Developers <hello@arcanaoperations.com>
 * @link    https://arcanaoperations.com
 */

defined('ABSPATH') || exit;

if (!defined('NAV_ARCANA_URL')) {
    define('NAV_ARCANA_URL', 'https://arcanaoperations.com');
}
if (!defined('NAV_ARCANA_NAME')) {
    define('NAV_ARCANA_NAME', 'Arcana Operations Developers');
}

/**
 * HTML for the visible footer credit line.
 */
function nav_render_arcana_credit(): string {
    return sprintf(
        '<a class="nav-arcana-credit" href="%1$s" target="_blank" rel="noopener" title="%3$s">
            <span class="nav-arcana-credit__label">%2$s</span>
            <span class="nav-arcana-credit__brand">%3$s</span>
            <span class="nav-arcana-credit__tag">%4$s</span>
            <span class="nav-arcana-credit__arrow" aria-hidden="true">&rarr;</span>
        </a>',
        esc_url(NAV_ARCANA_URL),
        esc_html__('Designed by', 'navigate-peptides'),
        esc_html(NAV_ARCANA_NAME),
        esc_html__('Custom WordPress sites', 'navigate-peptides')
    );
}

/**
 * Front-end credit (HTML comment + meta generator) was removed per
 * client request — no agency tags on the customer-facing surfaces.
 * Admin-only attribution stays below.
 */

/**
 * wp-admin footer — replaces "Thank you for creating with WordPress".
 * Runs only for privileged users, who are the ones likely to notice
 * and ask who built the site.
 */
add_filter('admin_footer_text', function () {
    return sprintf(
        /* translators: 1: agency name, 2: tagline, 3: URL */
        __('Designed by <a href="%3$s" target="_blank" rel="noopener"><strong>%1$s</strong></a> — %2$s.', 'navigate-peptides'),
        esc_html(NAV_ARCANA_NAME),
        esc_html__('custom WordPress sites and WooCommerce stores', 'navigate-peptides'),
        esc_url(NAV_ARCANA_URL)
    );
});

/**
 * Login screen footer link.
 */
add_action('login_footer', function () {
    printf(
        '<p class="nav-arcana-login-credit" style="text-align:center;margin-top:24px;font-family:monospace;font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:#888;">
            %1$s <a href="%4$s" target="_blank" rel="noopener" style="color:#9C843E;text-decoration:none;">%2$s</a> · %3$s
        </p>',
        esc_html__('Designed by', 'navigate-peptides'),
        esc_html(NAV_ARCANA_NAME),
        esc_html__('arcanaoperations.com', 'navigate-peptides'),
        esc_url(NAV_ARCANA_URL)
    );
});
