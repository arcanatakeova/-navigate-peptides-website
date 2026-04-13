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

    // Research Category taxonomy
    register_taxonomy('research_category', 'nav_research', [
        'labels' => [
            'name'          => __('Research Categories', 'navigate-peptides'),
            'singular_name' => __('Research Category', 'navigate-peptides'),
        ],
        'public'       => true,
        'hierarchical' => true,
        'rewrite'      => ['slug' => 'research/category', 'with_front' => false],
        'show_in_rest' => true,
    ]);
});

/**
 * Insert default research categories on theme activation.
 */
add_action('after_switch_theme', function () {
    $defaults = [
        'Research Intelligence' => 'intelligence',
        'Research Library'      => 'library',
        'Research Framework'    => 'framework',
        'Emerging Research'     => 'emerging',
    ];

    foreach ($defaults as $name => $slug) {
        if (!term_exists($slug, 'research_category')) {
            wp_insert_term($name, 'research_category', ['slug' => $slug]);
        }
    }

    // Insert default WooCommerce product categories
    if (taxonomy_exists('product_cat')) {
        $categories = [
            'Metabolic Research'     => 'metabolic-research',
            'Tissue Repair Research' => 'tissue-repair-research',
            'Cognitive Research'     => 'cognitive-research',
            'Inflammation Research'  => 'inflammation-research',
            'Cellular Research'      => 'cellular-research',
            'Dermal Research'        => 'dermal-research',
            'Research Blends'        => 'research-blends',
        ];

        foreach ($categories as $name => $slug) {
            if (!term_exists($slug, 'product_cat')) {
                wp_insert_term($name, 'product_cat', ['slug' => $slug]);
            }
        }
    }
});

/**
 * Flush rewrite rules on theme activation.
 */
add_action('after_switch_theme', function () {
    flush_rewrite_rules();
});
