<?php
/**
 * WooCommerce loop product card.
 *
 * Thin wrapper — delegates to the shared `template-parts/product-card.php`
 * so related products, upsells, shortcode loops, and shop archives all
 * render the same branded card. Previously this file duplicated the
 * markup from archive-product.php, meaning any fix had to be made twice.
 *
 * @package NavigatePeptides
 */

defined('ABSPATH') || exit;

global $product;
if (!($product instanceof WC_Product) || !$product->exists()) {
    return;
}

// Related-products cards omit the excerpt + RUO disclaimer so they stay
// compact next to the single-product hero. Shop archive cards pass
// show_excerpt => true for the fuller layout.
get_template_part('template-parts/product-card', null, [
    'product'      => $product,
    'show_excerpt' => false,
]);
