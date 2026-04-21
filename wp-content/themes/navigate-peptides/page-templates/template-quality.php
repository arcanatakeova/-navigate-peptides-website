<?php
/**
 * Template Name: Quality Overview
 *
 * @package NavigatePeptides
 */

get_header();
?>

<section class="nav-page-hero">
    <div class="nav-container">
        <h1 class="nav-page-hero__title">Quality Assurance</h1>
        <p class="nav-page-hero__subtitle">
            Every compound undergoes rigorous independent testing and verification. We publish certificates of analysis for complete transparency.
        </p>
    </div>
</section>

<section class="nav-section">
    <div class="nav-container">
        <!-- Interactive 3D vial trio — each compound has its own GLB
             with a compound-specific label. Users can rotate/zoom each
             vial individually. mtime-versioned so redeploys bust cache. -->
        <?php
        $theme_uri = get_template_directory_uri();
        // Each vial links to its WooCommerce product page. Uses the Woo
        // product slug where we have one; falls back to the compounds
        // archive filter. wc_get_page_permalink('shop') isn't useful here
        // — we want per-compound product URLs.
        $vials = [
            ['slug' => 'ghkcu',  'label' => 'GHK-Cu',  'desc' => 'Copper Tripeptide-1 · 340.4 g/mol',      'url' => '/product/ghk-cu/'],
            ['slug' => 'bpc157', 'label' => 'BPC-157', 'desc' => 'Body Protection Compound · 1419.5 g/mol', 'url' => '/product/bpc-157/'],
            ['slug' => 'tb500',  'label' => 'TB-500',  'desc' => 'Thymosin β-4 Fragment · 889.0 g/mol',    'url' => '/product/tb-500/'],
        ];
        ?>
        <div class="nav-vial-trio" aria-label="Interactive 3D peptide vial showcase">
            <?php foreach ($vials as $v):
                $rel = 'assets/models/vial-' . $v['slug'] . '.glb';
                $ver = function_exists('nav_asset_version') ? nav_asset_version($rel) : '';
                $src = $theme_uri . '/' . $rel . ($ver ? '?v=' . $ver : '');
            ?>
                <figure class="nav-vial-trio__cell">
                    <div class="nav-vial-trio__stage">
                    <model-viewer
                        class="nav-vial-trio__viewer"
                        src="<?php echo esc_url($src); ?>"
                        alt="<?php echo esc_attr($v['label']); ?> research vial — interactive 3D"
                        auto-rotate
                        camera-controls
                        interaction-prompt="none"
                        rotation-per-second="18deg"
                        camera-orbit="20deg 75deg 95%"
                        min-camera-orbit="auto auto 70%"
                        max-camera-orbit="auto auto 160%"
                        environment-image="neutral"
                        shadow-intensity="0.6"
                        exposure="1.15"
                        loading="eager"
                    ></model-viewer>
                    </div>
                    <figcaption class="nav-vial-trio__meta">
                        <span class="nav-vial-trio__name"><?php echo esc_html($v['label']); ?></span>
                        <span class="nav-vial-trio__desc"><?php echo esc_html($v['desc']); ?></span>
                        <a class="nav-vial-trio__cta" href="<?php echo esc_url(home_url($v['url'])); ?>">
                            View product <span aria-hidden="true">→</span>
                        </a>
                    </figcaption>
                </figure>
            <?php endforeach; ?>
        </div>

        <div class="nav-card-grid nav-card-grid--2">
            <?php
            $sections = [
                ['title' => 'Testing & Verification', 'url' => '/quality/testing/', 'icon' => '01', 'desc' => 'Independent third-party HPLC and mass spectrometry analysis for every batch. Endotoxin screening and sterility verification protocols.'],
                ['title' => 'Lab Results / COA', 'url' => '/quality/coa/', 'icon' => '02', 'desc' => 'Publicly accessible certificates of analysis for every compound. Purity data, molecular identification, and batch-specific documentation.'],
                ['title' => 'Manufacturing Standards', 'url' => '/quality/manufacturing/', 'icon' => '03', 'desc' => 'GMP-compliant synthesis facilities, validated production processes, and controlled environment standards for peptide manufacturing.'],
                ['title' => 'Handling & Storage', 'url' => '/quality/handling/', 'icon' => '04', 'desc' => 'Proper reconstitution, storage temperature requirements, and stability documentation for maintaining compound integrity.'],
            ];
            foreach ($sections as $s) :
            ?>
                <a href="<?php echo esc_url(home_url($s['url'])); ?>" class="nav-link-card">
                    <span class="nav-link-card__icon"><?php echo esc_html($s['icon']); ?></span>
                    <h3 class="nav-link-card__title"><?php echo esc_html($s['title']); ?></h3>
                    <p class="nav-link-card__desc"><?php echo esc_html($s['desc']); ?></p>
                    <span class="nav-link-card__action">Learn more →</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
