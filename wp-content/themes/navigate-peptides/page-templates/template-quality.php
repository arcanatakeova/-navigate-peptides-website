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
        <!-- Visual: Lab vials -->
        <div class="nav-about-visual">
            <picture>
                <source srcset="<?php echo esc_url(get_template_directory_uri() . '/assets/images/hero-three-vials.webp'); ?>" type="image/webp">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/hero-three-vials.png'); ?>" alt="Navigate Peptides research compound vials" loading="lazy" width="1200" height="400">
            </picture>
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
