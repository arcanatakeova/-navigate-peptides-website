<?php
/**
 * 404 Page
 *
 * @package NavigatePeptides
 */

get_header();
?>

<section class="nav-page-hero">
    <div class="nav-container">
        <span class="nav-kicker nav-kicker--mono"><?php esc_html_e('Error 404', 'navigate-peptides'); ?></span>
        <h1 class="nav-page-hero__title"><?php esc_html_e('Page Not Found', 'navigate-peptides'); ?></h1>
        <p class="nav-page-hero__subtitle"><?php esc_html_e("The page you're looking for doesn't exist or has been moved. Try one of the popular destinations below.", 'navigate-peptides'); ?></p>
    </div>
</section>

<section class="nav-section">
    <div class="nav-container">
        <div class="nav-section__header">
            <h2 class="nav-section__title-caps"><?php esc_html_e('Popular Destinations', 'navigate-peptides'); ?></h2>
        </div>
        <div class="nav-card-grid nav-card-grid--3">
            <a href="<?php echo esc_url(home_url('/compounds/')); ?>" class="nav-link-card">
                <span class="nav-link-card__tag"><?php esc_html_e('Catalog', 'navigate-peptides'); ?></span>
                <h3 class="nav-link-card__title"><?php esc_html_e('Research Compounds', 'navigate-peptides'); ?></h3>
                <p class="nav-link-card__desc"><?php esc_html_e('Browse our full catalog of peptide compounds organized by research application.', 'navigate-peptides'); ?></p>
                <span class="nav-link-card__action"><?php esc_html_e('View catalog →', 'navigate-peptides'); ?></span>
            </a>
            <a href="<?php echo esc_url(home_url('/quality/')); ?>" class="nav-link-card">
                <span class="nav-link-card__tag"><?php esc_html_e('Verification', 'navigate-peptides'); ?></span>
                <h3 class="nav-link-card__title"><?php esc_html_e('Quality Standards', 'navigate-peptides'); ?></h3>
                <p class="nav-link-card__desc"><?php esc_html_e('Third-party testing, certificates of analysis, and manufacturing standards.', 'navigate-peptides'); ?></p>
                <span class="nav-link-card__action"><?php esc_html_e('Review standards →', 'navigate-peptides'); ?></span>
            </a>
            <a href="<?php echo esc_url(home_url('/research/')); ?>" class="nav-link-card">
                <span class="nav-link-card__tag"><?php esc_html_e('Resources', 'navigate-peptides'); ?></span>
                <h3 class="nav-link-card__title"><?php esc_html_e('Research Hub', 'navigate-peptides'); ?></h3>
                <p class="nav-link-card__desc"><?php esc_html_e('Scientific resources, pathway analysis, and referenced preclinical studies.', 'navigate-peptides'); ?></p>
                <span class="nav-link-card__action"><?php esc_html_e('Explore research →', 'navigate-peptides'); ?></span>
            </a>
        </div>

        <div class="nav-section__header" style="margin-top:64px;">
            <h2 class="nav-section__title-caps"><?php esc_html_e('Research Categories', 'navigate-peptides'); ?></h2>
        </div>
        <div class="nav-category-grid">
            <?php
            // Category names are pulled from the taxonomy at render time so
            // translations/edits in the admin propagate here. The slug/color
            // mapping is the only hardcoded part.
            $quick_cats = [
                ['slug' => 'metabolic-research',           'color' => '#3F6A8A', 'fallback' => __('Metabolic Research', 'navigate-peptides')],
                ['slug' => 'cellular-research',            'color' => '#4A6F5A', 'fallback' => __('Cellular Research', 'navigate-peptides')],
                ['slug' => 'tissue-repair-research',       'color' => '#A88E45', 'fallback' => __('Tissue Repair Research', 'navigate-peptides')],
                ['slug' => 'hormonal-signaling-research',  'color' => '#6B5A7A', 'fallback' => __('Hormonal Signaling Research', 'navigate-peptides')],
            ];
            foreach ($quick_cats as $cat) :
                $term = get_term_by('slug', $cat['slug'], 'product_cat');
                $name = ($term && !is_wp_error($term)) ? $term->name : $cat['fallback'];
                $link = get_term_link($cat['slug'], 'product_cat');
                if (is_wp_error($link)) $link = home_url('/compounds/');
            ?>
                <a href="<?php echo esc_url($link); ?>" class="nav-category-card" style="--cat-color: <?php echo esc_attr($cat['color']); ?>">
                    <div class="nav-category-card__bar"></div>
                    <div class="nav-category-card__body">
                        <h3 class="nav-category-card__title"><?php echo esc_html($name); ?></h3>
                        <span class="nav-category-card__link"><?php esc_html_e('View compounds →', 'navigate-peptides'); ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="nav-section--center" style="margin-top:64px;">
            <div class="nav-cta-actions nav-cta-actions--center">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-btn nav-btn--primary"><?php esc_html_e('Return Home', 'navigate-peptides'); ?></a>
                <a href="<?php echo esc_url(nav_get_contact_url()); ?>" class="nav-btn nav-btn--outline"><?php esc_html_e('Contact Support', 'navigate-peptides'); ?></a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
