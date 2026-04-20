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
function nav_get_disclaimer(string $key): string {
    $disclaimers = [
        'product'  => 'All products currently listed on this site are for research purposes ONLY.',
        'sitewide' => 'All products sold on this website are intended for research and identification purposes only. These products are not intended for human dosing, injection, or ingestion.',
    ];
    if (!isset($disclaimers[$key])) {
        // Processor-mandated text must never silently disappear. error_log
        // (always) rather than trigger_error (which can escalate to a fatal
        // on hosts configured with strict WP_DEBUG handling).
        error_log(sprintf('[nav_compliance] unknown disclaimer key: %s', $key));
        // Fall back to the sitewide disclaimer so the page stays compliant.
        return $disclaimers['sitewide'];
    }
    return $disclaimers[$key];
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
