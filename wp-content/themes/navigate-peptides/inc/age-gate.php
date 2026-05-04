<?php
/**
 * Age + Research-Use-Only Verification Gate
 *
 * Full-screen modal rendered before any visitor reaches the catalog. Required
 * for processor compliance + RUO posture: confirms the visitor is 21+ and
 * understands the supplied compounds are for in-vitro research only.
 *
 * Implementation:
 *   - Server-side conditional render via wp_body_open. If the visitor's
 *     `nav_age_gate_v1` cookie is set + valid, the gate isn't emitted at all
 *     so there's zero render cost on returning visitors.
 *   - On submit, JS sets the cookie + localStorage with a 30-day TTL and
 *     removes the gate from the DOM. The body class flips so scroll resumes.
 *   - Decline → window.location to a neutral page (Google) so users who
 *     can't agree have a clean exit.
 *   - Skipped for wp-admin, REST, AJAX, the login screen, and known bot
 *     user agents (so the catalog stays indexable).
 *
 * @package NavigatePeptides
 */

defined('ABSPATH') || exit;

if (!defined('NAV_AGE_GATE_COOKIE')) {
    define('NAV_AGE_GATE_COOKIE', 'nav_age_gate_v1');
}
if (!defined('NAV_AGE_GATE_TTL_DAYS')) {
    define('NAV_AGE_GATE_TTL_DAYS', 30);
}

/**
 * Decide whether to render the gate for this request.
 *
 * NOTE: cookie + user-agent bypasses run client-side, not here. wp.com
 * fronts the site with Varnish; PHP-conditional output keyed on cookie
 * or UA gets cached on the first miss and the wrong variant gets
 * served forever. So the gate is always emitted into the HTML; JS
 * inside the document checks the cookie and hides the gate (or
 * removes it entirely) before paint when present, and the bot list is
 * matched in the same inline bootstrap.
 */
function nav_age_gate_should_render(): bool {
    if (is_admin()) return false;
    if (defined('DOING_AJAX') && DOING_AJAX) return false;
    if (defined('REST_REQUEST') && REST_REQUEST) return false;
    if (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) return false;

    // Skip on the login screen — wp-login.php has its own brand surface.
    $script = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
    if (str_ends_with($script, '/wp-login.php') || str_ends_with($script, '/wp-register.php')) {
        return false;
    }

    // Filter for testing — `add_filter('nav_age_gate_render', '__return_false');`
    return (bool) apply_filters('nav_age_gate_render', true);
}

/**
 * Inline pre-paint bootstrap. Runs in <head> before <body> is parsed.
 * Checks the visitor's cookie + UA against the bypass list and, if
 * matched, sets a `nav-age-gate-bypass` class on <html>. CSS then
 * hides the gate and unlocks body scroll before anything paints —
 * the cookied / crawler user never sees a flash.
 *
 * This avoids the wp.com Varnish problem with PHP-conditional output:
 * the cached HTML is identical for everyone and the per-visitor
 * decision happens in the browser instead of behind the cache.
 */
add_action('wp_head', function () {
    if (!nav_age_gate_should_render()) return;

    $cookie_name = NAV_AGE_GATE_COOKIE;
    $bot_pattern = 'googlebot|bingbot|slurp|duckduckbot|baiduspider|yandex|sogou|exabot|facebot|facebookexternalhit|twitterbot|linkedinbot|whatsapp|slackbot|discordbot|telegrambot|applebot|pingdom|uptimerobot|gtmetrix|lighthouse|chrome-lighthouse|headlesschrome|ahrefsbot|semrushbot|mj12bot';
    ?>
<style id="nav-age-gate-bootstrap-css">
.nav-age-gate-bypass .nav-age-gate { display: none !important; }
.nav-age-gate-bypass body.has-age-gate { overflow: auto !important; }
</style>
<script id="nav-age-gate-bootstrap-js">
(function () {
    try {
        var ua = navigator.userAgent || '';
        var bots = /<?php echo $bot_pattern; ?>/i;
        var hasCookie = document.cookie.indexOf('<?php echo esc_js($cookie_name); ?>=') !== -1;
        if (hasCookie || bots.test(ua)) {
            document.documentElement.classList.add('nav-age-gate-bypass');
        }
    } catch (_) { /* if anything throws, fall through to showing the gate */ }
})();
</script>
    <?php
}, 1);

/**
 * Inject the gate at the top of <body> so it covers the page before
 * subsequent markup paints.
 */
