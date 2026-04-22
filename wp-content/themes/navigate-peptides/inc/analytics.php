<?php
/**
 * Analytics — GA4 enqueue + WooCommerce ecommerce events
 *
 * Admin sets the measurement ID via the `nav_ga4_id` option (Customizer or
 * `update_option('nav_ga4_id', 'G-XXXXXXXXXX')`). If unset, no GA snippet
 * is emitted — safe default for staging and processor pre-audit.
 *
 * Events emitted (matching GA4 recommended ecommerce schema):
 *   - page_view     — automatic via gtag config
 *   - view_item     — single product page load
 *   - view_item_list — product archive / category
 *   - add_to_cart   — on woocommerce_add_to_cart
 *   - remove_from_cart — on woocommerce_cart_item_removed
 *   - begin_checkout — on woocommerce_checkout_init
 *   - purchase      — on woocommerce_thankyou
 *   - view_search_results — on /?s= result page
 *
 * @package NavigatePeptides
 */

defined('ABSPATH') || exit;

/**
 * Get the configured GA4 measurement ID. Empty string when unconfigured.
 */
function nav_ga4_id(): string {
    $id = strtoupper((string) get_option('nav_ga4_id', ''));
    // GA4 IDs are always uppercase: G-XXXXXXXXXX. Reject lowercase input.
    if (!preg_match('/^G-[A-Z0-9]{5,20}$/', $id)) return '';
    return $id;
}

/**
 * Emit the GA4 gtag.js snippet in <head> when configured.
 * Consent mode v2 gate — the snippet loads but analytics_storage is
 * DENIED until the consent banner records opt-in; then the banner JS
 * upgrades consent and gtag backfills events from the dataLayer. This
 * keeps the site GDPR/CCPA-defensible for EU/UK/CA visitors.
 */
add_action('wp_head', function () {
    $id = nav_ga4_id();
    if ($id === '') return;
    ?>
    <script>
      // Consent Mode v2 — set defaults BEFORE gtag.js loads so the tag
      // queues events instead of firing them until the visitor opts in.
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('consent', 'default', {
        ad_storage:             'denied',
        ad_user_data:           'denied',
        ad_personalization:     'denied',
        analytics_storage:      'denied',
        functionality_storage:  'granted',
        security_storage:       'granted',
        wait_for_update:        500
      });
    </script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($id); ?>"></script>
    <script>
      gtag('js', new Date());
      gtag('config', '<?php echo esc_js($id); ?>', {
        anonymize_ip: true,
        send_page_view: true
      });
    </script>
    <?php
}, 99);

/**
 * Build a GA4 "items" array entry from a WC product.
 */
function nav_ga4_product_item($product, $quantity = 1): array {
    if (!$product || !method_exists($product, 'get_id')) return [];

    $terms = get_the_terms($product->get_id(), 'product_cat');
    $cat   = ($terms && !is_wp_error($terms) && !empty($terms[0])) ? $terms[0]->name : '';

    return [
        'item_id'    => (string) $product->get_sku() ?: 'NAV-' . $product->get_id(),
        'item_name'  => $product->get_name(),
        'item_brand' => 'Navigate Peptides',
        'item_category' => $cat,
        'price'      => (float) $product->get_price(),
        'quantity'   => (int) $quantity,
    ];
}

/**
 * Push a GA4 event via dataLayer.
 */
function nav_ga4_emit_event(string $event, array $payload): void {
    if (nav_ga4_id() === '') return;
    // wp_json_encode returns false on non-UTF-8 / invalid input — emitting
    // `dataLayer.push(false)` is a silent no-op that drops the event. Bail
    // with a log instead so we can spot products / titles with bad data.
    $json = wp_json_encode(
        ['event' => $event] + $payload,
        JSON_UNESCAPED_SLASHES | JSON_HEX_TAG
    );
    if ($json === false) {
        error_log(sprintf(
            '[nav_ga4] json encode failed event=%s err=%s',
            $event,
            json_last_error_msg()
        ));
        return;
    }
    ?>
    <script>
      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push(<?php echo $json; ?>);
    </script>
    <?php
}

