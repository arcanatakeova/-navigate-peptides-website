<?php
/**
 * Business identity — single source of truth.
 *
 * Legal entity, customer-facing brand, mailing address, phone, and
 * support email. Used by:
 *   - footer.php (visible address + phone + copyright legal name)
 *   - inc/seo.php (Organization + LocalBusiness JSON-LD)
 *   - template-contact.php (direct contact channels above the form)
 *   - inc/age-gate.php (entry gate brand line)
 *   - inc/woocommerce.php (order email + invoice metadata, where used)
 *   - any future legal page (Privacy / Terms / Refund / Shipping)
 *
 * Change here, propagate everywhere.
 *
 * @package NavigatePeptides
 */

defined('ABSPATH') || exit;

// Customer-facing brand only. Legal entity name + phone are intentionally
// not displayed publicly — the storefront surfaces only the brand DBA, the
// PO mailing address, and the support email. Schema.org omits legalName
// and telephone for the same reason.
if (!defined('NAV_BIZ_DBA'))        define('NAV_BIZ_DBA',        'Navigate Peptides');

// Mailing address (CMRA / UPS Store private mailbox per CMRA rules) --
if (!defined('NAV_BIZ_ADDR_LINE1'))   define('NAV_BIZ_ADDR_LINE1',   '8941 Atlanta Ave #361');
if (!defined('NAV_BIZ_ADDR_CITY'))    define('NAV_BIZ_ADDR_CITY',    'Huntington Beach');
if (!defined('NAV_BIZ_ADDR_REGION'))  define('NAV_BIZ_ADDR_REGION',  'CA');
if (!defined('NAV_BIZ_ADDR_POSTAL'))  define('NAV_BIZ_ADDR_POSTAL',  '92646');
if (!defined('NAV_BIZ_ADDR_COUNTRY')) define('NAV_BIZ_ADDR_COUNTRY', 'US');

// Brand-domain email. Single inbox for now (shipping + general); add per-
// purpose addresses (returns@, support@) only when those mailboxes exist.
if (!defined('NAV_BIZ_EMAIL')) define('NAV_BIZ_EMAIL', 'shipping@navigatepeptides.com');

/**
 * Render the postal address as a 2-line block:
 *   8941 Atlanta Ave #361
 *   Huntington Beach, CA 92646
 *
 * @param string $separator HTML between lines.
 */
function nav_business_address(string $separator = '<br>'): string {
    return wp_kses(
        sprintf(
            '%s%s%s, %s %s',
            esc_html(NAV_BIZ_ADDR_LINE1),
            $separator,
            esc_html(NAV_BIZ_ADDR_CITY),
            esc_html(NAV_BIZ_ADDR_REGION),
            esc_html(NAV_BIZ_ADDR_POSTAL)
        ),
        ['br' => [], 'span' => ['class' => []]]
    );
}

/**
 * Address as a single comma-separated string (Schema.org / mailing labels).
 */
function nav_business_address_inline(): string {
    return sprintf(
        '%s, %s, %s %s',
        NAV_BIZ_ADDR_LINE1,
        NAV_BIZ_ADDR_CITY,
        NAV_BIZ_ADDR_REGION,
        NAV_BIZ_ADDR_POSTAL
    );
}

/**
 * Has the business email been configured? Used as a guard before
 * rendering a mailto: that would otherwise be empty.
 */
function nav_has_business_email(): bool {
    return defined('NAV_BIZ_EMAIL') && NAV_BIZ_EMAIL !== '' && is_email(NAV_BIZ_EMAIL);
}

/**
 * Schema.org PostalAddress array — feeds Organization + LocalBusiness
 * JSON-LD nodes in inc/seo.php.
 *
 * @return array<string, string>
 */
function nav_business_schema_address(): array {
    return [
        '@type'           => 'PostalAddress',
        'streetAddress'   => NAV_BIZ_ADDR_LINE1,
        'addressLocality' => NAV_BIZ_ADDR_CITY,
        'addressRegion'   => NAV_BIZ_ADDR_REGION,
        'postalCode'      => NAV_BIZ_ADDR_POSTAL,
        'addressCountry'  => NAV_BIZ_ADDR_COUNTRY,
    ];
}
