<?php
/**
 * SEO & Schema Markup
 *
 * @package NavigatePeptides
 */

defined('ABSPATH') || exit;

/**
 * Organization schema (sitewide).
 */
add_action('wp_head', function () {
    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Organization',
        'name'        => 'Navigate Peptides',
        'url'         => home_url('/'),
        'description' => 'Premium research peptide compounds for scientific investigation. All products are intended for research and identification purposes only.',
        'sameAs'      => [],
    ];

    if (has_custom_logo()) {
        $logo_id  = get_theme_mod('custom_logo');
        $logo_url = wp_get_attachment_image_url($logo_id, 'full');
        if ($logo_url) {
            $schema['logo'] = $logo_url;
        }
    }

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}, 5);

/**
 * BreadcrumbList schema for WooCommerce products.
 */
add_action('wp_head', function () {
    if (!is_singular('product')) return;

    global $product;
    if (!$product) return;

    $terms = get_the_terms($product->get_id(), 'product_cat');
    $cat   = $terms && !is_wp_error($terms) ? $terms[0] : null;

    $items = [
        [
            '@type'    => 'ListItem',
            'position' => 1,
            'name'     => 'Compounds',
            'item'     => home_url('/compounds/'),
        ],
    ];

    if ($cat) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => 2,
            'name'     => $cat->name,
            'item'     => get_term_link($cat),
        ];
        $items[] = [
            '@type'    => 'ListItem',
            'position' => 3,
            'name'     => get_the_title(),
        ];
    } else {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => 2,
            'name'     => get_the_title(),
        ];
    }

    $schema = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $items,
    ];

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
});

/**
 * Product schema enhancement — add research-safe structured data.
 */
add_filter('woocommerce_structured_data_product', function ($markup) {
    // Ensure description is compliant (no health claims)
    if (isset($markup['description'])) {
        $markup['description'] = wp_strip_all_tags($markup['description']);
    }
    // Add category
    $markup['category'] = 'Research Peptide Compound';
    return $markup;
});

/**
 * Meta description for pages.
 */
add_action('wp_head', function () {
    if (is_singular() && !is_front_page()) {
        global $post;
        $desc = get_the_excerpt($post);
        if ($desc) {
            echo '<meta name="description" content="' . esc_attr(wp_trim_words($desc, 25)) . '">' . "\n";
        }
    }
    // Open Graph basics
    if (is_singular()) {
        echo '<meta property="og:title" content="' . esc_attr(get_the_title()) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '">' . "\n";
        echo '<meta property="og:type" content="' . (is_singular('product') ? 'product' : 'website') . '">' . "\n";
        echo '<meta property="og:site_name" content="Navigate Peptides">' . "\n";
        if (has_post_thumbnail()) {
            echo '<meta property="og:image" content="' . esc_url(get_the_post_thumbnail_url(null, 'large')) . '">' . "\n";
        }
    }
});

/**
 * Canonical URLs.
 */
add_action('wp_head', function () {
    if (is_singular()) {
        echo '<link rel="canonical" href="' . esc_url(get_permalink()) . '">' . "\n";
    }
});
