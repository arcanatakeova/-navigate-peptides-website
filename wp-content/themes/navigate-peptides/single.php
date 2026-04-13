<?php
/**
 * Single post template.
 *
 * @package NavigatePeptides
 */

get_header();
?>

<?php while (have_posts()) : the_post(); ?>

<section class="nav-page-hero">
    <div class="nav-container">
        <span class="nav-kicker"><?php echo esc_html(get_post_type_object(get_post_type())->labels->singular_name ?? 'Article'); ?></span>
        <h1 class="nav-page-hero__title"><?php the_title(); ?></h1>
        <?php if (has_excerpt()) : ?>
            <p class="nav-page-hero__subtitle"><?php echo esc_html(get_the_excerpt()); ?></p>
        <?php endif; ?>
    </div>
</section>

<article class="nav-section">
    <div class="nav-container nav-content">
        <?php the_content(); ?>
    </div>
</article>

<!-- RUO Disclaimer on all research articles -->
<?php if (get_post_type() === 'nav_research') : ?>
<section class="nav-section nav-section--disclaimer">
    <div class="nav-container">
        <?php nav_sitewide_disclaimer(); ?>
    </div>
</section>
<?php endif; ?>

<?php endwhile; ?>

<?php get_footer(); ?>
