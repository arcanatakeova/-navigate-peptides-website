<?php
/**
 * Minicart drawer — slides in from the right on add-to-cart.
 * Rendered server-side in wp_footer and refreshed via WC fragments.
 *
 * @package NavigatePeptides
 */

defined('ABSPATH') || exit;

/**
 * Render the minicart drawer markup.
 *
 * Uses Woo's woocommerce_mini_cart template for the item rows so all the
 * existing product-type / variation logic continues to work, then wraps
 * it in our own drawer chrome. Fragment refresh targets the inner list
 * div so the drawer chrome stays in place.
 */
function nav_render_minicart(): void {
    if (!function_exists('WC')) return;
    ?>
    <aside
        class="nav-minicart"
        id="nav-minicart"
        role="dialog"
        aria-modal="true"
        aria-labelledby="nav-minicart-title"
        aria-hidden="true"
    >
        <button type="button" class="nav-minicart__scrim" id="nav-minicart-scrim" aria-label="<?php esc_attr_e('Close cart', 'navigate-peptides'); ?>" tabindex="-1"></button>

        <div class="nav-minicart__panel">
            <header class="nav-minicart__header">
                <h2 id="nav-minicart-title" class="nav-minicart__title"><?php esc_html_e('Cart', 'navigate-peptides'); ?></h2>
                <button type="button" class="nav-minicart__close" id="nav-minicart-close" aria-label="<?php esc_attr_e('Close cart', 'navigate-peptides'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="22" height="22"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </header>

            <div class="nav-minicart__body widget_shopping_cart_content">
                <?php
                // woocommerce_mini_cart outputs Woo's default item list +
                // totals + buttons. Our CSS restyles the default markup.
                woocommerce_mini_cart();
                ?>
            </div>

            <footer class="nav-minicart__footer">
                <p class="nav-minicart__disclaimer"><?php echo esc_html(nav_get_disclaimer('product')); ?></p>
            </footer>
        </div>
    </aside>
    <?php
}

// Render once per page in the footer.
add_action('wp_footer', 'nav_render_minicart', 10);

/**
 * Refresh the drawer body via WC fragments so add/remove/update
 * reflects without a full reload. Targets the inner widget div so
 * the drawer chrome (header, close button) stays mounted.
 */
add_filter('woocommerce_add_to_cart_fragments', function (array $fragments) {
    ob_start();
    ?>
    <div class="widget_shopping_cart_content"><?php woocommerce_mini_cart(); ?></div>
    <?php
    $fragments['div.widget_shopping_cart_content'] = ob_get_clean();
    return $fragments;
});

/**
 * Auto-open the drawer when an item lands in the cart — server tells the
 * client via a transient flag so the next page render pops the drawer.
 */
add_action('woocommerce_add_to_cart', function () {
    $token = wp_get_session_token();
    if ($token) set_transient('nav_minicart_autoopen_' . md5($token), 1, 30);
}, 99);

add_action('wp_footer', function () {
    $token = wp_get_session_token();
    if (!$token) return;
    $key = 'nav_minicart_autoopen_' . md5($token);
    if (!get_transient($key)) return;
    delete_transient($key);
    ?>
    <script>
      (function () {
        var openFn = window.navMinicartOpen;
        if (typeof openFn === 'function') {
            openFn();
        } else {
            // main.js hasn't bound yet — queue and flush on DOMContentLoaded
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof window.navMinicartOpen === 'function') window.navMinicartOpen();
            });
        }
      })();
    </script>
    <?php
}, 101);

/**
 * Hijack the header cart link to open the drawer instead of navigating
 * to /cart/. The PHP href remains as a no-JS fallback.
 */
add_action('wp_footer', function () {
    ?>
    <script>
      (function () {
        var cartLink = document.querySelector('.nav-header__cart');
        if (!cartLink) return;
        cartLink.addEventListener('click', function (e) {
            if (typeof window.navMinicartOpen === 'function') {
                e.preventDefault();
                window.navMinicartOpen();
            }
        });
      })();
    </script>
    <?php
}, 102);
