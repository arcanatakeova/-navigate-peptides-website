<?php
/**
 * Homepage Template — Matches Stephie's approved mockup.
 *
 * @package NavigatePeptides
 */

get_header();
$theme_uri = get_template_directory_uri();
?>

<!-- Hero: 3D Vial + Product Info Panel -->
<section class="nav-hero">
    <div class="nav-hero__bg"></div>
    <div class="nav-container nav-hero__inner">

        <!-- Left: Headline + CTA -->
        <div class="nav-hero__content">
            <span class="nav-kicker">Precision Peptide Research</span>
            <h1 class="nav-hero__title">
                <span class="nav-hero__title-line">Engineered.</span>
                <em class="nav-hero__title-italic">Intelligent.</em>
            </h1>
            <p class="nav-hero__subtitle">
                Premium peptides for research applications.
            </p>
            <ul class="nav-hero__badges">
                <li>
                    <?php echo nav_icon('check', 'nav-icon nav-icon--sm'); ?>
                    <span>Third-Party Tested</span>
                </li>
                <li>
                    <?php echo nav_icon('check', 'nav-icon nav-icon--sm'); ?>
                    <span>COA Verified</span>
                </li>
            </ul>
            <div class="nav-hero__actions">
                <a href="<?php echo esc_url(home_url('/compounds/')); ?>" class="nav-btn nav-btn--primary">
                    View Compounds
                </a>
                <a href="<?php echo esc_url(home_url('/quality/')); ?>" class="nav-btn nav-btn--outline">
                    Quality &amp; Verification
                </a>
            </div>
        </div>

        <!-- Center: 3D Vial (model-viewer or fallback image) -->
        <div class="nav-hero__vial">
            <model-viewer
                src="<?php echo esc_url($theme_uri . '/assets/models/vial.glb'); ?>"
                alt="Navigate Peptides BPC-157 research vial — 3D interactive model"
                auto-rotate
                camera-controls
                interaction-prompt="none"
                rotation-per-second="12deg"
                camera-orbit="20deg 75deg 105%"
                min-camera-orbit="auto auto 80%"
                max-camera-orbit="auto auto 150%"
                environment-image="neutral"
                shadow-intensity="0.4"
                exposure="1.1"
                style="width:100%;height:100%;"
                loading="eager"
            >
                <!-- Fallback for no WebGL -->
                <img
                    slot="poster"
                    src="<?php echo esc_url($theme_uri . '/assets/images/vial-ghkcu-single.png'); ?>"
                    alt="Navigate Peptides GHK-Cu research vial"
                >
            </model-viewer>
        </div>

        <!-- Right: Product Information Panel -->
        <div class="nav-hero__info-panel">
            <div class="nav-info-panel">
                <h3 class="nav-info-panel__title">Product Information</h3>
                <div class="nav-info-panel__grid">
                    <div class="nav-info-panel__row">
                        <span class="nav-info-panel__label">Compound</span>
                        <span class="nav-info-panel__value">BPC-157</span>
                    </div>
                    <div class="nav-info-panel__row">
                        <span class="nav-info-panel__label">Sequence</span>
                        <span class="nav-info-panel__value nav-info-panel__value--mono">H-Gly-Glu-Pro-Pro-Pro-Gly-Lys-Pro-Ala-Asp-Asp-Ala-Gly-Leu-Val</span>
                    </div>
                    <div class="nav-info-panel__row">
                        <span class="nav-info-panel__label">Molecular Weight</span>
                        <span class="nav-info-panel__value">1419.5 g/mol</span>
                    </div>
                    <div class="nav-info-panel__row">
                        <span class="nav-info-panel__label">Form</span>
                        <span class="nav-info-panel__value">Lyophilized Powder</span>
                    </div>
                    <div class="nav-info-panel__row">
                        <span class="nav-info-panel__label">Purity (HPLC)</span>
                        <span class="nav-info-panel__value">≥99%</span>
                    </div>
                    <div class="nav-info-panel__row">
                        <span class="nav-info-panel__label">Use</span>
                        <span class="nav-info-panel__value">Research Use Only<br>Not for Human Consumption</span>
                    </div>
                </div>

                <!-- Batch + Purity Boxes -->
                <div class="nav-info-panel__boxes">
                    <div class="nav-info-panel__box">
                        <span class="nav-info-panel__box-label">Batch:</span>
                        <span class="nav-info-panel__box-value">Q247A</span>
                    </div>
                    <div class="nav-info-panel__box">
                        <span class="nav-info-panel__box-label">Purity:</span>
                        <span class="nav-info-panel__box-value">≥99%</span>
                    </div>
                </div>

                <!-- Storage -->
                <div class="nav-info-panel__footer">
                    <div class="nav-info-panel__row">
                        <span class="nav-info-panel__label">Storage</span>
                        <span class="nav-info-panel__value">Store cold at 2-8°C.<br>Protect from light and moisture.</span>
                    </div>
                    <div class="nav-info-panel__mfg">
                        Manufactured in a GMP-Compliant Facility
                    </div>
                    <div class="nav-info-panel__brand">
                        <?php echo nav_icon('flask', 'nav-icon nav-icon--sm'); ?>
                        <span>Navigate Peptides</span>
                        <small>Precision Peptide Research</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Research Categories with Icons -->
