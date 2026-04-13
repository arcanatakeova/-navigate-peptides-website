<?php
/**
 * Default page template.
 *
 * @package NavigatePeptides
 */

get_header();
?>

<?php while (have_posts()) : the_post(); ?>

<section class="nav-page-hero">
    <div class="nav-container">
        <h1 class="nav-page-hero__title"><?php the_title(); ?></h1>
        <?php if (has_excerpt()) : ?>
            <p class="nav-page-hero__subtitle"><?php echo esc_html(get_the_excerpt()); ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="nav-section">
    <div class="nav-container nav-content">
        <?php the_content(); ?>
    </div>
</section>

<?php endwhile; ?>

<?php get_footer(); ?>