/* ------------------------------------------------------------------
 * view_item — single product page
 * ----------------------------------------------------------------*/
add_action('wp_footer', function () {
    if (!is_singular('product') || !function_exists('wc_get_product')) return;
    $product = wc_get_product(get_the_ID());
    if (!$product) return;
    nav_ga4_emit_event('view_item', [
        'ecommerce' => [
            'currency' => get_woocommerce_currency(),
            'value'    => (float) $product->get_price(),
            'items'    => [nav_ga4_product_item($product)],
        ],
    ]);
});

/* ------------------------------------------------------------------
 * view_item_list — product archive / category
 * ----------------------------------------------------------------*/
add_action('wp_footer', function () {
    if (!function_exists('wc_get_product')) return;
    if (!is_shop() && !is_product_category() && !is_post_type_archive('product')) return;

    global $wp_query;
    $posts = $wp_query->posts ?? [];
    if (empty($posts)) return;

    $items = [];
    $idx = 0;
    foreach ($posts as $post) {
        if ($idx++ >= 20) break;
        $p = wc_get_product($post->ID);
        if (!$p) continue;
        $items[] = nav_ga4_product_item($p) + ['index' => $idx];
    }
    if (empty($items)) return;

    $list = is_product_category() ? (get_queried_object()->name ?? 'Compounds') : 'Compounds';
    nav_ga4_emit_event('view_item_list', [
        'ecommerce' => [
            'item_list_name' => $list,
            'items'          => $items,
        ],
    ]);
});

/* ------------------------------------------------------------------
 * add_to_cart — fires on both AJAX and full-page adds
 * ----------------------------------------------------------------*/
add_action('woocommerce_add_to_cart', function ($cart_item_key, $product_id, $quantity) {
    if (!function_exists('wc_get_product')) return;
    $product = wc_get_product($product_id);
    if (!$product) return;

    // Queue the event so it fires on the next rendered page.
    $payload = [
        'currency' => get_woocommerce_currency(),
        'value'    => (float) $product->get_price() * (int) $quantity,
        'items'    => [nav_ga4_product_item($product, $quantity)],
    ];
    // Transient keyed by nav_visitor_key() — stable for BOTH anon guests
    // and logged-in users, so the GA4 event survives the redirect back
    // from the add_to_cart endpoint. TTL 15min so slow-tab-switch flows
    // still emit. (60s was too tight — event silently vanished in the
    // redirect gap on slow hosts.)
    $key = 'nav_ga4_atc_' . md5(nav_visitor_key());
    set_transient($key, $payload, 15 * MINUTE_IN_SECONDS);
}, 10, 3);

/**
 * Emit queued add_to_cart events on the next page load.
 */
add_action('wp_footer', function () {
    $key = 'nav_ga4_atc_' . md5(nav_visitor_key());
    $payload = get_transient($key);
    if (!$payload) return;
    delete_transient($key);
    nav_ga4_emit_event('add_to_cart', ['ecommerce' => $payload]);
});

/* ------------------------------------------------------------------
 * remove_from_cart — fires on woocommerce_cart_item_removed
 * ----------------------------------------------------------------*/
add_action('woocommerce_cart_item_removed', function ($cart_item_key, $cart) {
    if (!isset($cart->removed_cart_contents[$cart_item_key])) return;
    $removed = $cart->removed_cart_contents[$cart_item_key];
    $product = function_exists('wc_get_product') ? wc_get_product($removed['product_id'] ?? 0) : null;
    if (!$product) return;

    $key = 'nav_ga4_rm_' . md5(nav_visitor_key());
    set_transient($key, [
        'currency' => get_woocommerce_currency(),
        'value'    => (float) $product->get_price() * (int) ($removed['quantity'] ?? 1),
        'items'    => [nav_ga4_product_item($product, (int) ($removed['quantity'] ?? 1))],
    ], 15 * MINUTE_IN_SECONDS);
}, 10, 2);

