<?php
/**
 * Template Name: About
 *
 * @package NavigatePeptides
 */

get_header();
?>

<section class="nav-page-hero">
    <div class="nav-container">
        <h1 class="nav-page-hero__title">About Navigate Peptides</h1>
        <p class="nav-page-hero__subtitle">A research-focused peptide supplier built on transparency, scientific rigor, and uncompromising quality standards.</p>
    </div>
</section>

<section class="nav-section">
    <div class="nav-container">
        <!-- Visual: Research vials. mtime-versioned for cache-busting. -->
        <?php
        $hero_ver = function_exists('nav_asset_version')
            ? nav_asset_version('assets/images/hero-three-vials.png')
            : '';
        $hero_q   = $hero_ver ? '?v=' . $hero_ver : '';
        ?>
        <div class="nav-about-visual">
            <picture>
                <source srcset="<?php echo esc_url(get_template_directory_uri() . '/assets/images/hero-three-vials.webp' . $hero_q); ?>" type="image/webp">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/hero-three-vials.png' . $hero_q); ?>" alt="Navigate Peptides research vials with certificates of analysis" loading="lazy" width="1200" height="400">
            </picture>
        </div>

        <div class="nav-about-grid">
            <div class="nav-about-grid__content">
                <div class="nav-content">
                    <p>Navigate Peptides was founded to address a gap in the research peptide market: the need for a supplier that prioritizes scientific credibility, transparent quality documentation, and rigorous compliance standards.</p>
                    <p>Every compound in our catalog undergoes independent third-party testing via HPLC and mass spectrometry. Certificates of analysis are published for every batch, providing researchers with the verification data they need to trust their materials.</p>
                    <p>Our compound profiles include detailed mechanism-of-action documentation, cited preclinical studies, and pathway analysis — because researchers need more than a product listing. They need scientific context.</p>
                    <p><?php echo esc_html(nav_get_disclaimer('sitewide')); ?></p>
                </div>
            </div>
            <div class="nav-about-grid__sidebar">
                <a href="<?php echo esc_url(home_url('/about/standards/')); ?>" class="nav-link-card">
                    <h3 class="nav-link-card__title">Our Standards</h3>
                    <p class="nav-link-card__desc">The principles and commitments that guide our operations.</p>
                    <span class="nav-link-card__action">Read more →</span>
                </a>
                <a href="<?php echo esc_url(nav_get_contact_url()); ?>" class="nav-link-card">
                    <h3 class="nav-link-card__title">Contact / Request Access</h3>
                    <p class="nav-link-card__desc">Reach our team for inquiries, wholesale requests, or research collaboration.</p>
                    <span class="nav-link-card__action">Get in touch →</span>
                </a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
