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

    <!-- Checkout step indicator — purely visual, no state required -->
    <nav class="nav-checkout-steps" aria-label="<?php esc_attr_e('Checkout progress', 'navigate-peptides'); ?>">
        <ol>
            <li class="nav-checkout-steps__item is-done">
                <span class="nav-checkout-steps__n">01</span>
                <span class="nav-checkout-steps__label"><?php esc_html_e('Cart', 'navigate-peptides'); ?></span>
            </li>
            <li class="nav-checkout-steps__item is-current" aria-current="step">
                <span class="nav-checkout-steps__n">02</span>
                <span class="nav-checkout-steps__label"><?php esc_html_e('Details &amp; Payment', 'navigate-peptides'); ?></span>
            </li>
            <li class="nav-checkout-steps__item">
                <span class="nav-checkout-steps__n">03</span>
                <span class="nav-checkout-steps__label"><?php esc_html_e('Confirmation', 'navigate-peptides'); ?></span>
            </li>
        </ol>
    </nav>

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

                <!-- Trust strip — communicates what the customer gets after submit -->
                <ul class="nav-checkout-trust">
                    <li>
                        <svg class="nav-icon nav-icon--sm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                        <span><?php esc_html_e('SSL-secured payment', 'navigate-peptides'); ?></span>
                    </li>
                    <li>
                        <svg class="nav-icon nav-icon--sm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                        <span><?php esc_html_e('Batch-specific COA shipped with every order', 'navigate-peptides'); ?></span>
                    </li>
                    <li>
                        <svg class="nav-icon nav-icon--sm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5a8.25 8.25 0 0 1 8.25-8.25.75.75 0 0 1 .75.75v6.75H18a.75.75 0 0 1 .75.75 8.25 8.25 0 0 1-16.5 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12.75 3a.75.75 0 0 1 .75-.75 8.25 8.25 0 0 1 8.25 8.25.75.75 0 0 1-.75.75h-7.5a.75.75 0 0 1-.75-.75V3Z"/></svg>
                        <span><?php esc_html_e('Card, crypto (BTC / ETH / USDC), and ACH accepted', 'navigate-peptides'); ?></span>
                    </li>
                </ul>

            </div>

        </div>

    </form>

    <?php do_action('woocommerce_after_checkout_form', $checkout); ?>

</div>
