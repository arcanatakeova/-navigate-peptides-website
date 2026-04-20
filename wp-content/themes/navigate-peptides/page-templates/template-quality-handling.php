<?php
/**
 * Template Name: Quality — Handling & Storage
 *
 * @package NavigatePeptides
 */

get_header();
?>

<section class="nav-page-hero">
    <div class="nav-container">
        <h1 class="nav-page-hero__title"><?php esc_html_e('Handling & Storage', 'navigate-peptides'); ?></h1>
        <p class="nav-page-hero__subtitle"><?php esc_html_e('Proper handling and storage are critical for maintaining compound integrity.', 'navigate-peptides'); ?></p>
    </div>
</section>

<section class="nav-section">
    <div class="nav-container">
        <div class="nav-card-grid nav-card-grid--2">
            <div class="nav-info-card">
                <h3 class="nav-info-card__title"><?php esc_html_e('Storage Requirements', 'navigate-peptides'); ?></h3>
                <ul class="nav-info-card__list">
                    <li><?php esc_html_e('Lyophilized peptides: Store at -20°C for long-term stability', 'navigate-peptides'); ?></li>
                    <li><?php esc_html_e('Reconstituted peptides: Store at 2-8°C, use within 30 days', 'navigate-peptides'); ?></li>
                    <li><?php esc_html_e('Avoid repeated freeze-thaw transitions', 'navigate-peptides'); ?></li>
                    <li><?php esc_html_e('Keep away from direct light exposure', 'navigate-peptides'); ?></li>
                    <li><?php esc_html_e('Store in original nitrogen-flushed vials until reconstitution', 'navigate-peptides'); ?></li>
                </ul>
            </div>
            <div class="nav-info-card">
                <h3 class="nav-info-card__title"><?php esc_html_e('Laboratory Preparation Procedure', 'navigate-peptides'); ?></h3>
                <ul class="nav-info-card__list">
                    <li><?php esc_html_e('In laboratory preparation, add appropriate solvent slowly along the vial wall', 'navigate-peptides'); ?></li>
                    <li><?php esc_html_e('Allow lyophilized compound to dissolve under controlled conditions (2-5 minutes)', 'navigate-peptides'); ?></li>
                    <li><?php esc_html_e('Gently swirl if needed — do not agitate or vortex', 'navigate-peptides'); ?></li>
                    <li><?php esc_html_e('Record concentration based on total solvent volume for experimental documentation', 'navigate-peptides'); ?></li>
                    <li><?php esc_html_e('Document preparation date, solvent used, and final concentration in laboratory records', 'navigate-peptides'); ?></li>
                    <li><?php esc_html_e('For research use only — all handling must follow institutional laboratory procedures', 'navigate-peptides'); ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
