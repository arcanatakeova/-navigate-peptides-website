</main><!-- /.nav-main -->

<footer class="nav-footer">
    <div class="nav-container">

        <!-- Footer Navigation Grid -->
        <div class="nav-footer__grid">
            <div class="nav-footer__col">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-footer__logo">
                    <svg viewBox="0 0 213.135 232.253" preserveAspectRatio="xMidYMid meet" fill="none" xmlns="http://www.w3.org/2000/svg" class="nav-footer__logo-icon" aria-hidden="true" focusable="false"><g transform="translate(0.135 232) scale(0.1 -0.1)" fill="currentColor"><path d="M5 2299c4 -12 38 -87 76 -166 38 -80 66 -150 62 -155 -3 -6 -2 -8 4 -5 5 3 18 -13 28 -36 31 -70 75 -166 130 -282 29 -60 65 -137 79 -170 30 -68 63 -130 69 -130 8 0 134 83 150 99 14 15 14 19 1 44 -45 85 -113 246 -107 255 3 6 1 7 -5 3 -11 -7 -16 7 -15 41 1 7 -3 11 -7 8 -4 -3 -15 13 -23 34 -20 48 -75 170 -89 196 -6 11 -24 50 -39 87l-28 68 609 0 608 0 32 -56c17 -31 32 -65 33 -75 1 -10 3 -16 6 -14 8 9 100 -176 94 -190 -3 -8 -10 -12 -16 -8 -6 3 -7 1 -3 -5 4 -7 -5 -35 -20 -62 -15 -28 -25 -52 -23 -53 5 -4 159 -48 177 -50 7 -1 38 45 68 102l55 104 -57 111c-32 61 -65 125 -74 142 -29 50 -54 99 -55 104 -1 3 -11 22 -24 43l-23 37 -840 0 -839 0 6 -21z"/><path d="M1900 2315c0 -2 26 -47 59 -99 55 -90 59 -95 75 -78 9 10 16 23 16 28 0 5 18 40 40 78 22 37 40 70 40 72 0 2 -52 4 -115 4 -63 0 -115 -2 -115 -5z"/><path d="M1385 1644c-299 -44 -555 -154 -803 -344 -104 -80 -237 -218 -303 -315 -30 -44 -56 -82 -59 -85 -9 -9 -42 -84 -61 -137 -16 -47 -16 -54 -3 -68 9 -8 20 -15 27 -15 6 0 40 33 76 72 176 193 318 320 489 435 111 74 315 167 442 201 117 32 248 54 255 43 2 -4 -5 -24 -15 -45 -9 -21 -20 -35 -22 -30 -3 5 -3 3 -2 -4 2 -7 -5 -32 -15 -55 -41 -89 -129 -284 -146 -324 -10 -23 -21 -40 -24 -38 -3 1 -7 -7 -7 -18 -1 -12 -41 -111 -89 -220 -87 -195 -89 -197 -101 -168 -7 17 -20 45 -28 63 -101 217 -194 433 -190 440 3 4 -8 0 -24 -9 -16 -10 -55 -33 -86 -52 -32 -19 -60 -39 -62 -46 -3 -7 9 -43 27 -81 17 -38 69 -154 115 -259 47 -104 91 -203 99 -220 7 -16 23 -51 34 -77 30 -69 55 -126 80 -180 11 -26 21 -51 21 -55 0 -5 9 -19 19 -32l18 -24 22 49c13 27 31 67 42 90 10 23 19 44 19 47 0 3 16 39 35 80 19 41 35 80 35 86 0 7 5 9 12 5 7 -4 8 -3 4 4 -4 6 4 33 17 59 13 26 30 65 36 86 8 22 17 34 23 31 6 -4 8 -2 5 3 -4 6 34 101 83 212 50 111 90 207 90 212 0 6 5 7 12 3 7 -4 8 -3 4 4 -4 7 -1 24 7 39 22 41 103 225 111 251 4 12 12 19 18 16 6 -4 8 -2 5 3 -5 8 34 113 49 132 7 8 105 -32 167 -68l67 -39 45 56c25 31 45 60 45 64 0 20 -127 116 -202 153 -49 24 -88 42 -88 40 0 -2 -21 3 -47 10 -49 15 -224 27 -278 19z"/></g></svg>
                    <span class="nav-footer__logo-name"><?php echo esc_html(get_bloginfo('name')); ?></span>
                </a>
                <p class="nav-footer__tagline">
                    Research peptide compounds with verified certificates of analysis. Supplied for controlled laboratory environments.
                </p>
            </div>

            <div class="nav-footer__col">
                <h4 class="nav-footer__heading">
                    <span class="nav-footer__heading-mark" aria-hidden="true">
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><path d="M7 3h6M8 3v5L4 16a2 2 0 001.6 3h8.8a2 2 0 001.6-3L12 8V3"/><path d="M6 13h8"/></svg>
                    </span>
                    <?php esc_html_e('Compounds', 'navigate-peptides'); ?>
                </h4>
                <ul class="nav-footer__links">
                    <li><a href="<?php echo esc_url(home_url('/compounds/')); ?>"><?php esc_html_e('All Categories', 'navigate-peptides'); ?></a></li>
                    <?php
                    $cats = ['metabolic-research', 'cellular-research', 'tissue-repair-research', 'hormonal-signaling-research', 'cognitive-research', 'dermal-research'];
                    $cat_names = [__('Metabolic Research', 'navigate-peptides'), __('Cellular Research', 'navigate-peptides'), __('Tissue Repair Research', 'navigate-peptides'), __('Hormonal Signaling Research', 'navigate-peptides'), __('Cognitive Research', 'navigate-peptides'), __('Dermal Research', 'navigate-peptides')];
                    foreach ($cats as $i => $slug) :
                    ?>
                        <li><a href="<?php echo esc_url(nav_get_product_cat_url($slug)); ?>"><?php echo esc_html($cat_names[$i]); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="nav-footer__col">
                <h4 class="nav-footer__heading">
                    <span class="nav-footer__heading-mark" aria-hidden="true">
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><path d="M4 4h12v12H4z"/><path d="M8 8h4v4H8z"/><path d="M4 10h4M12 10h4M10 4v4M10 12v4"/></svg>
                    </span>
                    <?php esc_html_e('Research', 'navigate-peptides'); ?>
                </h4>
                <ul class="nav-footer__links">
                    <li><a href="<?php echo esc_url(home_url('/research/')); ?>"><?php esc_html_e('Research Hub', 'navigate-peptides'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/research/topic/intelligence/')); ?>"><?php esc_html_e('Research Intelligence', 'navigate-peptides'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/research/topic/library/')); ?>"><?php esc_html_e('Research Library', 'navigate-peptides'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/research/topic/framework/')); ?>"><?php esc_html_e('Research Framework', 'navigate-peptides'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/research/topic/emerging/')); ?>"><?php esc_html_e('Emerging Research', 'navigate-peptides'); ?></a></li>
                </ul>
            </div>

            <div class="nav-footer__col">
                <h4 class="nav-footer__heading">
                    <span class="nav-footer__heading-mark" aria-hidden="true">
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><path d="M10 2l7 4v8l-7 4-7-4V6l7-4z"/><path d="M10 10l7-4M10 10l-7-4M10 10v8"/></svg>
                    </span>
                    <?php esc_html_e('Quality', 'navigate-peptides'); ?>
                </h4>
                <ul class="nav-footer__links">
                    <li><a href="<?php echo esc_url(home_url('/quality/')); ?>"><?php esc_html_e('Quality Overview', 'navigate-peptides'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/quality/testing/')); ?>"><?php esc_html_e('Testing & Verification', 'navigate-peptides'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/quality/coa/')); ?>"><?php esc_html_e('Lab Results / COA', 'navigate-peptides'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/quality/manufacturing/')); ?>"><?php esc_html_e('Manufacturing Standards', 'navigate-peptides'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/quality/handling/')); ?>"><?php esc_html_e('Handling & Storage', 'navigate-peptides'); ?></a></li>
                </ul>
            </div>

            <div class="nav-footer__col">
                <h4 class="nav-footer__heading">
                    <span class="nav-footer__heading-mark" aria-hidden="true">
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><path d="M3 17h14M4 4h12v13H4z"/><path d="M7 9h2M7 13h2M11 9h2M11 13h2M9 4v3h2V4"/></svg>
                    </span>
                    <?php esc_html_e('Company', 'navigate-peptides'); ?>
                </h4>
                <ul class="nav-footer__links">
                    <li><a href="<?php echo esc_url(home_url('/about/')); ?>"><?php esc_html_e('About', 'navigate-peptides'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/about/standards/')); ?>"><?php esc_html_e('Standards', 'navigate-peptides'); ?></a></li>
                    <li><a href="<?php echo esc_url(nav_get_contact_url()); ?>"><?php esc_html_e('Contact / Request Access', 'navigate-peptides'); ?></a></li>
                </ul>
            </div>
        </div>

        <!-- Newsletter signup — posts to /wp-json/nav/v1/subscribe -->
        <section class="nav-newsletter" aria-labelledby="nav-newsletter-title">
            <div class="nav-newsletter__copy">
                <h3 id="nav-newsletter-title" class="nav-newsletter__title">
                    <?php esc_html_e('Research briefing', 'navigate-peptides'); ?>
                </h3>
                <p class="nav-newsletter__lede">
                    <?php esc_html_e('New compound additions, COA releases, and research commentary — no promotions, no hype.', 'navigate-peptides'); ?>
                </p>
            </div>
            <form class="nav-newsletter__form" data-nav-subscribe novalidate>
                <label class="screen-reader-text" for="nav-newsletter-email"><?php esc_html_e('Email address', 'navigate-peptides'); ?></label>
                <input
                    id="nav-newsletter-email"
                    type="email"
                    name="email"
                    class="nav-newsletter__input"
                    placeholder="<?php esc_attr_e('you@lab.edu', 'navigate-peptides'); ?>"
                    required
                    autocomplete="email"
                    inputmode="email"
                >
                <!-- Honeypot — hidden from humans; bots often fill every text field -->
                <input
                    type="text"
                    name="nav_hp"
                    tabindex="-1"
                    autocomplete="off"
                    aria-hidden="true"
                    class="nav-newsletter__hp"
                >
                <button type="submit" class="nav-newsletter__submit">
                    <span class="nav-newsletter__submit-label"><?php esc_html_e('Subscribe', 'navigate-peptides'); ?></span>
                    <span aria-hidden="true">→</span>
                </button>
                <p class="nav-newsletter__feedback" role="status" aria-live="polite"></p>
                <p class="nav-newsletter__disclaimer">
                    <?php
                    $privacy_url = function_exists('nav_privacy_url') ? nav_privacy_url() : '';
                    if ($privacy_url) {
                        echo wp_kses(
                            sprintf(
                                /* translators: %s: privacy policy link */
                                __('By subscribing you agree to receive occasional research emails and our %s. Unsubscribe anytime.', 'navigate-peptides'),
                                '<a href="' . esc_url($privacy_url) . '">' . esc_html__('Privacy Policy', 'navigate-peptides') . '</a>'
                            ),
                            ['a' => ['href' => []]]
                        );
                    } else {
                        esc_html_e('By subscribing you agree to receive occasional research emails. Unsubscribe anytime.', 'navigate-peptides');
                    }
                    ?>
                </p>
            </form>
        </section>

        <!-- Divider -->
        <hr class="nav-footer__divider">

        <!-- Accepted Payment Methods — trust signal, compliance-safe.
             Real brand marks read as more legitimate than text labels. -->
        <div class="nav-footer__payments" aria-label="<?php esc_attr_e('Accepted payment methods', 'navigate-peptides'); ?>">
            <?php
            $nav_payments = [
                'visa'       => __('Visa', 'navigate-peptides'),
                'mastercard' => __('Mastercard', 'navigate-peptides'),
                'amex'       => __('American Express', 'navigate-peptides'),
                'discover'   => __('Discover', 'navigate-peptides'),
                'bitcoin'    => __('Bitcoin', 'navigate-peptides'),
                'ethereum'   => __('Ethereum', 'navigate-peptides'),
                'usdc'       => __('USD Coin', 'navigate-peptides'),
                'ach'        => __('ACH bank transfer', 'navigate-peptides'),
            ];
            $pay_ver = function_exists('nav_asset_version')
                ? nav_asset_version('assets/images/payments/visa.svg') : '';
            foreach ($nav_payments as $slug => $label) :
                $src = get_template_directory_uri() . '/assets/images/payments/' . $slug . '.svg'
                     . ($pay_ver ? '?v=' . $pay_ver : '');
            ?>
                <span class="nav-footer__payment-method" role="img" aria-label="<?php echo esc_attr($label); ?>">
                    <img
                        src="<?php echo esc_url($src); ?>"
                        alt="<?php echo esc_attr($label); ?>"
                        loading="lazy"
                        decoding="async"
                        width="52"
                        height="32"
                    >
                </span>
            <?php endforeach; ?>
        </div>

        <!-- Sitewide Compliance Disclaimer -->
        <div class="nav-footer__disclaimer">
            <p><?php echo esc_html(nav_get_disclaimer('sitewide')); ?></p>
        </div>

        <!-- Public-facing merchant identity. We surface only the brand
             DBA, the PO mailing address, and the support email — legal
             entity name + phone are kept private per merchant policy. -->
        <?php if (defined('NAV_BIZ_DBA')) : ?>
        <div class="nav-footer__identity" aria-label="<?php esc_attr_e('Merchant contact', 'navigate-peptides'); ?>">
            <p class="nav-footer__identity-line">
                <?php echo nav_business_address('<span class="nav-footer__identity-sep" aria-hidden="true">·</span>'); // phpcs:ignore WordPress.Security.EscapeOutput -- helper escapes ?>
            </p>
            <?php if (function_exists('nav_has_business_email') && nav_has_business_email()) : ?>
            <p class="nav-footer__identity-line">
                <a href="mailto:<?php echo esc_attr(NAV_BIZ_EMAIL); ?>" class="nav-footer__identity-email"><?php echo esc_html(NAV_BIZ_EMAIL); ?></a>
            </p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Bottom Bar -->
        <div class="nav-footer__bottom">
            <p>&copy; <?php echo esc_html(wp_date('Y')); ?>
                <?php echo esc_html(defined('NAV_BIZ_DBA') ? NAV_BIZ_DBA : get_bloginfo('name')); ?>.
                <?php esc_html_e('All rights reserved.', 'navigate-peptides'); ?>
            </p>
            <nav class="nav-footer__legal" aria-label="<?php esc_attr_e('Legal', 'navigate-peptides'); ?>">
                <?php
                $privacy  = function_exists('nav_privacy_url')  ? nav_privacy_url()  : '';
                $terms    = function_exists('nav_terms_url')    ? nav_terms_url()    : '';
                $refund   = function_exists('nav_refund_url')   ? nav_refund_url()   : home_url('/refund-policy/');
                $shipping = function_exists('nav_shipping_url') ? nav_shipping_url() : home_url('/shipping-policy/');
                if ($privacy) : ?>
                    <a href="<?php echo esc_url($privacy); ?>"><?php esc_html_e('Privacy', 'navigate-peptides'); ?></a>
                <?php endif;
                if ($terms) : ?>
                    <a href="<?php echo esc_url($terms); ?>"><?php esc_html_e('Terms', 'navigate-peptides'); ?></a>
                <?php endif;
                if ($refund) : ?>
                    <a href="<?php echo esc_url($refund); ?>"><?php esc_html_e('Refund Policy', 'navigate-peptides'); ?></a>
                <?php endif;
                if ($shipping) : ?>
                    <a href="<?php echo esc_url($shipping); ?>"><?php esc_html_e('Shipping Policy', 'navigate-peptides'); ?></a>
                <?php endif; ?>
                <a href="<?php echo esc_url(nav_get_contact_url()); ?>"><?php esc_html_e('Contact', 'navigate-peptides'); ?></a>
            </nav>
            <p class="nav-footer__ruo"><?php esc_html_e('Research Use Only', 'navigate-peptides'); ?></p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
