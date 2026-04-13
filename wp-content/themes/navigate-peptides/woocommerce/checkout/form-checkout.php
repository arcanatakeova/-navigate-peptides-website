<?php
/**
 * WooCommerce Checkout Form — Navigate Peptides Override
 *
 * Ensures RUO checkbox and compliance disclaimers are properly
 * integrated with theme styling.
 *
 * @package NavigatePeptides
 */

defined('ABSPATH') || exit;

// If checkout registration is disabled and not logged in, the user cannot checkout.
if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'navigate-peptides')));
    return;
}
?>

<div class="nav-checkout-wrapper">

    <?php do_action('woocommerce_before_checkout_form', $checkout); ?>

    <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

        <div class="nav-checkout-grid">

            <!-- Left: Billing + Shipping -->
            <div class="nav-checkout-grid__main">

                <?php if ($checkout->get_checkout_fields()) : ?>

                    <?php do_action('woocommerce_checkout_before_customer_details'); ?>

                    <div id="customer_details">
                        <div class="nav-checkout-section">
                            <?php do_action('woocommerce_checkout_billing'); ?>
                        </div>

                        <div class="nav-checkout-section">
                            <?php do_action('woocommerce_checkout_shipping'); ?>
                        </div>
                    </div>

                    <?php do_action('woocommerce_checkout_after_customer_details'); ?>

                <?php endif; ?>

            </div>

            <!-- Right: Order Review + Payment -->
            <div class="nav-checkout-grid__sidebar">

                <div class="nav-checkout-order-review">
                    <h3 id="order_review_heading"><?php esc_html_e('Your order', 'navigate-peptides'); ?></h3>

                    <?php do_action('woocommerce_checkout_before_order_review'); ?>

                    <div id="order_review" class="woocommerce-checkout-review-order">
                        <?php do_action('woocommerce_checkout_order_review'); ?>
                    </div>

                    <?php do_action('woocommerce_checkout_after_order_review'); ?>
                </div>

                <!-- Sitewide RUO Disclaimer -->
                <div class="nav-checkout-disclaimer">
                    <p><?php echo esc_html(nav_get_disclaimer('sitewide')); ?></p>
                </div>

            </div>

        </div>

    </form>

    <?php do_action('woocommerce_after_checkout_form', $checkout); ?>

</div>
