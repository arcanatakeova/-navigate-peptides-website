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
    // Remove ALL default tabs — replace with Stephie's 3-tab structure
    unset($tabs['reviews']);
    unset($tabs['additional_information']);

    // Tab 1: Research Overview (replaces Description)
    if (isset($tabs['description'])) {
        $tabs['description']['title']    = __('Research Overview', 'navigate-peptides');
        $tabs['description']['priority'] = 10;
        $tabs['description']['callback'] = 'nav_research_overview_tab';
    } else {
        $tabs['research_overview'] = [
            'title'    => __('Research Overview', 'navigate-peptides'),
            'priority' => 10,
            'callback' => 'nav_research_overview_tab',
        ];
    }

    // Tab 2: Technical Specifications
    $tabs['technical_specs'] = [
        'title'    => __('Technical Specifications', 'navigate-peptides'),
        'priority' => 20,
        'callback' => 'nav_technical_specs_tab',
    ];

    // Tab 3: Batch Verification
    $tabs['batch_verification'] = [
        'title'    => __('Batch Verification', 'navigate-peptides'),
        'priority' => 30,
        'callback' => 'nav_batch_verification_tab',
    ];

    return $tabs;
});

/**
 * Tab 1: Research Overview
 * Compound description + research focus bullet points.
 */
function nav_research_overview_tab(): void {
    global $product;

    echo '<div class="nav-tab-content">';
    echo '<h2>' . esc_html__('Research Overview', 'navigate-peptides') . '</h2>';

    // Product description
    $desc = $product->get_description();
    if ($desc) {
        echo '<div class="nav-tab-content__text">' . wp_kses_post(wpautop($desc)) . '</div>';
    }

    echo '<p class="nav-tab-content__ruo">';
    echo esc_html__('This compound is classified within structured research frameworks and is supplied for controlled laboratory environments.', 'navigate-peptides');
    echo '</p>';

    // Research focus
    $focus = get_post_meta($product->get_id(), '_nav_research_focus', true);
    if ($focus) {
        echo '<h3>' . esc_html__('Research Focus', 'navigate-peptides') . '</h3>';
        echo '<ul class="nav-tab-content__list">';
        $items = array_filter(array_map('trim', explode("\n", $focus)));
        foreach ($items as $item) {
            echo '<li>' . esc_html($item) . '</li>';
        }
        echo '</ul>';
    }

    echo '</div>';
}

/**
 * Tab 2: Technical Specifications
 * CAS, MW, sequence, form, storage, purity — in a structured grid.
 */
function nav_technical_specs_tab(): void {
    global $product;

    $specs = [
        'Molecular Structure' => get_post_meta($product->get_id(), '_nav_sequence', true),
        'Molecular Weight'    => get_post_meta($product->get_id(), '_nav_molecular_weight', true),
        'CAS Number'          => get_post_meta($product->get_id(), '_nav_cas_number', true),
        'Form'                => get_post_meta($product->get_id(), '_nav_form', true),
        'Purity'              => get_post_meta($product->get_id(), '_nav_purity', true),
        'Storage'             => get_post_meta($product->get_id(), '_nav_storage', true),
    ];

    echo '<div class="nav-tab-content">';
    echo '<h2>' . esc_html__('Technical Specifications', 'navigate-peptides') . '</h2>';
    echo '<div class="nav-tab-specs">';

    foreach ($specs as $label => $value) {
        if (!$value) $value = __('Available', 'navigate-peptides');
        echo '<div class="nav-tab-specs__row">';
        echo '<span class="nav-tab-specs__label">' . esc_html($label) . '</span>';
        echo '<span class="nav-tab-specs__value">' . esc_html($value) . '</span>';
        echo '</div>';
    }

    echo '</div>';

    // Additional notes
    echo '<div class="nav-tab-specs__notes">';
    echo '<div class="nav-tab-specs__note"><span>Analytical Data</span><span>' . esc_html__('Available upon request', 'navigate-peptides') . '</span></div>';
    echo '<div class="nav-tab-specs__note"><span>Stability Profile</span><span>' . esc_html__('Controlled conditions required', 'navigate-peptides') . '</span></div>';
    echo '<div class="nav-tab-specs__note"><span>Reconstitution</span><span>' . esc_html__('Laboratory handling required', 'navigate-peptides') . '</span></div>';
    echo '</div>';
    echo '</div>';
}

/**
 * Tab 3: Batch Verification
 * COA data, batch number, testing lab, download link.
 */
function nav_batch_verification_tab(): void {
    global $product;

    $coa_url = get_post_meta($product->get_id(), '_nav_coa_pdf', true);
    $batch   = get_post_meta($product->get_id(), '_nav_batch_number', true);
    $lab     = get_post_meta($product->get_id(), '_nav_testing_lab', true);
    $purity  = get_post_meta($product->get_id(), '_nav_purity', true);

    echo '<div class="nav-tab-content">';
    echo '<h2>' . esc_html__('Batch Verification', 'navigate-peptides') . '</h2>';

    // COA Download Button
    if ($coa_url) {
        echo '<a href="' . esc_url($coa_url) . '" class="nav-tab-coa-btn" target="_blank" rel="noopener">';
        echo esc_html__('View Certificate of Analysis', 'navigate-peptides');
        echo ' <span>→</span>';
        echo '</a>';
    }

    // Batch details
    if ($batch || $lab || $purity) {
        echo '<div class="nav-tab-specs" style="margin-top:24px;">';
        if ($batch) echo '<div class="nav-tab-specs__row"><span class="nav-tab-specs__label">Batch Number</span><span class="nav-tab-specs__value">' . esc_html($batch) . '</span></div>';
        if ($purity) echo '<div class="nav-tab-specs__row"><span class="nav-tab-specs__label">Verified Purity</span><span class="nav-tab-specs__value">' . esc_html($purity) . '</span></div>';
        if ($lab) echo '<div class="nav-tab-specs__row"><span class="nav-tab-specs__label">Testing Laboratory</span><span class="nav-tab-specs__value">' . esc_html($lab) . '</span></div>';
        echo '<div class="nav-tab-specs__row"><span class="nav-tab-specs__label">Methods</span><span class="nav-tab-specs__value">HPLC, Mass Spectrometry</span></div>';
        echo '</div>';
    } else {
        echo '<p class="nav-text-muted">' . esc_html__('Batch verification data will be available once third-party testing is complete.', 'navigate-peptides') . '</p>';
    }

    // Additional links matching mockup
    echo '<div class="nav-tab-links">';
    echo '<a href="' . esc_url(home_url('/quality/testing/')) . '" class="nav-tab-link">Research Classification <span>→</span></a>';
    echo '<a href="' . esc_url(home_url('/quality/handling/')) . '" class="nav-tab-link">Handling &amp; Storage <span>→</span></a>';
    echo '</div>';

    // Disclaimer
    echo '<div class="nav-tab-disclaimer">';
    echo '<p>' . esc_html__('This product is supplied for research purposes only.', 'navigate-peptides') . '</p>';
    echo '<p>' . esc_html__('Not for human or veterinary use.', 'navigate-peptides') . '</p>';
    echo '</div>';

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
    // Nonce verification is handled by WooCommerce core checkout form processing.
    // phpcs:ignore WordPress.Security.NonceVerification.Missing
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
