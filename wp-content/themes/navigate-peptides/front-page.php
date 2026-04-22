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
            <span class="nav-kicker"><?php esc_html_e('Precision Peptide Research', 'navigate-peptides'); ?></span>
            <h1 class="nav-hero__title">
                <span class="nav-hero__title-line"><?php esc_html_e('Engineered.', 'navigate-peptides'); ?></span>
                <em class="nav-hero__title-italic"><?php esc_html_e('Intelligent.', 'navigate-peptides'); ?></em>
            </h1>
            <p class="nav-hero__subtitle">
                <?php esc_html_e('Research-grade peptide compounds for controlled laboratory investigation.', 'navigate-peptides'); ?>
            </p>
            <ul class="nav-hero__badges">
                <li>
                    <?php echo nav_icon('check', 'nav-icon nav-icon--sm'); ?>
                    <span><?php esc_html_e('Third-Party Tested', 'navigate-peptides'); ?></span>
                </li>
                <li>
                    <?php echo nav_icon('check', 'nav-icon nav-icon--sm'); ?>
                    <span><?php esc_html_e('COA Verified', 'navigate-peptides'); ?></span>
                </li>
            </ul>
            <div class="nav-hero__actions">
                <a href="<?php echo esc_url(home_url('/compounds/')); ?>" class="nav-btn nav-btn--primary">
                    <?php esc_html_e('View Compounds', 'navigate-peptides'); ?>
                </a>
                <a href="<?php echo esc_url(home_url('/quality/')); ?>" class="nav-btn nav-btn--outline">
                    <?php esc_html_e('Quality & Verification', 'navigate-peptides'); ?>
                </a>
            </div>
        </div>

        <!-- Center: 3D vial. The GLB is fully branded — glass body, amber
             crimp cap, lyophilized powder, Navigate Peptides paper label
             with wordmark + spec strip + RUO chip. No SVG fallback; if
             WebGL fails the container is empty (acceptable — the spec
             panel to the right carries the compound identity).
             Shipped compound is GHK-Cu; swapping vials requires swapping
             the spec block. -->
        <?php
        // Cache-bust the GLB by file mtime. Without this, browsers hold the
        // old model in disk cache after a redeploy (WordPress static assets
        // ship with long browser-cache TTLs) and users see stale geometry.
        $vial_ver = function_exists('nav_asset_version')
            ? nav_asset_version('assets/models/vial.glb')
            : '';
        $vial_url = $theme_uri . '/assets/models/vial.glb'
                  . ($vial_ver ? '?v=' . $vial_ver : '');
        ?>
        <div class="nav-hero__vial">
            <model-viewer
                src="<?php echo esc_url($vial_url); ?>"
                alt="Navigate Peptides GHK-Cu research vial — 3D interactive model"
                auto-rotate
                camera-controls
                interaction-prompt="none"
                rotation-per-second="32deg"
                camera-orbit="0deg 75deg 110%"
                min-camera-orbit="auto auto 80%"
                max-camera-orbit="auto auto 180%"
                environment-image="neutral"
                shadow-intensity="0.6"
                exposure="1.2"
                loading="eager"
            ></model-viewer>
            <a href="<?php echo esc_url(home_url('/product/ghk-cu/')); ?>"
               class="nav-hero__vial-cta">
                View GHK-Cu peptide <span aria-hidden="true">→</span>
            </a>
        </div>

        <!-- Right: Product Information Panel — GHK-Cu to match the rendered vial -->
        <div class="nav-hero__info-panel">
            <div class="nav-info-panel">
                <h2 class="nav-info-panel__title"><?php esc_html_e('Product Information', 'navigate-peptides'); ?></h2>
                <div class="nav-info-panel__grid">
                    <div class="nav-info-panel__row">
                        <span class="nav-info-panel__label"><?php esc_html_e('Compound', 'navigate-peptides'); ?></span>
                        <span class="nav-info-panel__value">GHK-Cu</span>
                    </div>
                    <div class="nav-info-panel__row">
                        <span class="nav-info-panel__label"><?php esc_html_e('Sequence', 'navigate-peptides'); ?></span>
                        <span class="nav-info-panel__value nav-info-panel__value--mono">Gly-His-Lys · Cu²⁺</span>
                    </div>
                    <div class="nav-info-panel__row">
                        <span class="nav-info-panel__label"><?php esc_html_e('Molecular Weight', 'navigate-peptides'); ?></span>
                        <span class="nav-info-panel__value">340.4 g/mol</span>
                    </div>
                    <div class="nav-info-panel__row">
                        <span class="nav-info-panel__label"><?php esc_html_e('Form', 'navigate-peptides'); ?></span>
                        <span class="nav-info-panel__value"><?php esc_html_e('Lyophilized Powder', 'navigate-peptides'); ?></span>
                    </div>
                    <div class="nav-info-panel__row">
                        <span class="nav-info-panel__label"><?php esc_html_e('Purity (HPLC)', 'navigate-peptides'); ?></span>
                        <span class="nav-info-panel__value">≥99%</span>
                    </div>
                    <div class="nav-info-panel__row">
                        <span class="nav-info-panel__label"><?php esc_html_e('Use', 'navigate-peptides'); ?></span>
                        <span class="nav-info-panel__value"><?php esc_html_e('Research Use Only', 'navigate-peptides'); ?><br><?php esc_html_e('Not for Human Consumption', 'navigate-peptides'); ?></span>
                    </div>
                </div>

                <!-- Batch + Purity Boxes — pulled from the GHK-Cu product's
                     admin-editable meta so batch rotations (monthly for QA)
                     don't leave stale values on the homepage. Falls back to
                     'Latest' / '≥99%' when no product or meta is configured. -->
                <?php
                $hero_batch  = 'Latest';
                $hero_purity = '≥99%';
                if (function_exists('wc_get_products')) {
                    $hero_products = wc_get_products([
                        'limit'    => 1,
                        'category' => ['cellular-research'],  // GHK-Cu's documented category
                        'status'   => 'publish',
                        'orderby'  => 'date',
                        'order'    => 'DESC',
                    ]);
                    if (!empty($hero_products[0])) {
                        $hid = $hero_products[0]->get_id();
                        $b = get_post_meta($hid, '_nav_batch_number', true);
                        $p = get_post_meta($hid, '_nav_purity', true);
                        if ($b) $hero_batch  = $b;
                        if ($p) $hero_purity = $p;
                    }
                }
                ?>
                <div class="nav-info-panel__boxes">
                    <div class="nav-info-panel__box">
                        <span class="nav-info-panel__box-label"><?php esc_html_e('Batch:', 'navigate-peptides'); ?></span>
                        <span class="nav-info-panel__box-value"><?php echo esc_html($hero_batch); ?></span>
                    </div>
                    <div class="nav-info-panel__box">
                        <span class="nav-info-panel__box-label"><?php esc_html_e('Purity:', 'navigate-peptides'); ?></span>
                        <span class="nav-info-panel__box-value"><?php echo esc_html($hero_purity); ?></span>
                    </div>
                </div>

                <!-- COA Link -->
                <a href="<?php echo esc_url(home_url('/quality/coa/')); ?>" class="nav-info-panel__coa-link">
                    <?php esc_html_e('COA & Analytical Data', 'navigate-peptides'); ?>
                </a>

                <!-- Storage -->
                <div class="nav-info-panel__footer">
                    <div class="nav-info-panel__section-label"><?php esc_html_e('Storage', 'navigate-peptides'); ?></div>
                    <p class="nav-info-panel__storage">
                        <?php esc_html_e('Store cold at 2-8°C.', 'navigate-peptides'); ?><br><?php esc_html_e('Protect from light and moisture.', 'navigate-peptides'); ?>
                    </p>
                    <div class="nav-info-panel__mfg">
                        <?php esc_html_e('Manufactured in a GMP-Compliant Facility', 'navigate-peptides'); ?>
                    </div>
                    <div class="nav-info-panel__brand">
                        <?php echo nav_icon('flask', 'nav-icon nav-icon--sm'); ?>
                        <span>Navigate Peptides</span>
                        <small><?php esc_html_e('Precision Peptide Research', 'navigate-peptides'); ?></small>
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
            <h2 class="nav-section__title-caps"><?php esc_html_e('Research Categories', 'navigate-peptides'); ?></h2>
        </div>
        <div class="nav-category-grid nav-category-grid--icons">
            <?php
            // Per-category scientific line-art icons matching Stephie's mockup v1.
            // Shown at 56px, stroked with the category's brand color.
            // Names are literal __() calls so makepot extracts them — a
            // dynamic __($var) wrapper is a no-op for the extractor and
            // these strings would otherwise never land in the .pot file.
            $categories = [
                // Cognitive — stylized brain in profile
                ['name' => __('Cognitive Research', 'navigate-peptides'),      'slug' => 'cognitive-research',     'color' => '#5E507F',
                 'icon' => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M23 10c-4-4-12-2-12 6 0 2 1 3 2 4-2 2-2 6 1 8-1 2 0 5 3 6 0 3 3 5 6 4 2 2 6 2 8-1V10"/><path d="M23 14c1 0 3 1 4 3M23 22c-2 0-3 0-4 2M19 32c1-1 3-1 4-1M15 26c1-1 2-1 3 0"/></svg>'],

                // Tissue Repair — overlapping suture / knit lines (cross + hairlines)
                ['name' => __('Tissue Repair Research', 'navigate-peptides'),  'slug' => 'tissue-repair-research', 'color' => '#9C843E',
                 'icon' => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M10 20h12v-8h4v8h12v4H26v16h-4V24H10z"/><path d="M6 14l4 4M42 14l-4 4M6 34l4-4M42 34l-4-4"/></svg>'],

                // Inflammation — teardrop with inner pulse
                ['name' => __('Inflammation Research', 'navigate-peptides'),   'slug' => 'inflammation-research',  'color' => '#4A141C',
                 'icon' => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M24 6c0 0-10 12-10 20a10 10 0 0020 0c0-8-10-20-10-20z"/><path d="M19 26c0 3 2 5 5 5"/></svg>'],

                // Cellular — cell with radiating field (organelles)
                ['name' => __('Cellular Research', 'navigate-peptides'),       'slug' => 'cellular-research',      'color' => '#8E5660',
                 'icon' => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="24" cy="24" r="14"/><circle cx="24" cy="24" r="4"/><circle cx="18" cy="18" r="1.5"/><circle cx="31" cy="18" r="1.5"/><circle cx="30" cy="31" r="1.5"/><circle cx="17" cy="29" r="1.5"/><path d="M24 4v3M24 41v3M4 24h3M41 24h3M10 10l2 2M36 36l2 2M10 38l2-2M36 12l2-2"/></svg>'],

                // Dermal — layered skin / profile with hair strands
                ['name' => __('Dermal Research', 'navigate-peptides'),         'slug' => 'dermal-research',        'color' => '#4A6B5F',
                 'icon' => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M30 10c-6-2-12 1-14 7-2 6 0 12 5 16 2 2 2 5 0 7"/><path d="M32 14c4 3 6 9 3 15-1 2-1 4 1 6"/><path d="M22 20c2 0 3 1 3 3M18 28c2 0 3 1 3 3"/></svg>'],

                // Metabolic — flask with droplets (mitochondrial flow)
                ['name' => __('Metabolic Research', 'navigate-peptides'),      'slug' => 'metabolic-research',     'color' => '#2F4666',
                 'icon' => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6h12M20 6v14L10 38a3 3 0 002.5 5h23A3 3 0 0038 38L28 20V6"/><path d="M14 30h20"/><circle cx="22" cy="35" r="1.5" fill="currentColor"/><circle cx="28" cy="33" r="1" fill="currentColor"/></svg>'],

                // Longevity — hourglass / bidirectional arrow (time + lifespan)
                ['name' => __('Longevity Research', 'navigate-peptides'),      'slug' => 'longevity-research',     'color' => '#2E5C6A',
                 'icon' => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 6h24M12 42h24M14 6c0 6 3 10 10 16 7-6 10-10 10-16M14 42c0-6 3-10 10-16 7 6 10 10 10 16"/><circle cx="24" cy="24" r="1.5" fill="currentColor"/></svg>'],

                // Research Blends — diamond / prism (multi-compound blend)
                ['name' => __('Research Blends', 'navigate-peptides'),         'slug' => 'research-blends',        'color' => '#474C50',
                 'icon' => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M24 4L8 18 24 44 40 18 24 4z"/><path d="M8 18h32M16 18L24 4M32 18L24 4M16 18L24 44M32 18L24 44"/></svg>'],
            ];

            foreach ($categories as $cat) :
                $link = nav_get_product_cat_url($cat['slug']);
            ?>
                <a href="<?php echo esc_url($link); ?>" class="nav-cat-icon-card" style="--cat-color: <?php echo esc_attr($cat['color']); ?>">
                    <div class="nav-cat-icon-card__icon">
                        <?php echo nav_kses_svg($cat['icon']); ?>
                    </div>
                    <h3 class="nav-cat-icon-card__title"><?php echo esc_html($cat['name']); ?></h3>
                    <span class="nav-cat-icon-card__btn"><?php esc_html_e('View', 'navigate-peptides'); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Trust Signals -->
<section class="nav-section nav-section--dark" aria-labelledby="nav-trust-heading">
    <div class="nav-container">
        <h2 id="nav-trust-heading" class="screen-reader-text"><?php esc_html_e('Quality and trust', 'navigate-peptides'); ?></h2>
        <div class="nav-trust-grid">
            <div class="nav-trust-card">
                <div class="nav-trust-card__icon">
                    <?php echo nav_icon('check', 'nav-icon nav-icon--lg'); ?>
                </div>
                <div class="nav-trust-card__content">
                    <h3 class="nav-trust-card__title"><?php esc_html_e('Third-Party Tested', 'navigate-peptides'); ?></h3>
                    <p class="nav-trust-card__desc">
                        <?php esc_html_e('Every batch verified for purity and composition.', 'navigate-peptides'); ?>
                    </p>
                </div>
            </div>
            <div class="nav-trust-card">
                <div class="nav-trust-card__icon">
                    <?php echo nav_icon('flask', 'nav-icon nav-icon--lg'); ?>
                </div>
                <div class="nav-trust-card__content">
                    <h3 class="nav-trust-card__title"><?php esc_html_e('Laboratory Use Only', 'navigate-peptides'); ?></h3>
                    <p class="nav-trust-card__desc">
                        <?php esc_html_e('Not for human consumption. For research purposes exclusively.', 'navigate-peptides'); ?>
                    </p>
                </div>
            </div>
            <div class="nav-trust-card">
                <div class="nav-trust-card__icon">
                    <?php echo nav_icon('building', 'nav-icon nav-icon--lg'); ?>
                </div>
                <div class="nav-trust-card__content">
                    <h3 class="nav-trust-card__title"><?php esc_html_e('GMP Compliant', 'navigate-peptides'); ?></h3>
                    <p class="nav-trust-card__desc">
                        <?php esc_html_e('Manufactured to the highest quality standards.', 'navigate-peptides'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="nav-section nav-section--cta">
    <div class="nav-container nav-section--center">
        <h2 class="nav-section__title"><?php esc_html_e('Begin Your Research', 'navigate-peptides'); ?></h2>
        <p class="nav-section__subtitle">
            <?php esc_html_e('Browse our full catalog directly. All compounds include batch-specific certificates of analysis and ship under controlled laboratory handling. Institutional or wholesale volumes — use the inquiry form below.', 'navigate-peptides'); ?>
        </p>
        <div class="nav-cta-actions">
            <a href="<?php echo esc_url(home_url('/compounds/')); ?>" class="nav-btn nav-btn--primary">
                <?php esc_html_e('Browse Compounds', 'navigate-peptides'); ?>
            </a>
            <a href="<?php echo esc_url(nav_get_contact_url()); ?>" class="nav-btn nav-btn--outline">
                <?php esc_html_e('Wholesale Inquiry', 'navigate-peptides'); ?>
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
