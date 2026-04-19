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
        <span class="nav-kicker nav-kicker--mono">Error 404</span>
        <h1 class="nav-page-hero__title">Page Not Found</h1>
        <p class="nav-page-hero__subtitle">The page you're looking for doesn't exist or has been moved. Try one of the popular destinations below.</p>
    </div>
</section>

<section class="nav-section">
    <div class="nav-container">
        <div class="nav-section__header">
            <h2 class="nav-section__title-caps">Popular Destinations</h2>
        </div>
        <div class="nav-card-grid nav-card-grid--3">
            <a href="<?php echo esc_url(home_url('/compounds/')); ?>" class="nav-link-card">
                <span class="nav-link-card__tag">Catalog</span>
                <h3 class="nav-link-card__title">Research Compounds</h3>
                <p class="nav-link-card__desc">Browse our full catalog of peptide compounds organized by research application.</p>
                <span class="nav-link-card__action">View catalog →</span>
            </a>
            <a href="<?php echo esc_url(home_url('/quality/')); ?>" class="nav-link-card">
                <span class="nav-link-card__tag">Verification</span>
                <h3 class="nav-link-card__title">Quality Standards</h3>
                <p class="nav-link-card__desc">Third-party testing, certificates of analysis, and manufacturing standards.</p>
                <span class="nav-link-card__action">Review standards →</span>
            </a>
            <a href="<?php echo esc_url(home_url('/research/')); ?>" class="nav-link-card">
                <span class="nav-link-card__tag">Resources</span>
                <h3 class="nav-link-card__title">Research Hub</h3>
                <p class="nav-link-card__desc">Scientific resources, pathway analysis, and referenced preclinical studies.</p>
                <span class="nav-link-card__action">Explore research →</span>
            </a>
        </div>

        <div class="nav-section__header" style="margin-top:64px;">
            <h2 class="nav-section__title-caps">Research Categories</h2>
        </div>
        <div class="nav-category-grid">
            <?php
            $quick_cats = [
                ['name' => 'Metabolic Research',     'slug' => 'metabolic-research',     'color' => '#2F4666'],
                ['name' => 'Tissue Repair Research', 'slug' => 'tissue-repair-research', 'color' => '#9C843E'],
                ['name' => 'Cognitive Research',     'slug' => 'cognitive-research',     'color' => '#5E507F'],
                ['name' => 'Inflammation Research',  'slug' => 'inflammation-research',  'color' => '#4A141C'],
            ];
            foreach ($quick_cats as $cat) :
                $link = get_term_link($cat['slug'], 'product_cat');
                if (is_wp_error($link)) $link = home_url('/compounds/');
            ?>
                <a href="<?php echo esc_url($link); ?>" class="nav-category-card" style="--cat-color: <?php echo esc_attr($cat['color']); ?>">
                    <div class="nav-category-card__bar"></div>
                    <div class="nav-category-card__body">
                        <h3 class="nav-category-card__title"><?php echo esc_html($cat['name']); ?></h3>
                        <span class="nav-category-card__link">View compounds →</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="nav-section--center" style="margin-top:64px;">
            <div class="nav-cta-actions nav-cta-actions--center">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-btn nav-btn--primary">Return Home</a>
                <a href="<?php echo esc_url(nav_get_contact_url()); ?>" class="nav-btn nav-btn--outline">Contact Support</a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
