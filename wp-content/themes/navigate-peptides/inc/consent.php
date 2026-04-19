<?php
/**
 * Cookie consent banner — GDPR / CCPA / BRAM-audit minimum.
 *
 * Works alongside the GA4 Consent Mode v2 defaults set in inc/analytics.php.
 * Visitors land with analytics_storage = denied; clicking "Accept" upgrades
 * to granted via gtag('consent', 'update'), and the choice is stored in a
 * 180-day first-party cookie so repeat visits don't re-prompt.
 *
 * Privacy policy and terms-of-service page lookups use the same
 * page_by_path resolution pattern as nav_get_contact_url so slug changes
 * don't break the footer links.
 *
 * @package NavigatePeptides
 */

defined('ABSPATH') || exit;

/**
 * Resolve a legal page URL (privacy, terms) with graceful fallback.
 */
function nav_get_legal_url(string $slug_candidate_a, string $slug_candidate_b = ''): string {
    $page = get_page_by_path($slug_candidate_a);
    if ($page) return (string) get_permalink($page);
    if ($slug_candidate_b !== '') {
        $page = get_page_by_path($slug_candidate_b);
        if ($page) return (string) get_permalink($page);
    }
    // WP core privacy policy page id (set in Settings → Privacy).
    if ($slug_candidate_a === 'privacy-policy') {
        $privacy_id = (int) get_option('wp_page_for_privacy_policy');
        if ($privacy_id && get_post_status($privacy_id) === 'publish') {
            return (string) get_permalink($privacy_id);
        }
    }
    return '';
}

function nav_privacy_url(): string {
    return nav_get_legal_url('privacy-policy', 'privacy');
}

function nav_terms_url(): string {
    return nav_get_legal_url('terms-of-service', 'terms');
}

/**
 * Render the consent banner. Hidden by default; a small inline script at
 * the top of the page checks the nav_consent cookie and shows the banner
 * only when the visitor hasn't chosen yet.
 */
add_action('wp_footer', function () {
    // Don't bother rendering if analytics isn't configured — no tag to gate.
    if (function_exists('nav_ga4_id') && nav_ga4_id() === '') return;

    $privacy = nav_privacy_url();
    $terms   = nav_terms_url();
    ?>
    <div class="nav-consent" id="nav-consent" hidden role="dialog" aria-labelledby="nav-consent-title" aria-describedby="nav-consent-body">
        <div class="nav-consent__inner">
            <div class="nav-consent__copy">
                <h2 id="nav-consent-title" class="nav-consent__title"><?php esc_html_e('Your privacy', 'navigate-peptides'); ?></h2>
                <p id="nav-consent-body" class="nav-consent__body">
                    <?php
                    if ($privacy) {
                        printf(
                            /* translators: %s: link to privacy policy */
                            esc_html__('We use cookies to understand how researchers use this site and to improve the compound catalog. See our %s.', 'navigate-peptides'),
                            sprintf(
                                '<a href="%s">%s</a>',
                                esc_url($privacy),
                                esc_html__('Privacy Policy', 'navigate-peptides')
                            )
                        );
                    } else {
                        esc_html_e('We use cookies to understand how researchers use this site and to improve the compound catalog.', 'navigate-peptides');
                    }
                    ?>
                </p>
            </div>
            <div class="nav-consent__actions">
                <button type="button" class="nav-btn nav-btn--outline nav-consent__btn" id="nav-consent-deny"><?php esc_html_e('Decline', 'navigate-peptides'); ?></button>
                <button type="button" class="nav-btn nav-btn--primary nav-consent__btn" id="nav-consent-allow"><?php esc_html_e('Accept', 'navigate-peptides'); ?></button>
            </div>
        </div>
    </div>

    <script>
      (function () {
        var COOKIE = 'nav_consent';
        var TTL_DAYS = 180;
        var banner = document.getElementById('nav-consent');
        if (!banner) return;

        function readCookie(name) {
            var match = document.cookie.match('(?:^|; )' + name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '=([^;]*)');
            return match ? decodeURIComponent(match[1]) : null;
        }

        function writeCookie(name, value) {
            var expires = new Date(Date.now() + TTL_DAYS * 864e5).toUTCString();
            var secure = location.protocol === 'https:' ? '; Secure' : '';
            document.cookie = name + '=' + encodeURIComponent(value) +
                '; Expires=' + expires + '; Path=/; SameSite=Lax' + secure;
        }

        function applyChoice(choice) {
            if (typeof window.gtag === 'function') {
                window.gtag('consent', 'update', {
                    analytics_storage: choice === 'allow' ? 'granted' : 'denied'
                });
            }
        }

        var existing = readCookie(COOKIE);
        if (existing === 'allow' || existing === 'deny') {
            applyChoice(existing);
            return; // Visitor already chose — don't re-prompt.
        }

        banner.hidden = false;
        banner.setAttribute('data-state', 'open');

        function choose(value) {
            writeCookie(COOKIE, value);
            applyChoice(value);
            banner.setAttribute('data-state', 'closed');
            setTimeout(function () { banner.hidden = true; }, 260);
        }

        var allow = document.getElementById('nav-consent-allow');
        var deny  = document.getElementById('nav-consent-deny');
        if (allow) allow.addEventListener('click', function () { choose('allow'); });
        if (deny)  deny .addEventListener('click', function () { choose('deny');  });
      })();
    </script>
    <?php
}, 200);
