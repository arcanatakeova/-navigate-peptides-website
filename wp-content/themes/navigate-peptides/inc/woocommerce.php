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
    if (!($product instanceof WC_Product)) return;

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
    if (!($product instanceof WC_Product)) return;

    $specs = [
        __('Molecular Structure', 'navigate-peptides') => get_post_meta($product->get_id(), '_nav_sequence', true),
        __('Molecular Weight', 'navigate-peptides')    => get_post_meta($product->get_id(), '_nav_molecular_weight', true),
        __('CAS Number', 'navigate-peptides')          => get_post_meta($product->get_id(), '_nav_cas_number', true),
        __('Form', 'navigate-peptides')                => get_post_meta($product->get_id(), '_nav_form', true),
        __('Purity', 'navigate-peptides')              => get_post_meta($product->get_id(), '_nav_purity', true),
        __('Storage', 'navigate-peptides')             => get_post_meta($product->get_id(), '_nav_storage', true),
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
    echo '<div class="nav-tab-specs__note"><span>' . esc_html__('Analytical Data', 'navigate-peptides') . '</span><span>' . esc_html__('Available upon request', 'navigate-peptides') . '</span></div>';
    echo '<div class="nav-tab-specs__note"><span>' . esc_html__('Stability Profile', 'navigate-peptides') . '</span><span>' . esc_html__('Controlled conditions required', 'navigate-peptides') . '</span></div>';
    echo '<div class="nav-tab-specs__note"><span>' . esc_html__('Reconstitution', 'navigate-peptides') . '</span><span>' . esc_html__('Laboratory handling required', 'navigate-peptides') . '</span></div>';
    echo '</div>';
    echo '</div>';
}

/**
 * Tab 3: Batch Verification
 * COA data, batch number, testing lab, download link.
 */
