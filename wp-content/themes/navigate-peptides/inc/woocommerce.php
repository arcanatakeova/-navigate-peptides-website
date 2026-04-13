<?php
/**
 * WooCommerce Integration
 *
 * @package NavigatePeptides
 */

defined('ABSPATH') || exit;

/* ------------------------------------------------------------------
 * Remove default WooCommerce wrappers, use our own
 * ----------------------------------------------------------------*/
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

add_action('woocommerce_before_main_content', function () {
    echo '<div class="nav-woo-wrap"><div class="nav-container">';
}, 10);

add_action('woocommerce_after_main_content', function () {
    echo '</div></div>';
}, 10);

/* ------------------------------------------------------------------
 * Remove sidebar from WooCommerce pages
 * ----------------------------------------------------------------*/
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

/* ------------------------------------------------------------------
 * Products per page
 * ----------------------------------------------------------------*/
add_filter('loop_shop_per_page', fn() => 24);

/* ------------------------------------------------------------------
 * Products per row
 * ----------------------------------------------------------------*/
add_filter('loop_shop_columns', fn() => 3);

/* ------------------------------------------------------------------
 * Related products limit
 * ----------------------------------------------------------------*/
add_filter('woocommerce_output_related_products_args', function ($args) {
    $args['posts_per_page'] = 3;
    $args['columns'] = 3;
    return $args;
});

/* ------------------------------------------------------------------
 * Remove default product meta (categories/tags below add to cart)
 * We show these in our own template.
 * ----------------------------------------------------------------*/
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

/* ------------------------------------------------------------------
 * Custom product tabs
 * ----------------------------------------------------------------*/
add_filter('woocommerce_product_tabs', function ($tabs) {
    // Remove default tabs
    unset($tabs['reviews']);

    // Rename description tab
    if (isset($tabs['description'])) {
        $tabs['description']['title'] = __('Research Context', 'navigate-peptides');
        $tabs['description']['priority'] = 10;
    }

    // Add Research Focus tab
    $tabs['research_focus'] = [
        'title'    => __('Research Focus', 'navigate-peptides'),
        'priority' => 20,
        'callback' => 'nav_research_focus_tab',
    ];

    // Add COA tab
    $tabs['coa'] = [
        'title'    => __('Certificate of Analysis', 'navigate-peptides'),
        'priority' => 30,
        'callback' => 'nav_coa_tab',
    ];

    return $tabs;
});

function nav_research_focus_tab(): void {
    global $product;
    $focus = get_post_meta($product->get_id(), '_nav_research_focus', true);
    if ($focus) {
        echo '<div class="nav-research-focus">';
        echo '<h2>' . esc_html__('Research Focus', 'navigate-peptides') . '</h2>';
        echo '<ul class="nav-research-focus__list">';
        $items = explode("\n", $focus);
        foreach ($items as $item) {
            $item = trim($item);
            if ($item) {
                echo '<li>' . esc_html($item) . '</li>';
            }
        }
        echo '</ul>';
        echo '</div>';
    } else {
        echo '<p class="nav-text-muted">' . esc_html__('Research focus data is being prepared for this compound.', 'navigate-peptides') . '</p>';
    }
}

function nav_coa_tab(): void {
    global $product;
    $coa_url = get_post_meta($product->get_id(), '_nav_coa_pdf', true);
    $batch   = get_post_meta($product->get_id(), '_nav_batch_number', true);
    $lab     = get_post_meta($product->get_id(), '_nav_testing_lab', true);
    $purity  = get_post_meta($product->get_id(), '_nav_purity', true);

    echo '<div class="nav-coa-tab">';
    echo '<h2>' . esc_html__('Certificate of Analysis', 'navigate-peptides') . '</h2>';

    if ($batch || $lab || $purity) {
        echo '<div class="nav-coa-tab__specs">';
        if ($batch) echo '<div class="nav-coa-tab__row"><span class="nav-coa-tab__label">Batch</span><span class="nav-coa-tab__value">' . esc_html($batch) . '</span></div>';
        if ($lab) echo '<div class="nav-coa-tab__row"><span class="nav-coa-tab__label">Testing Lab</span><span class="nav-coa-tab__value">' . esc_html($lab) . '</span></div>';
        if ($purity) echo '<div class="nav-coa-tab__row"><span class="nav-coa-tab__label">Purity</span><span class="nav-coa-tab__value">' . esc_html($purity) . '</span></div>';
        echo '</div>';
    }

    if ($coa_url) {
        echo '<a href="' . esc_url($coa_url) . '" class="nav-btn nav-btn--outline" target="_blank" rel="noopener">';
        echo esc_html__('Download COA (PDF)', 'navigate-peptides');
        echo '</a>';
    } else {
        echo '<p class="nav-text-muted">' . esc_html__('COA will be available once this batch has completed third-party testing.', 'navigate-peptides') . '</p>';
    }

    echo '</div>';
}

/* ------------------------------------------------------------------
 * Custom product meta fields (Admin)
 * ----------------------------------------------------------------*/
