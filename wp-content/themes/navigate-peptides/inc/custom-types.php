<?php
/**
 * Custom Post Types & Taxonomies
 *
 * @package NavigatePeptides
 */

defined('ABSPATH') || exit;

/**
 * Register Research Articles CPT.
 */
add_action('init', function () {
    register_post_type('nav_research', [
        'labels' => [
            'name'               => __('Research Articles', 'navigate-peptides'),
            'singular_name'      => __('Research Article', 'navigate-peptides'),
            'add_new_item'       => __('Add New Research Article', 'navigate-peptides'),
            'edit_item'          => __('Edit Research Article', 'navigate-peptides'),
            'view_item'          => __('View Research Article', 'navigate-peptides'),
            'search_items'       => __('Search Research Articles', 'navigate-peptides'),
            'not_found'          => __('No research articles found.', 'navigate-peptides'),
            'not_found_in_trash' => __('No research articles found in Trash.', 'navigate-peptides'),
        ],
        'public'        => true,
        'has_archive'   => true,
        'rewrite'       => ['slug' => 'research', 'with_front' => false],
        'menu_icon'     => 'dashicons-microscope',
        'supports'      => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions'],
        'show_in_rest'  => true,
    ]);

    // Research Topic taxonomy — slug matches docs/SITE_ARCHITECTURE:
    //   /research/topic/intelligence/  /research/topic/library/  etc.
    // Previously /research/category/ — renamed so nothing reads as a product
    // category (that word is reserved for product_cat).
    register_taxonomy('research_category', 'nav_research', [
        'labels' => [
            'name'          => __('Research Topics', 'navigate-peptides'),
            'singular_name' => __('Research Topic', 'navigate-peptides'),
        ],
        'public'       => true,
        'hierarchical' => true,
        'rewrite'      => ['slug' => 'research/topic', 'with_front' => false],
        'show_in_rest' => true,
    ]);
});

/**
 * Backfill default terms for both research and product taxonomies.
 * Runs on theme activation AND on woocommerce_init so that if Woo is
 * activated after the theme (common during onboarding), the product
 * categories still get seeded.
 */
function nav_backfill_default_terms(): void {
    // Research topics — theme-native taxonomy, always present.
    $research_defaults = [
        'Research Intelligence' => 'intelligence',
        'Research Library'      => 'library',
        'Research Framework'    => 'framework',
        'Emerging Research'     => 'emerging',
    ];
    foreach ($research_defaults as $name => $slug) {
        if (!term_exists($slug, 'research_category')) {
            wp_insert_term($name, 'research_category', ['slug' => $slug]);
        }
    }

    // Product categories — only when WooCommerce is active.
    if (taxonomy_exists('product_cat')) {
        $product_defaults = [
            'Metabolic Research'     => 'metabolic-research',
            'Tissue Repair Research' => 'tissue-repair-research',
            'Cognitive Research'     => 'cognitive-research',
            'Inflammation Research'  => 'inflammation-research',
            'Cellular Research'      => 'cellular-research',
            'Dermal Research'        => 'dermal-research',
            'Research Blends'        => 'research-blends',
        ];
        foreach ($product_defaults as $name => $slug) {
            if (!term_exists($slug, 'product_cat')) {
                wp_insert_term($name, 'product_cat', ['slug' => $slug]);
            }
        }
    }
}

add_action('after_switch_theme', 'nav_backfill_default_terms');
// If WC activates after the theme, fire once more so product categories
// exist. Hook on init:20 — product_cat registers at init:8 in WC core;
// woocommerce_init is too early so taxonomy_exists() would return false.
// Also: only flip the seeded flag when we actually seeded, so a race
// doesn't permanently prevent retry.
add_action('init', function () {
    if (get_option('nav_product_terms_seeded')) return;
    if (!taxonomy_exists('product_cat')) return;
    nav_backfill_default_terms();
    update_option('nav_product_terms_seeded', 1, false);
}, 20);

/**
 * Flush rewrite rules on theme activation.
 */
add_action('after_switch_theme', function () {
    flush_rewrite_rules();
});
