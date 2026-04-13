</main><!-- /.nav-main -->

<footer class="nav-footer">
    <div class="nav-container">

        <!-- Footer Navigation Grid -->
        <div class="nav-footer__grid">
            <div class="nav-footer__col">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-footer__logo">
                    <span class="nav-header__logo-mark">NP</span>
                    <span class="nav-header__logo-text"><?php echo esc_html(get_bloginfo('name')); ?></span>
                </a>
                <p class="nav-footer__tagline">
                    Research peptide compounds with verified certificates of analysis.
                </p>
            </div>

            <div class="nav-footer__col">
                <h4 class="nav-footer__heading">Compounds</h4>
                <ul class="nav-footer__links">
                    <li><a href="<?php echo esc_url(home_url('/compounds/')); ?>">All Categories</a></li>
                    <?php
                    $cats = ['metabolic-research', 'tissue-repair-research', 'cognitive-research', 'inflammation-research', 'cellular-research', 'dermal-research', 'research-blends'];
                    $cat_names = ['Metabolic Research', 'Tissue Repair Research', 'Cognitive Research', 'Inflammation Research', 'Cellular Research', 'Dermal Research', 'Research Blends'];
                    foreach ($cats as $i => $slug) :
                        $link = get_term_link($slug, 'product_cat');
                        if (!is_wp_error($link)) :
                    ?>
                        <li><a href="<?php echo esc_url($link); ?>"><?php echo esc_html($cat_names[$i]); ?></a></li>
                    <?php
                        endif;
                    endforeach;
                    ?>
                </ul>
            </div>

            <div class="nav-footer__col">
                <h4 class="nav-footer__heading">Research</h4>
                <ul class="nav-footer__links">
                    <li><a href="<?php echo esc_url(home_url('/research/')); ?>">Research Hub</a></li>
                    <li><a href="<?php echo esc_url(home_url('/research/category/intelligence/')); ?>">Research Intelligence</a></li>
                    <li><a href="<?php echo esc_url(home_url('/research/category/library/')); ?>">Research Library</a></li>
                    <li><a href="<?php echo esc_url(home_url('/research/category/framework/')); ?>">Research Framework</a></li>
                    <li><a href="<?php echo esc_url(home_url('/research/category/emerging/')); ?>">Emerging Research</a></li>
                </ul>
            </div>

            <div class="nav-footer__col">
                <h4 class="nav-footer__heading">Quality</h4>
                <ul class="nav-footer__links">
                    <li><a href="<?php echo esc_url(home_url('/quality/')); ?>">Quality Overview</a></li>
                    <li><a href="<?php echo esc_url(home_url('/quality/testing/')); ?>">Testing &amp; Verification</a></li>
                    <li><a href="<?php echo esc_url(home_url('/quality/coa/')); ?>">Lab Results / COA</a></li>
                    <li><a href="<?php echo esc_url(home_url('/quality/manufacturing/')); ?>">Manufacturing Standards</a></li>
                    <li><a href="<?php echo esc_url(home_url('/quality/handling/')); ?>">Handling &amp; Storage</a></li>
                </ul>
            </div>

            <div class="nav-footer__col">
                <h4 class="nav-footer__heading">Company</h4>
                <ul class="nav-footer__links">
                    <li><a href="<?php echo esc_url(home_url('/about/')); ?>">About</a></li>
                    <li><a href="<?php echo esc_url(home_url('/about/standards/')); ?>">Standards</a></li>
                    <li><a href="<?php echo esc_url(home_url('/about/contact/')); ?>">Contact / Request Access</a></li>
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
