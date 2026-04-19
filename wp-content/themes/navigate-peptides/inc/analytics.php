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
    $id = (string) get_option('nav_ga4_id', '');
    // GA4 IDs always start with 'G-' followed by 10 alphanumerics; strip
    // anything else defensively.
    if (!preg_match('/^G-[A-Z0-9]{5,20}$/i', $id)) return '';
    return $id;
}

/**
 * Emit the GA4 gtag.js snippet in <head> when configured.
 * Deliberately emitted late (priority 99) so dataLayer-pusher
 * events fired in the page can append before gtag() exists.
 */
add_action('wp_head', function () {
    $id = nav_ga4_id();
    if ($id === '') return;
    ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($id); ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '<?php echo esc_js($id); ?>', {
        send_page_view: true,
        anonymize_ip: true
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
    ?>
    <script>
      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push(<?php echo wp_json_encode(['event' => $event] + $payload, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG); ?>);
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
    // Use a transient keyed by session cookie so it survives the redirect.
    $key = 'nav_ga4_atc_' . md5(wp_get_session_token() ?: (string) wp_hash(serialize($payload)));
    set_transient($key, $payload, 60);
}, 10, 3);

/**
 * Emit queued add_to_cart events on the next page load.
 */
add_action('wp_footer', function () {
    // Only check when we likely just redirected from an add.
    $token = wp_get_session_token();
    if (!$token) return;
    $key = 'nav_ga4_atc_' . md5($token);
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

    $key = 'nav_ga4_rm_' . md5(wp_get_session_token() ?: $cart_item_key);
    set_transient($key, [
        'currency' => get_woocommerce_currency(),
        'value'    => (float) $product->get_price() * (int) ($removed['quantity'] ?? 1),
        'items'    => [nav_ga4_product_item($product, (int) ($removed['quantity'] ?? 1))],
    ], 60);
}, 10, 2);

add_action('wp_footer', function () {
    $token = wp_get_session_token();
    if (!$token) return;
    $key = 'nav_ga4_rm_' . md5($token);
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

    // Prevent double-fire on page refresh of thank-you.
    if ($order->get_meta('_nav_ga4_fired', true)) return;
    $order->update_meta_data('_nav_ga4_fired', '1');
    $order->save();

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
            $value = trim((string) $value);
            return preg_match('/^G-[A-Z0-9]{5,20}$/i', $value) ? $value : '';
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
