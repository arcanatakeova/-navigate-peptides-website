<?php
/**
 * Template Name: Compounds Overview
 *
 * Redirects to WooCommerce shop page / product archive.
 * Assign this template to the "Compounds" page.
 *
 * @package NavigatePeptides
 */

// If WooCommerce shop page exists, redirect there
if (class_exists('WooCommerce') && wc_get_page_id('shop') > 0) {
    wp_safe_redirect(get_permalink(wc_get_page_id('shop')));
    exit;
}

get_header();
?>

<section class="nav-page-hero">
    <div class="nav-container">
        <h1 class="nav-page-hero__title">Research Compound Library</h1>
        <p class="nav-page-hero__subtitle">Browse peptide compounds organized by research application.</p>
    </div>
</section>

<section class="nav-section">
    <div class="nav-container">
        <?php get_template_part('template-parts/category', 'grid'); ?>
    </div>
</section>

<?php get_footer(); ?>