add_action('woocommerce_product_options_general_product_data', function () {
    echo '<div class="options_group">';
    echo '<h4 style="padding-left:12px;margin-top:12px;">' . esc_html__('Navigate Peptides — Compound Data', 'navigate-peptides') . '</h4>';

    woocommerce_wp_text_input([
        'id'          => '_nav_technical_subtitle',
        'label'       => __('Technical Subtitle', 'navigate-peptides'),
        'placeholder' => 'e.g. Synthetic Pentadecapeptide — BPC Fragment 15',
    ]);

    woocommerce_wp_text_input([
        'id'    => '_nav_cas_number',
        'label' => __('CAS Number', 'navigate-peptides'),
    ]);

    woocommerce_wp_text_input([
        'id'    => '_nav_molecular_weight',
        'label' => __('Molecular Weight', 'navigate-peptides'),
    ]);

    woocommerce_wp_text_input([
        'id'    => '_nav_sequence',
        'label' => __('Amino Acid Sequence', 'navigate-peptides'),
    ]);

    woocommerce_wp_text_input([
        'id'          => '_nav_purity',
        'label'       => __('Purity', 'navigate-peptides'),
        'placeholder' => '≥99% (Third-party HPLC verified)',
    ]);

    woocommerce_wp_text_input([
        'id'    => '_nav_form',
        'label' => __('Form', 'navigate-peptides'),
        'placeholder' => 'Lyophilized powder',
    ]);

    woocommerce_wp_text_input([
        'id'    => '_nav_storage',
        'label' => __('Storage', 'navigate-peptides'),
        'placeholder' => '-20°C. Protect from light and moisture.',
    ]);

    woocommerce_wp_textarea_input([
        'id'          => '_nav_research_focus',
        'label'       => __('Research Focus (one per line)', 'navigate-peptides'),
        'placeholder' => "VEGF pathway upregulation mechanisms\nNitric oxide system modulation",
    ]);

    woocommerce_wp_text_input([
        'id'    => '_nav_batch_number',
        'label' => __('Batch Number', 'navigate-peptides'),
    ]);

    woocommerce_wp_text_input([
        'id'    => '_nav_testing_lab',
        'label' => __('Testing Lab', 'navigate-peptides'),
    ]);

    woocommerce_wp_text_input([
        'id'          => '_nav_coa_pdf',
        'label'       => __('COA PDF URL', 'navigate-peptides'),
        'type'        => 'url',
        'placeholder' => 'https://...',
    ]);

    woocommerce_wp_text_input([
        'id'          => '_nav_3d_model_url',
        'label'       => __('3D Model URL (.glb)', 'navigate-peptides'),
        'type'        => 'url',
        'placeholder' => 'https://example.com/models/vial.glb',
        'description' => __('Interactive 3D vial model. Leave blank to use product image.', 'navigate-peptides'),
        'desc_tip'    => true,
    ]);

    echo '</div>';
});

add_action('woocommerce_process_product_meta', function ($post_id) {
    $fields = [
        '_nav_technical_subtitle',
        '_nav_cas_number',
        '_nav_molecular_weight',
        '_nav_sequence',
        '_nav_purity',
        '_nav_form',
        '_nav_storage',
        '_nav_research_focus',
        '_nav_batch_number',
        '_nav_testing_lab',
        '_nav_coa_pdf',
        '_nav_3d_model_url',
    ];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta(
                $post_id,
                $field,
                $field === '_nav_coa_pdf'
                    ? esc_url_raw(wp_unslash($_POST[$field]))
                    : sanitize_textarea_field(wp_unslash($_POST[$field]))
            );
        }
    }
});

/* ------------------------------------------------------------------
 * Add RUO disclaimer after Add to Cart button
 * ----------------------------------------------------------------*/
add_action('woocommerce_after_add_to_cart_form', function () {
    echo '<div class="nav-product-disclaimer">';
    echo '<p>' . esc_html(nav_get_disclaimer('product')) . '</p>';
    echo '</div>';
});

/* ------------------------------------------------------------------
 * Checkout: RUO acknowledgment checkbox
 * ----------------------------------------------------------------*/
add_action('woocommerce_review_order_before_submit', function () {
    woocommerce_form_field('nav_ruo_acknowledgment', [
        'type'     => 'checkbox',
        'class'    => ['nav-ruo-checkbox'],
        'label'    => __('I acknowledge that all products purchased are intended for research and identification purposes only. These products are not intended for human dosing, injection, or ingestion.', 'navigate-peptides'),
        'required' => true,
    ]);
});

add_action('woocommerce_checkout_process', function () {
    if (empty($_POST['nav_ruo_acknowledgment'])) {
        wc_add_notice(
            __('You must acknowledge the research-use-only agreement before completing your order.', 'navigate-peptides'),
            'error'
        );
    }
});

/* ------------------------------------------------------------------
 * Cart/Checkout: Add sitewide disclaimer
 * ----------------------------------------------------------------*/
add_action('woocommerce_after_cart_table', function () {
    echo '<div class="nav-cart-disclaimer">';
    echo '<p>' . esc_html(nav_get_disclaimer('sitewide')) . '</p>';
    echo '</div>';
});

/* ------------------------------------------------------------------
 * Modify "Add to Cart" button text
 * ----------------------------------------------------------------*/
add_filter('woocommerce_product_single_add_to_cart_text', fn() => __('Add to Cart', 'navigate-peptides'));
add_filter('woocommerce_product_add_to_cart_text', fn() => __('Add to Cart', 'navigate-peptides'));

/* ------------------------------------------------------------------
 * Breadcrumb customization
 * ----------------------------------------------------------------*/
add_filter('woocommerce_breadcrumb_defaults', function ($defaults) {
    $defaults['delimiter']   = '<span class="nav-breadcrumb__sep">/</span>';
    $defaults['wrap_before'] = '<nav class="nav-breadcrumb" aria-label="Breadcrumb"><div class="nav-container">';
    $defaults['wrap_after']  = '</div></nav>';
    return $defaults;
});
