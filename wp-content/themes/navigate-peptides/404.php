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
        <h1 class="nav-page-hero__title">Page Not Found</h1>
        <p class="nav-page-hero__subtitle">The page you're looking for doesn't exist or has been moved.</p>
    </div>
</section>

<section class="nav-section">
    <div class="nav-container nav-section--center">
        <div class="nav-cta-actions">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-btn nav-btn--primary">Return Home</a>
            <a href="<?php echo esc_url(home_url('/compounds/')); ?>" class="nav-btn nav-btn--outline">Browse Compounds</a>
        </div>
    </div>
</section>

<?php get_footer(); ?>