add_action('wp_footer', function () {
    $key = 'nav_ga4_rm_' . md5(nav_visitor_key());
    $payload = get_transient($key);
    if (!$payload) return;
    delete_transient($key);
    nav_ga4_emit_event('remove_from_cart', ['ecommerce' => $payload]);
});

/* ------------------------------------------------------------------
 * begin_checkout — on checkout page load
 * ----------------------------------------------------------------*/
add_action('wp_footer', function () {
    if (!function_exists('is_checkout') || !is_checkout() || is_order_received_page()) return;
    if (!WC()->cart || WC()->cart->is_empty()) return;

    $items = [];
    foreach (WC()->cart->get_cart() as $cart_item) {
        if (empty($cart_item['data'])) continue;
        $items[] = nav_ga4_product_item($cart_item['data'], $cart_item['quantity'] ?? 1);
    }
    if (empty($items)) return;

    nav_ga4_emit_event('begin_checkout', [
        'ecommerce' => [
            'currency' => get_woocommerce_currency(),
            'value'    => (float) WC()->cart->get_total('edit'),
            'items'    => $items,
        ],
    ]);
});

/* ------------------------------------------------------------------
 * purchase — on order-received (thank you) page
 * ----------------------------------------------------------------*/
add_action('woocommerce_thankyou', function ($order_id) {
    if (!$order_id) return;
    $order = wc_get_order($order_id);
    if (!$order) return;

    // Atomic de-dup: add_post_meta with $unique=true fails (returns false)
    // if the meta row already exists. Check-then-act with get/update_meta
    // races across tabs; this path is safe even under concurrent requests
    // because MySQL's UNIQUE-key check on postmeta happens in a single
    // statement.
    $claimed = add_post_meta($order_id, '_nav_ga4_fired', '1', true);
    if (!$claimed) return;

    $items = [];
    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        if (!$product) continue;
        $items[] = nav_ga4_product_item($product, (int) $item->get_quantity());
    }

    nav_ga4_emit_event('purchase', [
        'ecommerce' => [
            'transaction_id' => (string) $order->get_order_number(),
            'value'          => (float) $order->get_total(),
            'tax'            => (float) $order->get_total_tax(),
            'shipping'       => (float) $order->get_shipping_total(),
            'currency'       => $order->get_currency(),
            'items'          => $items,
        ],
    ]);
}, 20);

/* ------------------------------------------------------------------
 * view_search_results + site search on the theme-native /?s= path
 * ----------------------------------------------------------------*/
add_action('wp_footer', function () {
    if (!is_search()) return;
    $q = wp_strip_all_tags((string) get_search_query());
    if ($q === '') return;
    global $wp_query;
    nav_ga4_emit_event('view_search_results', [
        'search_term' => mb_substr($q, 0, 80),
        'result_count' => (int) ($wp_query->found_posts ?? 0),
    ]);
});

/* ------------------------------------------------------------------
 * Customizer: GA4 Measurement ID field
 * ----------------------------------------------------------------*/
add_action('customize_register', function ($wp_customize) {
    $wp_customize->add_section('nav_analytics', [
        'title'    => __('Analytics', 'navigate-peptides'),
        'priority' => 200,
    ]);
    $wp_customize->add_setting('nav_ga4_id', [
        'type'              => 'option',
        'default'           => '',
        'sanitize_callback' => function ($value) {
            $value = strtoupper(trim((string) $value));
            return preg_match('/^G-[A-Z0-9]{5,20}$/', $value) ? $value : '';
        },
        'capability'        => 'manage_options',
    ]);
    $wp_customize->add_control('nav_ga4_id', [
        'section'     => 'nav_analytics',
        'label'       => __('GA4 Measurement ID', 'navigate-peptides'),
        'description' => __('Format: G-XXXXXXXXXX. Leave blank to disable analytics.', 'navigate-peptides'),
        'type'        => 'text',
    ]);
});
