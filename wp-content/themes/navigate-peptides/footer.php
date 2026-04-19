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
                    Compounds
                </h4>
                <ul class="nav-footer__links">
                    <li><a href="<?php echo esc_url(home_url('/compounds/')); ?>">All Categories</a></li>
                    <?php
                    $cats = ['metabolic-research', 'tissue-repair-research', 'cognitive-research', 'inflammation-research', 'cellular-research', 'dermal-research', 'research-blends'];
                    $cat_names = ['Metabolic Research', 'Tissue Repair Research', 'Cognitive Research', 'Inflammation Research', 'Cellular Research', 'Dermal Research', 'Research Blends'];
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
                    Research
                </h4>
                <ul class="nav-footer__links">
                    <li><a href="<?php echo esc_url(home_url('/research/')); ?>">Research Hub</a></li>
                    <li><a href="<?php echo esc_url(home_url('/research/topic/intelligence/')); ?>">Research Intelligence</a></li>
                    <li><a href="<?php echo esc_url(home_url('/research/topic/library/')); ?>">Research Library</a></li>
                    <li><a href="<?php echo esc_url(home_url('/research/topic/framework/')); ?>">Research Framework</a></li>
                    <li><a href="<?php echo esc_url(home_url('/research/topic/emerging/')); ?>">Emerging Research</a></li>
                </ul>
            </div>

            <div class="nav-footer__col">
                <h4 class="nav-footer__heading">
                    <span class="nav-footer__heading-mark" aria-hidden="true">
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><path d="M10 2l7 4v8l-7 4-7-4V6l7-4z"/><path d="M10 10l7-4M10 10l-7-4M10 10v8"/></svg>
                    </span>
                    Quality
                </h4>
                <ul class="nav-footer__links">
                    <li><a href="<?php echo esc_url(home_url('/quality/')); ?>">Quality Overview</a></li>
                    <li><a href="<?php echo esc_url(home_url('/quality/testing/')); ?>">Testing &amp; Verification</a></li>
                    <li><a href="<?php echo esc_url(home_url('/quality/coa/')); ?>">Lab Results / COA</a></li>
                    <li><a href="<?php echo esc_url(home_url('/quality/manufacturing/')); ?>">Manufacturing Standards</a></li>
                    <li><a href="<?php echo esc_url(home_url('/quality/handling/')); ?>">Handling &amp; Storage</a></li>
                </ul>
            </div>

            <div class="nav-footer__col">
                <h4 class="nav-footer__heading">
                    <span class="nav-footer__heading-mark" aria-hidden="true">
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><path d="M3 17h14M4 4h12v13H4z"/><path d="M7 9h2M7 13h2M11 9h2M11 13h2M9 4v3h2V4"/></svg>
                    </span>
                    Company
                </h4>
                <ul class="nav-footer__links">
                    <li><a href="<?php echo esc_url(home_url('/about/')); ?>">About</a></li>
                    <li><a href="<?php echo esc_url(home_url('/about/standards/')); ?>">Standards</a></li>
                    <li><a href="<?php echo esc_url(nav_get_contact_url()); ?>">Contact / Request Access</a></li>
                </ul>
            </div>
        </div>

        <!-- Divider -->
        <hr class="nav-footer__divider">

        <!-- Sitewide Compliance Disclaimer -->
        <div class="nav-footer__disclaimer">
            <p><?php echo esc_html(nav_get_disclaimer('sitewide')); ?></p>
        </div>

        <!-- Bottom Bar -->
        <div class="nav-footer__bottom">
            <p>&copy; <?php echo esc_html(date('Y')); ?> <?php echo esc_html(get_bloginfo('name')); ?>. All rights reserved.</p>
            <p class="nav-footer__ruo">Research Use Only</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
