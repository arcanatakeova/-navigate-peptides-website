</main><!-- /.nav-main -->

<footer class="nav-footer">
    <div class="nav-container">

        <!-- Footer Navigation Grid -->
        <div class="nav-footer__grid">
            <div class="nav-footer__col">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-footer__logo">
                    <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="nav-footer__logo-icon"><path d="M16 3L27.3 9.5V22.5L16 29L4.7 22.5V9.5L16 3Z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/><path d="M10 13L16 9L22 13" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 13V19L16 23L22 19V13" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="16" cy="16" r="2" fill="currentColor" opacity="0.6"/></svg>
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
                    $cats = ['metabolic-research', 'tissue-repair-research', 'cognitive-research', 'inflammation-research', 'cellular-research', 'dermal-research', 'longevity-research', 'research-blends'];
                    $cat_names = [__('Metabolic Research', 'navigate-peptides'), __('Tissue Repair Research', 'navigate-peptides'), __('Cognitive Research', 'navigate-peptides'), __('Inflammation Research', 'navigate-peptides'), __('Cellular Research', 'navigate-peptides'), __('Dermal Research', 'navigate-peptides'), __('Longevity Research', 'navigate-peptides'), __('Research Blends', 'navigate-peptides')];
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
                    <?php esc_html_e('By subscribing you agree to receive occasional research emails. Unsubscribe anytime.', 'navigate-peptides'); ?>
                </p>
            </form>
        </section>

        <!-- Divider -->
        <hr class="nav-footer__divider">

        <!-- Accepted Payment Methods — trust signal, compliance-safe -->
        <div class="nav-footer__payments" aria-label="<?php esc_attr_e('Accepted payment methods', 'navigate-peptides'); ?>">
            <span class="nav-footer__payments-label"><?php esc_html_e('Accepted', 'navigate-peptides'); ?></span>
            <span class="nav-footer__payment-method">Visa</span>
            <span class="nav-footer__payment-method">Mastercard</span>
            <span class="nav-footer__payment-method">Amex</span>
            <span class="nav-footer__payment-method">Discover</span>
            <span class="nav-footer__payment-method">Bitcoin</span>
            <span class="nav-footer__payment-method">Ethereum</span>
            <span class="nav-footer__payment-method">USDC</span>
            <span class="nav-footer__payment-method">ACH</span>
        </div>

        <!-- Sitewide Compliance Disclaimer -->
        <div class="nav-footer__disclaimer">
            <p><?php echo esc_html(nav_get_disclaimer('sitewide')); ?></p>
        </div>

        <!-- Bottom Bar -->
        <div class="nav-footer__bottom">
            <p>&copy; <?php echo esc_html(wp_date('Y')); ?> <?php echo esc_html(get_bloginfo('name')); ?>. <?php esc_html_e('All rights reserved.', 'navigate-peptides'); ?></p>
            <nav class="nav-footer__legal" aria-label="<?php esc_attr_e('Legal', 'navigate-peptides'); ?>">
                <?php
                $privacy = function_exists('nav_privacy_url') ? nav_privacy_url() : '';
                $terms   = function_exists('nav_terms_url')   ? nav_terms_url()   : '';
                if ($privacy) : ?>
                    <a href="<?php echo esc_url($privacy); ?>"><?php esc_html_e('Privacy', 'navigate-peptides'); ?></a>
                <?php endif;
                if ($terms) : ?>
                    <a href="<?php echo esc_url($terms); ?>"><?php esc_html_e('Terms', 'navigate-peptides'); ?></a>
                <?php endif; ?>
                <a href="<?php echo esc_url(nav_get_contact_url()); ?>"><?php esc_html_e('Contact', 'navigate-peptides'); ?></a>
            </nav>
            <p class="nav-footer__ruo"><?php esc_html_e('Research Use Only', 'navigate-peptides'); ?></p>
        </div>

        <!-- Arcana Operations Developers — designed + developed by -->
        <div class="nav-footer__arcana">
            <?php
            if (function_exists('nav_render_arcana_credit')) {
                echo nav_render_arcana_credit(); // wp_kses-clean: hardcoded link, no user input
            }
            ?>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
