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
 * Redact PII to a short stable token suitable for error_log / audit
 * trails. SHA-256 truncated to 12 hex chars gives ~48 bits of entropy
 * — enough to spot a repeated offender across logs while losing the
 * raw value entirely, so the log line is no longer a PII spill.
 *
 * Empty input returns empty string (so call-sites can short-circuit
 * without nesting). Salted by AUTH_SALT so logs from one site can't
 * be brute-forced against an email/IP list pulled from another.
 */
function nav_redact(string $value): string {
    $value = trim($value);
    if ($value === '') {
        return '';
    }
    $salt = defined('AUTH_SALT') ? AUTH_SALT : 'nav-fallback-salt';
    return substr(hash('sha256', $salt . '|' . $value), 0, 12);
}

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
