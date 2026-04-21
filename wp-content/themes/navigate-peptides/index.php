<?php
/**
 * Default index template (fallback).
 *
 * @package NavigatePeptides
 */

get_header();
?>

<section class="nav-page-hero">
    <div class="nav-container">
        <h1 class="nav-page-hero__title"><?php echo esc_html(wp_get_document_title()); ?></h1>
    </div>
</section>

<section class="nav-section">
    <div class="nav-container">
        <?php if (have_posts()) : ?>
            <div class="nav-post-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article class="nav-post-card">
                        <h3 class="nav-post-card__title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <p class="nav-post-card__excerpt"><?php the_excerpt(); ?></p>
                    </article>
                <?php endwhile; ?>
            </div>
            <?php the_posts_pagination(['class' => 'nav-pagination']); ?>
        <?php else : ?>
            <p class="nav-text-muted"><?php esc_html_e('No content found.', 'navigate-peptides'); ?></p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
