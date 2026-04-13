<?php
/**
 * Navigate Peptides Theme Functions
 *
 * @package NavigatePeptides
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

define('NAV_THEME_VERSION', '1.0.0');
define('NAV_THEME_DIR', get_template_directory());
define('NAV_THEME_URI', get_template_directory_uri());

/* ------------------------------------------------------------------
 * 1. Theme Setup
 * ----------------------------------------------------------------*/
add_action('after_setup_theme', function () {
    // Title tag support
    add_theme_support('title-tag');

    // Post thumbnails
    add_theme_support('post-thumbnails');
    add_image_size('product-card', 400, 400, true);
    add_image_size('product-hero', 800, 800, false);
    add_image_size('category-hero', 1200, 400, true);

    // HTML5 markup
    add_theme_support('html5', [
        'search-form', 'comment-form', 'comment-list',
        'gallery', 'caption', 'style', 'script',
    ]);

    // Custom logo
    add_theme_support('custom-logo', [
        'height'      => 40,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ]);

    // WooCommerce
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    // Menus
    register_nav_menus([
        'primary'     => __('Primary Navigation', 'navigate-peptides'),
        'footer-1'    => __('Footer Column 1', 'navigate-peptides'),
        'footer-2'    => __('Footer Column 2', 'navigate-peptides'),
        'footer-3'    => __('Footer Column 3', 'navigate-peptides'),
    ]);
});

/* ------------------------------------------------------------------
 * 2. Enqueue Assets
 * ----------------------------------------------------------------*/
add_action('wp_enqueue_scripts', function () {
    // Google Fonts
    wp_enqueue_style(
        'nav-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&family=Playfair+Display:wght@400;500;600;700&display=swap',
        [],
        null
    );

    // Main stylesheet
    wp_enqueue_style(
        'nav-main',
        NAV_THEME_URI . '/assets/css/main.css',
        ['nav-google-fonts'],
        NAV_THEME_VERSION
    );

    // WooCommerce overrides
    if (class_exists('WooCommerce')) {
        wp_enqueue_style(
            'nav-woocommerce',
            NAV_THEME_URI . '/assets/css/woocommerce.css',
            ['nav-main'],
            NAV_THEME_VERSION
        );
    }

    // JavaScript
    wp_enqueue_script(
        'nav-main',
        NAV_THEME_URI . '/assets/js/main.js',
        [],
        NAV_THEME_VERSION,
        true
    );

    // Dequeue default WooCommerce styles — we override everything
    if (class_exists('WooCommerce')) {
        wp_dequeue_style('woocommerce-general');
        wp_dequeue_style('woocommerce-layout');
        wp_dequeue_style('woocommerce-smallscreen');
    }
});

/* ------------------------------------------------------------------
 * 3. WooCommerce Configuration
 * ----------------------------------------------------------------*/
require_once NAV_THEME_DIR . '/inc/woocommerce.php';

/* ------------------------------------------------------------------
 * 4. Compliance & Disclaimers
 * ----------------------------------------------------------------*/
require_once NAV_THEME_DIR . '/inc/compliance.php';

/* ------------------------------------------------------------------
 * 5. SEO & Schema Markup
 * ----------------------------------------------------------------*/
require_once NAV_THEME_DIR . '/inc/seo.php';

/* ------------------------------------------------------------------
 * 6. Custom Post Types & Taxonomies
 * ----------------------------------------------------------------*/
require_once NAV_THEME_DIR . '/inc/custom-types.php';

/* ------------------------------------------------------------------
 * 7. Security Headers
 * ----------------------------------------------------------------*/
