<?php
/**
 * Search results template.
 *
 * @package NavigatePeptides
 */

get_header();

$search_query = get_search_query();
$total_results = $GLOBALS['wp_query']->found_posts;
?>

<section class="nav-page-hero">
    <div class="nav-container">
        <span class="nav-kicker nav-kicker--mono"><?php esc_html_e('Search Results', 'navigate-peptides'); ?></span>
        <h1 class="nav-page-hero__title">
            <?php
            if ($total_results > 0) {
                printf(
                    /* translators: 1: number of results, 2: search query */
                    esc_html(_n('%1$s result for "%2$s"', '%1$s results for "%2$s"', $total_results, 'navigate-peptides')),
                    esc_html(number_format_i18n($total_results)),
                    esc_html($search_query)
                );
            } else {
                /* translators: %s: search query */
                printf(esc_html__('No results for "%s"', 'navigate-peptides'), esc_html($search_query));
            }
            ?>
        </h1>
    </div>
</section>

<section class="nav-section nav-section--compact">
    <div class="nav-container">
        <form role="search" method="get" class="nav-search-form" action="<?php echo esc_url(home_url('/')); ?>">
            <label for="nav-search-input" class="screen-reader-text"><?php esc_html_e('Search', 'navigate-peptides'); ?></label>
            <input type="search" id="nav-search-input" class="nav-form-input" placeholder="<?php esc_attr_e('Search compounds, research, quality…', 'navigate-peptides'); ?>" value="<?php echo esc_attr($search_query); ?>" name="s">
            <button type="submit" class="nav-btn nav-btn--primary"><?php esc_html_e('Search', 'navigate-peptides'); ?></button>
        </form>
    </div>
</section>

<section class="nav-section">
    <div class="nav-container">
        <?php if (have_posts()) : ?>
            <div class="nav-post-grid">
                <?php while (have_posts()) : the_post();
                    // Null-safe — get_post_type_object can return null on unregistered types.
                    $pto = get_post_type_object(get_post_type());
                    $post_type_label = $pto && isset($pto->labels->singular_name)
                        ? $pto->labels->singular_name
                        : '';
                ?>
                    <article class="nav-post-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>" class="nav-post-card__image">
                                <?php the_post_thumbnail('product-card', ['loading' => 'lazy']); ?>
                            </a>
                        <?php endif; ?>
                        <div class="nav-post-card__body">
                            <?php if ($post_type_label) : ?>
                                <span class="nav-post-card__tag"><?php echo esc_html($post_type_label); ?></span>
                            <?php endif; ?>
                            <h3 class="nav-post-card__title">
                                <a href="<?php the_permalink(); ?>"><?php echo esc_html(get_the_title()); ?></a>
                            </h3>
                            <p class="nav-post-card__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 22)); ?></p>
                            <a href="<?php the_permalink(); ?>" class="nav-post-card__link"><?php esc_html_e('View →', 'navigate-peptides'); ?></a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            <?php the_posts_pagination(['class' => 'nav-pagination']); ?>
        <?php else : ?>
            <div class="nav-empty-state">
                <h3 class="nav-empty-state__title"><?php esc_html_e('No matching content', 'navigate-peptides'); ?></h3>
                <p><?php esc_html_e('Try a different search term, or explore the catalog directly.', 'navigate-peptides'); ?></p>
                <div class="nav-cta-actions nav-cta-actions--center">
                    <a href="<?php echo esc_url(home_url('/compounds/')); ?>" class="nav-btn nav-btn--primary"><?php esc_html_e('Browse Compounds', 'navigate-peptides'); ?></a>
                    <a href="<?php echo esc_url(home_url('/research/')); ?>" class="nav-btn nav-btn--outline"><?php esc_html_e('Research Hub', 'navigate-peptides'); ?></a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
