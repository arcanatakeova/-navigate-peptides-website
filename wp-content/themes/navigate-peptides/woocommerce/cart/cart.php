<?php
/**
 * WooCommerce Cart Template — Navigate Peptides Override
 *
 * Adds theme styling and RUO disclaimer to the cart page.
 * Based on WooCommerce cart/cart.php template.
 *
 * @package NavigatePeptides
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_cart');
?>

<form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
    <?php do_action('woocommerce_before_cart_table'); ?>

    <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
        <thead>
            <tr>
                <th class="product-remove"><span class="screen-reader-text"><?php esc_html_e('Remove item', 'navigate-peptides'); ?></span></th>
                <th class="product-thumbnail"><span class="screen-reader-text"><?php esc_html_e('Thumbnail', 'navigate-peptides'); ?></span></th>
                <th class="product-name"><?php esc_html_e('Compound', 'navigate-peptides'); ?></th>
                <th class="product-price"><?php esc_html_e('Price', 'navigate-peptides'); ?></th>
                <th class="product-quantity"><?php esc_html_e('Quantity', 'navigate-peptides'); ?></th>
                <th class="product-subtotal"><?php esc_html_e('Subtotal', 'navigate-peptides'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php do_action('woocommerce_before_cart_contents'); ?>

            <?php
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                    $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                    ?>
                    <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">

                        <td class="product-remove">
                            <?php
                            echo apply_filters(
                                'woocommerce_cart_item_remove_link',
                                sprintf(
                                    '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                                    esc_url(wc_get_cart_remove_url($cart_item_key)),
                                    /* translators: %s is the product name */
                                    esc_attr(sprintf(__('Remove %s from cart', 'navigate-peptides'), wp_strip_all_tags($_product->get_name()))),
                                    esc_attr($product_id),
                                    esc_attr($_product->get_sku())
                                ),
                                $cart_item_key
                            );
                            ?>
                        </td>

                        <td class="product-thumbnail">
                            <?php
                            $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('product-card', ['loading' => 'lazy']), $cart_item, $cart_item_key);
                            if (!$product_permalink) {
                                echo $thumbnail;
                            } else {
                                printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail);
                            }
                            ?>
                        </td>

                        <td class="product-name" data-title="<?php esc_attr_e('Compound', 'navigate-peptides'); ?>">
                            <?php
                            if (!$product_permalink) {
                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;');
                            } else {
                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
                            }

                            do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);

                            echo wc_get_formatted_cart_item_data($cart_item);

                            if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                                echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'navigate-peptides') . '</p>', $product_id));
                            }
                            ?>
                        </td>

                        <td class="product-price" data-title="<?php esc_attr_e('Price', 'navigate-peptides'); ?>">
                            <?php echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); ?>
                        </td>

                        <td class="product-quantity" data-title="<?php esc_attr_e('Quantity', 'navigate-peptides'); ?>">
                            <?php
                            if ($_product->is_sold_individually()) {
                                $min_quantity = 1;
                                $max_quantity = 1;
                            } else {
                                $min_quantity = 0;
                                $max_quantity = $_product->get_max_purchase_quantity();
                            }

                            $product_quantity = woocommerce_quantity_input(
                                [
                                    'input_name'   => "cart[{$cart_item_key}][qty]",
                                    'input_value'  => $cart_item['quantity'],
                                    'max_value'    => $max_quantity,
                                    'min_value'    => $min_quantity,
                                    'product_name' => $_product->get_name(),
                                ],
                                $_product,
                                false
                            );

                            echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item);
                            ?>
                        </td>

                        <td class="product-subtotal" data-title="<?php esc_attr_e('Subtotal', 'navigate-peptides'); ?>">
                            <?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>

            <?php do_action('woocommerce_cart_contents'); ?>

            <tr>
                <td colspan="6" class="actions">
                    <?php if (wc_coupons_enabled()) : ?>
                        <div class="coupon">
                            <label for="coupon_code" class="screen-reader-text"><?php esc_html_e('Coupon:', 'navigate-peptides'); ?></label>
                            <input type="text" name="coupon_code" class="input-text nav-form-input" id="coupon_code" value="" placeholder="<?php esc_attr_e('Coupon code', 'navigate-peptides'); ?>" />
                            <button type="submit" class="nav-btn nav-btn--outline nav-btn--sm" name="apply_coupon" value="<?php esc_attr_e('Apply coupon', 'navigate-peptides'); ?>"><?php esc_html_e('Apply coupon', 'navigate-peptides'); ?></button>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="nav-btn nav-btn--outline nav-btn--sm" name="update_cart" value="<?php esc_attr_e('Update cart', 'navigate-peptides'); ?>"><?php esc_html_e('Update cart', 'navigate-peptides'); ?></button>

                    <?php do_action('woocommerce_cart_actions'); ?>
                    <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                </td>
            </tr>

            <?php do_action('woocommerce_after_cart_contents'); ?>
        </tbody>
    </table>

    <?php do_action('woocommerce_after_cart_table'); ?>

</form>

<!-- RUO Disclaimer — Required by AllayPay/Mastercard BRAM -->
<div class="nav-cart-disclaimer">
    <p><?php echo esc_html(nav_get_disclaimer('sitewide')); ?></p>
</div>

<?php do_action('woocommerce_before_cart_collaterals'); ?>

<div class="cart-collaterals">
    <?php
    /**
     * Cart collaterals hook.
     *
     * @hooked woocommerce_cross_sell_display
     * @hooked woocommerce_cart_totals - 10
     */
    do_action('woocommerce_cart_collaterals');
    ?>
</div>

<?php
// Mobile-only sticky checkout bar — the cart_totals card pushes the
// proceed-to-checkout button below the fold on phones, so duplicate
// the action in a sticky bar pinned to the bottom of the viewport so
// customers always have a reachable CTA.
if (function_exists('WC') && WC()->cart && !WC()->cart->is_empty()) :
    $nav_cart_total = wc_price(WC()->cart->get_total('edit'));
?>
<div class="nav-cart-sticky-checkout" role="region" aria-label="<?php esc_attr_e('Cart total and checkout', 'navigate-peptides'); ?>">
    <span class="nav-cart-sticky-checkout__total">
        <small><?php esc_html_e('Total', 'navigate-peptides'); ?></small>
        <?php echo $nav_cart_total; // phpcs:ignore WordPress.Security.EscapeOutput -- wc_price returns trusted HTML ?>
    </span>
    <a href="<?php echo esc_url(wc_get_checkout_url()); ?>"><?php esc_html_e('Checkout →', 'navigate-peptides'); ?></a>
</div>
<?php endif; ?>

<?php do_action('woocommerce_after_cart'); ?>
