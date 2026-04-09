<?php
/**
 * Homepage Template
 *
 * @package NavigatePeptides
 */

get_header();
?>

<!-- Hero -->
<section class="nav-hero">
    <div class="nav-hero__bg"></div>
    <div class="nav-container nav-hero__inner">
        <div class="nav-hero__content">
            <span class="nav-kicker">Research Peptide Compounds</span>
            <h1 class="nav-hero__title">Advancing Peptide Research Through Precision and Purity</h1>
            <p class="nav-hero__subtitle">
                High-purity research compounds with verified certificates of analysis.
                Supporting scientific investigation through rigorous quality standards
                and transparent documentation.
            </p>
            <div class="nav-hero__actions">
                <a href="<?php echo esc_url(home_url('/compounds/')); ?>" class="nav-btn nav-btn--primary">
                    Browse Compounds
                </a>
                <a href="<?php echo esc_url(home_url('/quality/')); ?>" class="nav-btn nav-btn--outline">
                    View Quality Standards
                </a>
            </div>
        </div>
        <div class="nav-hero__visual">
            <div class="nav-hero__vial-placeholder">
                <div class="nav-hero__vial-inner">
                    <span class="nav-hero__vial-label">NP</span>
                </div>
                <div class="nav-hero__vial-info">
                    <span class="nav-kicker">Featured Compound</span>
                    <h3>BPC-157</h3>
                    <p>Synthetic Pentadecapeptide</p>
                    <span class="nav-mono">≥99% HPLC Verified</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Research Categories -->
<section class="nav-section">
    <div class="nav-container">
        <div class="nav-section__header">
            <span class="nav-kicker">Research Categories</span>
            <h2 class="nav-section__title">Compound Library</h2>
        </div>
        <div class="nav-category-grid">
            <?php
            $categories = [
                ['name' => 'Metabolic Research',     'slug' => 'metabolic-research',     'color' => '#2F4666', 'desc' => 'Peptides studied for metabolic pathway modulation and energy metabolism research.'],
                ['name' => 'Tissue Repair Research', 'slug' => 'tissue-repair-research', 'color' => '#9C843E', 'desc' => 'Compounds investigated for tissue regeneration and structural repair mechanisms.'],
                ['name' => 'Cognitive Research',     'slug' => 'cognitive-research',     'color' => '#5E507F', 'desc' => 'Peptides explored for neuroprotective pathways and cognitive function research.'],
                ['name' => 'Inflammation Research',  'slug' => 'inflammation-research',  'color' => '#4A141C', 'desc' => 'Compounds studied for inflammatory response modulation and immune signaling.'],
                ['name' => 'Cellular Research',      'slug' => 'cellular-research',      'color' => '#8E5660', 'desc' => 'Peptides investigated for cellular signaling pathways and proliferation mechanisms.'],
                ['name' => 'Dermal Research',        'slug' => 'dermal-research',        'color' => '#4A6B5F', 'desc' => 'Compounds explored for dermal tissue modeling and epidermal pathway analysis.'],
                ['name' => 'Research Blends',        'slug' => 'research-blends',        'color' => '#474C50', 'desc' => 'Multi-peptide formulations designed for synergistic pathway research applications.'],
            ];

            foreach ($categories as $cat) :
                $link = get_term_link($cat['slug'], 'product_cat');
                if (is_wp_error($link)) {
                    $link = home_url('/compounds/');
                }
            ?>
                <a href="<?php echo esc_url($link); ?>" class="nav-category-card" style="--cat-color: <?php echo esc_attr($cat['color']); ?>">
                    <div class="nav-category-card__bar"></div>
                    <div class="nav-category-card__body">
                        <h3 class="nav-category-card__title"><?php echo esc_html($cat['name']); ?></h3>
                        <p class="nav-category-card__desc"><?php echo esc_html($cat['desc']); ?></p>
                        <span class="nav-category-card__link">View Compounds →</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Trust Signals -->
<section class="nav-section nav-section--dark">
    <div class="nav-container">
        <div class="nav-section__header nav-section__header--center">
            <span class="nav-kicker">Our Approach</span>
            <h2 class="nav-section__title">Research-Grade Standards</h2>
        </div>
        <div class="nav-trust-grid">
            <div class="nav-trust-card">
                <div class="nav-trust-card__icon">
                    <?php echo nav_icon('flask', 'nav-icon nav-icon--lg'); ?>
                </div>
                <h3 class="nav-trust-card__title">Third-Party Tested</h3>
                <p class="nav-trust-card__desc">
                    Every batch undergoes independent HPLC and mass spectrometry analysis by accredited laboratories.
                </p>
            </div>
            <div class="nav-trust-card">
                <div class="nav-trust-card__icon">
                    <?php echo nav_icon('shield', 'nav-icon nav-icon--lg'); ?>
                </div>
                <h3 class="nav-trust-card__title">Laboratory Use Only</h3>
                <p class="nav-trust-card__desc">
                    All compounds are intended exclusively for laboratory research and identification purposes.
                </p>
            </div>
            <div class="nav-trust-card">
                <div class="nav-trust-card__icon">
                    <?php echo nav_icon('building', 'nav-icon nav-icon--lg'); ?>
                </div>
                <h3 class="nav-trust-card__title">GMP Compliant</h3>
                <p class="nav-trust-card__desc">
                    Synthesized in GMP-compliant facilities with validated production processes and environmental controls.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="nav-section nav-section--cta">
    <div class="nav-container nav-section--center">
        <h2 class="nav-section__title">Begin Your Research</h2>
        <p class="nav-section__subtitle">
            Access our full catalog of research-grade peptide compounds. All products include
            certificates of analysis and are intended for research purposes only.
        </p>
        <div class="nav-cta-actions">
            <a href="<?php echo esc_url(home_url('/compounds/')); ?>" class="nav-btn nav-btn--primary">
                View Compounds
            </a>
            <a href="<?php echo esc_url(home_url('/about/contact/')); ?>" class="nav-btn nav-btn--outline">
                Request Access
            </a>
        </div>
    </div>
</section>

<!-- Sitewide Disclaimer -->
<section class="nav-section nav-section--disclaimer">
    <div class="nav-container">
        <?php nav_sitewide_disclaimer(); ?>
    </div>
</section>

<?php get_footer(); ?>