add_action('send_headers', function () {
    if (!is_admin()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header("Permissions-Policy: camera=(), microphone=(), geolocation=()");
        header("Content-Security-Policy: default-src 'self'; script-src 'self' https://ajax.googleapis.com https://fonts.googleapis.com 'unsafe-inline' 'unsafe-eval'; style-src 'self' https://fonts.googleapis.com 'unsafe-inline'; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self'; frame-ancestors 'self'; base-uri 'self'; form-action 'self' " . esc_url(admin_url()) . ";");
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
});

/* ------------------------------------------------------------------
 * 8. Admin Cleanup
 * ----------------------------------------------------------------*/
add_action('wp_head', function () {
    // Remove unnecessary meta
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
}, 1);

// Remove emoji scripts
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

/* ------------------------------------------------------------------
 * 9. Widget Areas
 * ----------------------------------------------------------------*/
add_action('widgets_init', function () {
    register_sidebar([
        'name'          => __('Footer Widget Area', 'navigate-peptides'),
        'id'            => 'footer-widgets',
        'before_widget' => '<div class="nav-footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="nav-footer-widget__title">',
        'after_title'   => '</h4>',
    ]);
});

/* ------------------------------------------------------------------
 * 10. Helper Functions
 * ----------------------------------------------------------------*/

/**
 * Get category color by slug.
 */
function nav_get_category_color(string $slug): string {
    $colors = [
        'metabolic-research'     => '#2F4666',
        'tissue-repair-research' => '#9C843E',
        'cognitive-research'     => '#5E507F',
        'inflammation-research'  => '#4A141C',
        'cellular-research'      => '#8E5660',
        'dermal-research'        => '#4A6B5F',
        'research-blends'        => '#474C50',
    ];
    return $colors[$slug] ?? '#474C50';
}

/**
 * Get category color from WooCommerce product category term.
 */
function nav_get_product_category_color($product = null): string {
    if (!$product) {
        global $product;
    }
    if (!$product) return '#474C50';

    $terms = get_the_terms($product->get_id(), 'product_cat');
    if (!$terms || is_wp_error($terms)) return '#474C50';

    foreach ($terms as $term) {
        $color = nav_get_category_color($term->slug);
        if ($color !== '#474C50') return $color;
    }
    return '#474C50';
}

/**
 * Render SVG icon.
 */
function nav_icon(string $name, string $class = ''): string {
    $icons = [
        'menu' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>',
        'close' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>',
        'cart' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>',
        'arrow-right' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>',
        'check' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>',
        'flask' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M5 14.5l-.94 2.06A2.25 2.25 0 0 0 6.107 20h11.786a2.25 2.25 0 0 0 2.047-3.44L19 14.5"/></svg>',
        'shield' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>',
        'building' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>',
    ];
    $svg = $icons[$name] ?? '';
    $cls = $class ? ' class="' . esc_attr($class) . '"' : '';
    return str_replace('<svg ', "<svg{$cls} ", $svg);
}

/* ------------------------------------------------------------------
 * 11. Contact Form Handler
 * ----------------------------------------------------------------*/
add_action('admin_post_nopriv_nav_contact_form', 'nav_handle_contact_form');
add_action('admin_post_nav_contact_form', 'nav_handle_contact_form');

function nav_handle_contact_form(): void {
    if (
        !isset($_POST['nav_nonce'])
        || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nav_nonce'])), 'nav_contact_nonce')
    ) {
        wp_die(
            esc_html__('Security check failed. Please go back and try again.', 'navigate-peptides'),
            esc_html__('Forbidden', 'navigate-peptides'),
            ['response' => 403]
        );
    }

    // Honeypot check — bots fill hidden fields
    if (!empty($_POST['nav_hp_field'])) {
        wp_safe_redirect(add_query_arg('contact', 'success', wp_get_referer() ?: home_url('/')));
        exit;
    }

    $first_name   = sanitize_text_field(wp_unslash($_POST['first_name'] ?? ''));
    $last_name    = sanitize_text_field(wp_unslash($_POST['last_name'] ?? ''));
    $email        = sanitize_email(wp_unslash($_POST['email'] ?? ''));
    $organization = sanitize_text_field(wp_unslash($_POST['organization'] ?? ''));
    $inquiry_type = sanitize_text_field(wp_unslash($_POST['inquiry_type'] ?? 'general'));
    $message      = sanitize_textarea_field(wp_unslash($_POST['message'] ?? ''));

    if (!$first_name || !$last_name || !$email || !$message) {
        wp_safe_redirect(add_query_arg('contact', 'missing', wp_get_referer() ?: home_url('/')));
        exit;
    }

    if (!is_email($email)) {
        wp_safe_redirect(add_query_arg('contact', 'invalid', wp_get_referer() ?: home_url('/')));
        exit;
    }

    $admin_email = get_option('admin_email');
    $subject     = sprintf('[Navigate Peptides] %s inquiry from %s %s', ucfirst($inquiry_type), $first_name, $last_name);
    $body        = sprintf(
        "Name: %s %s\nEmail: %s\nOrganization: %s\nInquiry Type: %s\n\nMessage:\n%s",
        $first_name, $last_name, $email, $organization, $inquiry_type, $message
    );
    $headers     = ['Reply-To: ' . $email];

    wp_mail($admin_email, $subject, $body, $headers);

    wp_safe_redirect(add_query_arg('contact', 'success', wp_get_referer() ?: home_url('/')));
    exit;
}

/* ------------------------------------------------------------------
 * 12. Excerpt Length
 * ----------------------------------------------------------------*/
add_filter('excerpt_length', fn() => 20);
add_filter('excerpt_more', fn() => '&hellip;');
