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
        <!-- About-page editorial 3D hero. Differs deliberately from the
             Quality page's 3-vial trio: here a single interactive vial
             is the focal point, flanked by brand statement copy and a
             metric strip — an about-us canvas, not a product lineup. -->
        <?php
        $theme_uri   = get_template_directory_uri();
        $about_rel   = 'assets/models/vial-ghkcu.glb';
        $about_ver   = function_exists('nav_asset_version')
            ? nav_asset_version($about_rel) : '';
        $about_glb   = $theme_uri . '/' . $about_rel . ($about_ver ? '?v=' . $about_ver : '');
        ?>
        <div class="nav-about-hero">
            <!-- Decorative hex lattice — subtle brand motif, pure CSS/SVG, no 3D cost -->
            <svg class="nav-about-hero__motif" viewBox="0 0 800 600" aria-hidden="true" focusable="false">
                <defs>
                    <pattern id="nav-hex" x="0" y="0" width="48" height="41.57" patternUnits="userSpaceOnUse">
                        <polygon points="24,2 44,13 44,33 24,44 4,33 4,13" fill="none" stroke="rgba(143,166,149,0.09)" stroke-width="0.8"/>
                    </pattern>
                    <radialGradient id="nav-hex-fade" cx="50%" cy="50%" r="60%">
                        <stop offset="0%"  stop-color="white" stop-opacity="1"/>
                        <stop offset="100%" stop-color="white" stop-opacity="0"/>
                    </radialGradient>
                    <mask id="nav-hex-mask">
                        <rect width="100%" height="100%" fill="url(#nav-hex-fade)"/>
                    </mask>
                </defs>
                <rect width="100%" height="100%" fill="url(#nav-hex)" mask="url(#nav-hex-mask)"/>
            </svg>

            <!-- Left: 3D vial -->
            <div class="nav-about-hero__viewer-wrap">
                <model-viewer
                    class="nav-about-hero__viewer"
                    src="<?php echo esc_url($about_glb); ?>"
                    alt="Navigate Peptides research vial — interactive 3D"
                    auto-rotate
                    camera-controls
                    interaction-prompt="none"
                    rotation-per-second="14deg"
                    camera-orbit="25deg 75deg 105%"
                    min-camera-orbit="auto auto 75%"
                    max-camera-orbit="auto auto 170%"
                    environment-image="neutral"
                    shadow-intensity="0.55"
                    exposure="1.2"
                    loading="eager"
                ></model-viewer>
            </div>

            <!-- Right: brand statement + metric strip -->
            <div class="nav-about-hero__statement">
                <span class="nav-about-hero__kicker">── Est. 2026 · USA</span>
                <h2 class="nav-about-hero__head">
                    Built for the<br>
                    <em>research bench.</em>
                </h2>
                <p class="nav-about-hero__lede">
                    Every compound is third-party HPLC-verified, batch-documented with
                    a public certificate of analysis, and supplied exclusively for
                    controlled laboratory investigation.
                </p>
                <dl class="nav-about-hero__metrics">
                    <div><dt>COA</dt><dd>Per batch</dd></div>
                    <div><dt>Purity</dt><dd>≥99% HPLC</dd></div>
                    <div><dt>Facility</dt><dd>GMP, USA</dd></div>
                </dl>
            </div>
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