<section class="nav-section">
    <div class="nav-container">
        <div class="nav-section__header nav-section__header--center">
            <h2 class="nav-section__title-caps">Research Categories</h2>
        </div>
        <div class="nav-category-grid nav-category-grid--7">
            <?php
            $categories = [
                ['name' => 'Cognitive Research',      'slug' => 'cognitive-research',     'color' => '#5E507F',
                 'icon' => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M24 4C14 4 8 12 8 20c0 5 2 9 6 12v8a4 4 0 004 4h12a4 4 0 004-4v-8c4-3 6-7 6-12 0-8-6-16-16-16z"/><path d="M18 44h12M20 36v-4l-4-4M28 36v-4l4-4M24 20v8M20 24h8"/></svg>'],
                ['name' => 'Tissue Repair Research',  'slug' => 'tissue-repair-research', 'color' => '#9C843E',
                 'icon' => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M24 8v32M8 24h32"/><path d="M18 14l6-6 6 6M30 34l-6 6-6-6M14 30l-6-6 6-6M34 18l6 6-6 6"/></svg>'],
                ['name' => 'Inflammation Research',   'slug' => 'inflammation-research',  'color' => '#4A141C',
                 'icon' => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M24 4c0 8-8 12-8 20a8 8 0 0016 0c0-8-8-12-8-20z"/><circle cx="24" cy="28" r="3"/></svg>'],
                ['name' => 'Cellular Research',       'slug' => 'cellular-research',      'color' => '#8E5660',
                 'icon' => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="24" cy="24" r="16"/><circle cx="24" cy="24" r="6"/><path d="M24 8v4M24 36v4M8 24h4M36 24h4M12 12l3 3M33 33l3 3M12 36l3-3M33 15l3-3"/></svg>'],
                ['name' => 'Dermal Research',         'slug' => 'dermal-research',        'color' => '#4A6B5F',
                 'icon' => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 8c0 0 4 4 4 12s-4 12-4 12"/><path d="M20 8c0 0 4 4 4 12s-4 12-4 12"/><path d="M28 8c0 0 4 4 4 12s-4 12-4 12"/><path d="M36 8c0 0 4 4 4 12s-4 12-4 12"/></svg>'],
                ['name' => 'Metabolic Research',      'slug' => 'metabolic-research',     'color' => '#2F4666',
                 'icon' => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 44h20"/><path d="M18 4v14l-8 18a4 4 0 004 4h20a4 4 0 004-4l-8-18V4"/><path d="M16 4h16"/><path d="M14 30h20"/><circle cx="22" cy="34" r="2"/><circle cx="30" cy="32" r="1.5"/></svg>'],
                ['name' => 'Research Blends',         'slug' => 'research-blends',        'color' => '#474C50',
                 'icon' => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 24l8-8 8 8 8-8 8 8"/><path d="M8 32l8-8 8 8 8-8 8 8"/><circle cx="16" cy="16" r="4"/><circle cx="32" cy="16" r="4"/></svg>'],
            ];

            foreach ($categories as $cat) :
                $link = get_term_link($cat['slug'], 'product_cat');
                if (is_wp_error($link)) $link = home_url('/compounds/');
            ?>
                <a href="<?php echo esc_url($link); ?>" class="nav-cat-icon-card" style="--cat-color: <?php echo esc_attr($cat['color']); ?>">
                    <div class="nav-cat-icon-card__icon">
                        <?php echo $cat['icon']; ?>
                    </div>
                    <h3 class="nav-cat-icon-card__title"><?php echo esc_html($cat['name']); ?></h3>
                    <span class="nav-cat-icon-card__btn">View</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Trust Signals -->
<section class="nav-section nav-section--dark">
    <div class="nav-container">
        <div class="nav-trust-grid">
            <div class="nav-trust-card">
                <div class="nav-trust-card__icon">
                    <?php echo nav_icon('check', 'nav-icon nav-icon--lg'); ?>
                </div>
                <h3 class="nav-trust-card__title">Third-Party Tested</h3>
                <p class="nav-trust-card__desc">
                    Every batch verified for purity and composition.
                </p>
            </div>
            <div class="nav-trust-card">
                <div class="nav-trust-card__icon">
                    <?php echo nav_icon('flask', 'nav-icon nav-icon--lg'); ?>
                </div>
                <h3 class="nav-trust-card__title">Laboratory Use Only</h3>
                <p class="nav-trust-card__desc">
                    Not for human consumption. For research purposes exclusively.
                </p>
            </div>
            <div class="nav-trust-card">
                <div class="nav-trust-card__icon">
                    <?php echo nav_icon('building', 'nav-icon nav-icon--lg'); ?>
                </div>
                <h3 class="nav-trust-card__title">GMP Compliant</h3>
                <p class="nav-trust-card__desc">
                    Manufactured to the highest quality standards.
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
