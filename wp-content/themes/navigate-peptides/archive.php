<?php
/**
 * Archive template (research articles, etc).
 *
 * @package NavigatePeptides
 */

get_header();
?>

<section class="nav-page-hero">
    <div class="nav-container">
        <span class="nav-kicker"><?php echo esc_html(post_type_archive_title('', false) ?: 'Archive'); ?></span>
        <h1 class="nav-page-hero__title"><?php the_archive_title(); ?></h1>
        <?php the_archive_description('<p class="nav-page-hero__subtitle">', '</p>'); ?>
    </div>
</section>

<section class="nav-section">
    <div class="nav-container">
        <?php if (have_posts()) : ?>
            <div class="nav-post-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article class="nav-post-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="nav-post-card__image">
                                <?php the_post_thumbnail('product-card'); ?>
                            </div>
                        <?php endif; ?>
                        <div class="nav-post-card__body">
                            <h3 class="nav-post-card__title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            <p class="nav-post-card__excerpt"><?php the_excerpt(); ?></p>
                            <a href="<?php the_permalink(); ?>" class="nav-post-card__link">Read more →</a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            <?php the_posts_pagination(['class' => 'nav-pagination']); ?>
        <?php else : ?>
            <div class="nav-empty-state">
                <p>Content is being prepared. Published articles will appear here.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
