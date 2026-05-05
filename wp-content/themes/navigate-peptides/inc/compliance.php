<?php
/**
 * Compliance & Disclaimers
 *
 * Processor-mandated exact text. DO NOT modify without legal review.
 * AllayPay/NMI and Mastercard BRAM require these disclaimers verbatim.
 *
 * @package NavigatePeptides
 */

defined('ABSPATH') || exit;

/**
 * Get compliance disclaimer by key.
 */
function nav_get_disclaimer(string $key = 'sitewide'): string {
    // Single canonical RUO line. The $key argument is preserved for
    // call-site compatibility but every key resolves to the same text
    // so messaging stays consistent across product cards, PDP tabs,
    // cart, footer, and the compliance scanner block.
    return 'All products sold on this website are intended for research and identification purposes only. These products are not intended for human dosing, injection, or ingestion.';
}

/**
 * Render product disclaimer block.
 */
function nav_product_disclaimer(): void {
    ?>
    <div class="nav-disclaimer nav-disclaimer--product">
        <p><?php echo esc_html(nav_get_disclaimer('product')); ?></p>
    </div>
    <?php
}

/**
 * Render sitewide disclaimer block.
 */
function nav_sitewide_disclaimer(): void {
    ?>
    <div class="nav-disclaimer nav-disclaimer--sitewide">
        <p><?php echo esc_html(nav_get_disclaimer('sitewide')); ?></p>
    </div>
    <?php
}

/**
 * Add structured data for compliance (visible to Mastercard scanners).
 */
add_action('wp_footer', function () {
    ?>
    <div class="nav-compliance-footer" aria-label="Research use disclaimer">
        <div class="nav-container">
            <p class="nav-compliance-footer__text">
                <?php echo esc_html(nav_get_disclaimer('sitewide')); ?>
            </p>
        </div>
    </div>
    <?php
}, 99);