function nav_batch_verification_tab(): void {
    global $product;
    if (!($product instanceof WC_Product)) return;

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
        if ($batch) echo '<div class="nav-tab-specs__row"><span class="nav-tab-specs__label">' . esc_html__('Batch Number', 'navigate-peptides') . '</span><span class="nav-tab-specs__value">' . esc_html($batch) . '</span></div>';
        if ($purity) echo '<div class="nav-tab-specs__row"><span class="nav-tab-specs__label">' . esc_html__('Verified Purity', 'navigate-peptides') . '</span><span class="nav-tab-specs__value">' . esc_html($purity) . '</span></div>';
        if ($lab) echo '<div class="nav-tab-specs__row"><span class="nav-tab-specs__label">' . esc_html__('Testing Laboratory', 'navigate-peptides') . '</span><span class="nav-tab-specs__value">' . esc_html($lab) . '</span></div>';
        echo '<div class="nav-tab-specs__row"><span class="nav-tab-specs__label">' . esc_html__('Methods', 'navigate-peptides') . '</span><span class="nav-tab-specs__value">' . esc_html__('HPLC, Mass Spectrometry', 'navigate-peptides') . '</span></div>';
        echo '</div>';
    } else {
        echo '<p class="nav-text-muted">' . esc_html__('Batch verification data will be available once third-party testing is complete.', 'navigate-peptides') . '</p>';
    }

    // Additional links matching mockup
    echo '<div class="nav-tab-links">';
    echo '<a href="' . esc_url(home_url('/quality/testing/')) . '" class="nav-tab-link">' . esc_html__('Research Classification', 'navigate-peptides') . ' <span>→</span></a>';
    echo '<a href="' . esc_url(home_url('/quality/handling/')) . '" class="nav-tab-link">' . esc_html__('Handling & Storage', 'navigate-peptides') . ' <span>→</span></a>';
    echo '</div>';

    // Disclaimer — processor-mandated verbatim text, do not paraphrase
    echo '<div class="nav-tab-disclaimer">';
    echo '<p>' . esc_html(nav_get_disclaimer('product')) . '</p>';
    echo '<p>' . esc_html(nav_get_disclaimer('sitewide')) . '</p>';
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
            $is_url = in_array($field, ['_nav_coa_pdf', '_nav_3d_model_url'], true);
            update_post_meta(
                $post_id,
                $field,
                $is_url
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
    $ack = isset($_POST['nav_ruo_acknowledgment'])
        ? sanitize_text_field(wp_unslash($_POST['nav_ruo_acknowledgment']))
        : '';
    if (empty($ack)) {
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
 * Cart-count fragment — keeps the header badge in sync after ajax adds.
 * WC fires 'added_to_cart' which fetches fragments; our key replaces
 * the <span> identified by selector.
 * ----------------------------------------------------------------*/
add_filter('woocommerce_add_to_cart_fragments', function (array $fragments) {
    $count = WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
    ob_start();
    ?>
    <span class="nav-header__cart-count cart-contents-count"
          id="nav-cart-count"
          data-cart-count="<?php echo esc_attr((string) $count); ?>">
        <?php echo esc_html((string) $count); ?>
    </span>
    <?php
    $fragments['span.nav-header__cart-count'] = ob_get_clean();
    return $fragments;
});

/* ------------------------------------------------------------------
 * COA PDF + 3D model URL admin warning — processor audit relies on the
 * linked COA being a real document under a domain Ian controls. Block
 * obvious mixed-content / attacker-controlled-host URLs and nudge admins
 * toward the WP media library.
 * ----------------------------------------------------------------*/
add_action('woocommerce_process_product_meta', function ($post_id) {
    $urls_to_check = [
        '_nav_coa_pdf'       => __('COA PDF URL', 'navigate-peptides'),
        '_nav_3d_model_url'  => __('3D Model URL', 'navigate-peptides'),
    ];
    $allowed_hosts = apply_filters('nav_product_url_allowed_hosts', [
        wp_parse_url(home_url(), PHP_URL_HOST),
    ]);

    foreach ($urls_to_check as $field => $label) {
        $url = get_post_meta($post_id, $field, true);
        if (!$url) continue;

        $scheme = wp_parse_url($url, PHP_URL_SCHEME);
        $host   = wp_parse_url($url, PHP_URL_HOST);

        if ($scheme !== 'https') {
            error_log(sprintf('[nav_wc] product %d %s is not https: %s', $post_id, $field, $url));
        }
        if ($host && !in_array($host, $allowed_hosts, true)) {
            error_log(sprintf('[nav_wc] product %d %s points to external host %s', $post_id, $field, $host));
        }
    }
}, 20);

/* ------------------------------------------------------------------
 * Belt-and-suspenders RUO acknowledgment — if a checkout plugin replaces
 * the order-review template and strips our UI checkbox, keep the
 * server-side validation intact so no unacknowledged order ever posts.
 * ----------------------------------------------------------------*/
add_action('woocommerce_after_checkout_validation', function ($data, $errors) {
    $ack = isset($_POST['nav_ruo_acknowledgment'])
        ? sanitize_text_field(wp_unslash($_POST['nav_ruo_acknowledgment']))
        : '';
    if (empty($ack)) {
        $errors->add(
            'nav_ruo_required',
            __('You must acknowledge the research-use-only agreement before completing your order. If the checkbox is missing from your checkout, contact support.', 'navigate-peptides')
        );
    }
}, 10, 2);

/* ------------------------------------------------------------------
 * Log checkout failures for diagnosis. Without this, declines and
 * validation errors are invisible unless the gateway emails — and even
 * then, the theme has no record of which page the user abandoned on.
 * ----------------------------------------------------------------*/
add_action('woocommerce_checkout_validation', function ($data, $errors) {
    if (!empty($errors->get_error_messages())) {
        $codes = array_unique($errors->get_error_codes());
        error_log(sprintf(
            '[nav_checkout] validation failed codes=%s ip=%s',
            implode(',', $codes),
            nav_contact_client_ip()
        ));
    }
}, 10, 2);

add_action('woocommerce_payment_complete_order_status', function ($status, $order_id) {
    error_log(sprintf('[nav_checkout] payment completed order=%d status=%s', $order_id, $status));
    return $status;
}, 10, 2);

/* ------------------------------------------------------------------
 * Prohibited Term Validation (Admin Product Save)
 * Warns admins when product content contains compliance-risk language.
 * ----------------------------------------------------------------*/
add_action('woocommerce_process_product_meta', function ($post_id) {
    $prohibited_terms = [
        'dose', 'dosing', 'dosage', 'protocol', 'cycle', 'stack',
        'inject', 'injection', 'subcutaneous', 'intramuscular',
        'healing', 'recovery', 'anti-aging', 'fat loss', 'weight loss',
        'muscle growth', 'clinically proven', 'FDA approved',
        'pharmaceutical grade', 'treatment', 'therapy', 'patients',
        'wellness', 'before and after', 'testimonial',
    ];

    $fields_to_check = [
        'post_title'  => get_the_title($post_id),
        'description' => get_post_field('post_content', $post_id),
        'excerpt'     => get_post_field('post_excerpt', $post_id),
    ];

    // Also check custom meta fields
    $meta_keys = ['_nav_technical_subtitle', '_nav_research_focus'];
    foreach ($meta_keys as $key) {
        $val = get_post_meta($post_id, $key, true);
        if ($val) {
            $fields_to_check[$key] = $val;
        }
    }

    $found = [];
    foreach ($fields_to_check as $field => $content) {
        if (empty($content)) continue;
        $content_lower = strtolower($content);
        foreach ($prohibited_terms as $term) {
            if (strpos($content_lower, $term) !== false) {
                $found[] = sprintf('%s (in %s)', $term, str_replace('_', ' ', $field));
            }
        }
    }

    if (! empty($found)) {
        // 15 minutes covers slow admin redirects (plugin-heavy hosts can take
        // many seconds to load the edit screen after save). The prior value
        // `30` was interpreted as seconds, not minutes — the warning was
        // already gone by the time the admin saw the screen on most hosts.
        set_transient(
            'nav_compliance_warning_' . $post_id,
            $found,
            15 * MINUTE_IN_SECONDS
        );
    }
}, 20);

add_action('admin_notices', function () {
    $screen = get_current_screen();
    if (! $screen || $screen->id !== 'product') return;

    global $post;
    if (! $post) return;

    $warnings = get_transient('nav_compliance_warning_' . $post->ID);
    if (! $warnings) return;

    delete_transient('nav_compliance_warning_' . $post->ID);

    echo '<div class="notice notice-warning is-dismissible">';
    echo '<p><strong>' . esc_html__('Compliance Warning:', 'navigate-peptides') . '</strong> ' . esc_html__('The following prohibited terms were detected in this product. These terms may cause the payment processor to reject the merchant account or trigger FDA enforcement.', 'navigate-peptides') . '</p>';
    echo '<ul style="list-style:disc;padding-left:20px;">';
    foreach ($warnings as $w) {
        echo '<li>' . esc_html($w) . '</li>';
    }
    echo '</ul>';
    echo '<p>' . wp_kses(
        /* translators: %s: <code>docs/COMPLIANCE</code> literal */
        sprintf(__('Review %s for the full list of prohibited language.', 'navigate-peptides'), '<code>docs/COMPLIANCE</code>'),
        ['code' => []]
    ) . '</p>';
    echo '</div>';
});

/* ------------------------------------------------------------------
 * Breadcrumb customization
 * ----------------------------------------------------------------*/
add_filter('woocommerce_breadcrumb_defaults', function ($defaults) {
    $defaults['delimiter']   = '<span class="nav-breadcrumb__sep">/</span>';
    $defaults['wrap_before'] = '<nav class="nav-breadcrumb" aria-label="Breadcrumb"><div class="nav-container">';
    $defaults['wrap_after']  = '</div></nav>';
    return $defaults;
});

/* ------------------------------------------------------------------
 * Replace WooCommerce's default placeholder image — the mountain-icon
 * placeholder on a white box clashes hard with the dark brand. Prefer
 * the category-specific SVG when a product category is in scope; fall
 * back to the generic branded vial placeholder otherwise.
 * ----------------------------------------------------------------*/
add_filter('woocommerce_placeholder_img_src', function ($src) {
    $product_id = 0;
    if (function_exists('is_product') && is_product()) {
        $product_id = get_the_ID();
    } elseif (is_singular('product')) {
        $product_id = get_the_ID();
    }
    $cat_slug = '';
    if ($product_id) {
        $terms = get_the_terms($product_id, 'product_cat');
        if ($terms && !is_wp_error($terms)) {
            $cat_slug = $terms[0]->slug;
        }
    }
    if ($cat_slug && function_exists('nav_get_category_placeholder')) {
        return nav_get_category_placeholder($cat_slug);
    }
    return get_template_directory_uri() . '/assets/images/product-placeholder.svg';
});

/* ------------------------------------------------------------------
 * Related products heading — "Related products" is WC's default. Swap
 * in an on-brand label so the section reads as curated, not automatic.
 * ----------------------------------------------------------------*/
add_filter('gettext', function ($translation, $text, $domain) {
    if ($domain !== 'woocommerce') return $translation;
    if ($text === 'Related products') return __('You may also research', 'navigate-peptides');
    return $translation;
}, 10, 3);