add_action('wp_body_open', function () {
    if (!nav_age_gate_should_render()) return;

    $brand_name   = function_exists('get_bloginfo') ? get_bloginfo('name') : 'Navigate Peptides';
    $decline_url  = (string) apply_filters('nav_age_gate_decline_url', 'https://www.google.com/');
    $cookie_name  = NAV_AGE_GATE_COOKIE;
    $ttl_days     = (int) NAV_AGE_GATE_TTL_DAYS;
    ?>
    <div
        class="nav-age-gate"
        id="nav-age-gate"
        role="dialog"
        aria-modal="true"
        aria-labelledby="nav-age-gate-title"
        aria-describedby="nav-age-gate-desc"
        data-cookie="<?php echo esc_attr($cookie_name); ?>"
        data-ttl-days="<?php echo esc_attr((string) $ttl_days); ?>"
        data-decline-url="<?php echo esc_attr($decline_url); ?>"
    >
        <div class="nav-age-gate__scrim" aria-hidden="true"></div>

        <div class="nav-age-gate__panel" role="document">
            <header class="nav-age-gate__header">
                <span class="nav-age-gate__kicker"><?php esc_html_e('Research Use Only · 21+', 'navigate-peptides'); ?></span>
                <h1 id="nav-age-gate-title" class="nav-age-gate__title">
                    <?php echo esc_html($brand_name); ?>
                </h1>
                <p class="nav-age-gate__lede">
                    <?php esc_html_e('A structured supply for controlled laboratory research — batch-traceable compounds with third-party purity data and clear technical documentation.', 'navigate-peptides'); ?>
                </p>
            </header>

            <div id="nav-age-gate-desc" class="nav-age-gate__body">
                <p>
                    <?php esc_html_e('Compounds on this site are research-grade materials intended for in-vitro investigation by qualified laboratory personnel. Every batch ships with a certificate of analysis. Marketing language is intentionally absent — clarity and documentation come first.', 'navigate-peptides'); ?>
                </p>
                <p class="nav-age-gate__divider" aria-hidden="true"></p>
                <p class="nav-age-gate__instructions">
                    <?php esc_html_e('To continue, please confirm:', 'navigate-peptides'); ?>
                </p>
            </div>

            <form class="nav-age-gate__form" id="nav-age-gate-form" novalidate>
                <label class="nav-age-gate__check">
                    <input type="checkbox" name="age" id="nav-age-gate-age" required>
                    <span class="nav-age-gate__check-mark" aria-hidden="true"></span>
                    <span class="nav-age-gate__check-label">
                        <?php esc_html_e('I am 21 years of age or older.', 'navigate-peptides'); ?>
                    </span>
                </label>

                <label class="nav-age-gate__check">
                    <input type="checkbox" name="ruo" id="nav-age-gate-ruo" required>
                    <span class="nav-age-gate__check-mark" aria-hidden="true"></span>
                    <span class="nav-age-gate__check-label">
                        <?php esc_html_e('I understand all products are research-grade compounds for in-vitro laboratory research only — not for human or animal consumption, dosing, or administration.', 'navigate-peptides'); ?>
                    </span>
                </label>

                <div class="nav-age-gate__actions">
                    <button
                        type="submit"
                        class="nav-age-gate__enter"
                        id="nav-age-gate-enter"
                        disabled
                    >
                        <span><?php esc_html_e('Enter site', 'navigate-peptides'); ?></span>
                        <span aria-hidden="true">→</span>
                    </button>
                    <button
                        type="button"
                        class="nav-age-gate__decline"
                        id="nav-age-gate-decline"
                    >
                        <?php esc_html_e('Decline', 'navigate-peptides'); ?>
                    </button>
                </div>
            </form>

            <footer class="nav-age-gate__footer">
                <span><?php esc_html_e('© ', 'navigate-peptides'); ?><?php echo esc_html(wp_date('Y')); ?> <?php echo esc_html($brand_name); ?></span>
                <span class="nav-age-gate__sep" aria-hidden="true">·</span>
                <span><?php esc_html_e('All products intended for research use only.', 'navigate-peptides'); ?></span>
            </footer>
        </div>
    </div>
    <?php
}, 1);

/**
 * Lock body scroll on initial render. JS removes this class on
 * acknowledgment so subsequent navigation is unaffected.
 */
add_filter('body_class', function ($classes) {
    if (nav_age_gate_should_render()) {
        $classes[] = 'has-age-gate';
    }
    return $classes;
});
