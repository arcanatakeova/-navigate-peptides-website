<?php
/**
 * Single post template (posts + nav_research CPT).
 *
 * @package NavigatePeptides
 */

get_header();

while (have_posts()) : the_post();
    $is_research = get_post_type() === 'nav_research';

    // Get primary category (research_category taxonomy for research posts)
    $tax_name = $is_research ? 'research_category' : 'category';
    $post_terms = get_the_terms(get_the_ID(), $tax_name);
    $primary_term = ($post_terms && !is_wp_error($post_terms)) ? $post_terms[0] : null;

    // Estimate reading time (rough: ~200 words/min)
    $word_count = str_word_count(wp_strip_all_tags(get_the_content()));
    $reading_time = max(1, (int) ceil($word_count / 200));

    // Prev/next in same post type
    $prev_post = get_previous_post(true, '', $tax_name);
    $next_post = get_next_post(true, '', $tax_name);
?>

<article class="nav-article">

    <section class="nav-page-hero">
        <div class="nav-container">
            <!-- Breadcrumbs -->
            <nav class="nav-breadcrumb nav-breadcrumb--inline" aria-label="Breadcrumb">
                <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
                <span class="nav-breadcrumb__sep">/</span>
                <?php if ($is_research) : ?>
                    <a href="<?php echo esc_url(home_url('/research/')); ?>">Research</a>
                    <?php if ($primary_term) : ?>
                        <span class="nav-breadcrumb__sep">/</span>
                        <a href="<?php echo esc_url(get_term_link($primary_term)); ?>"><?php echo esc_html($primary_term->name); ?></a>
                    <?php endif; ?>
                <?php endif; ?>
                <span class="nav-breadcrumb__sep">/</span>
                <span aria-current="page"><?php echo esc_html(wp_trim_words(get_the_title(), 8)); ?></span>
            </nav>

            <?php if ($primary_term) : ?>
                <a href="<?php echo esc_url(get_term_link($primary_term)); ?>" class="nav-kicker nav-kicker--link">
                    <?php echo esc_html($primary_term->name); ?>
                </a>
            <?php else : ?>
                <span class="nav-kicker"><?php echo esc_html(get_post_type_object(get_post_type())->labels->singular_name ?? 'Article'); ?></span>
            <?php endif; ?>

            <h1 class="nav-page-hero__title"><?php echo esc_html(get_the_title()); ?></h1>

            <?php if (has_excerpt()) : ?>
                <p class="nav-page-hero__subtitle"><?php echo esc_html(get_the_excerpt()); ?></p>
            <?php endif; ?>

            <div class="nav-article__meta">
                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                    <?php echo esc_html(get_the_date()); ?>
                </time>
                <span class="nav-article__meta-sep">·</span>
                <span><?php echo esc_html($reading_time); ?> min read</span>
            </div>
        </div>
    </section>

    <?php if (has_post_thumbnail()) : ?>
        <section class="nav-section nav-section--compact">
            <div class="nav-container">
                <div class="nav-article__hero-image">
                    <?php the_post_thumbnail('category-hero', ['loading' => 'eager']); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="nav-section">
        <div class="nav-container nav-container--narrow">
            <div class="nav-content nav-article__body">
                <?php the_content(); ?>
            </div>

            <!-- Prev / Next Navigation -->
            <?php if ($prev_post || $next_post) : ?>
                <nav class="nav-article-nav" aria-label="Article navigation">
                    <?php if ($prev_post) : ?>
                        <a href="<?php echo esc_url(get_permalink($prev_post)); ?>" class="nav-article-nav__link nav-article-nav__link--prev">
                            <span class="nav-article-nav__label">← Previous</span>
                            <span class="nav-article-nav__title"><?php echo esc_html(get_the_title($prev_post)); ?></span>
                        </a>
                    <?php else : ?>
                        <span></span>
                    <?php endif; ?>

                    <?php if ($next_post) : ?>
                        <a href="<?php echo esc_url(get_permalink($next_post)); ?>" class="nav-article-nav__link nav-article-nav__link--next">
                            <span class="nav-article-nav__label">Next →</span>
                            <span class="nav-article-nav__title"><?php echo esc_html(get_the_title($next_post)); ?></span>
                        </a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </div>
    </section>

    <?php
    // Related articles (same category, different posts)
    if ($primary_term) :
        $related = new WP_Query([
            'post_type'      => get_post_type(),
            'posts_per_page' => 3,
            'post__not_in'   => [get_the_ID()],
            'tax_query'      => [
                [
                    'taxonomy' => $tax_name,
                    'field'    => 'term_id',
                    'terms'    => $primary_term->term_id,
                ],
            ],
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        if ($related->have_posts()) :
    ?>
        <section class="nav-section nav-section--dark">
            <div class="nav-container">
                <div class="nav-section__header">
                    <h2 class="nav-section__title-caps">Related Articles</h2>
                </div>
                <div class="nav-post-grid">
                    <?php while ($related->have_posts()) : $related->the_post(); ?>
                        <article class="nav-post-card">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>" class="nav-post-card__image">
                                    <?php the_post_thumbnail('product-card', ['loading' => 'lazy']); ?>
                                </a>
                            <?php endif; ?>
                            <div class="nav-post-card__body">
                                <h3 class="nav-post-card__title">
                                    <a href="<?php the_permalink(); ?>"><?php echo esc_html(get_the_title()); ?></a>
                                </h3>
                                <p class="nav-post-card__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 18)); ?></p>
                                <a href="<?php the_permalink(); ?>" class="nav-post-card__link">Read more →</a>
                            </div>
                        </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
        </section>
    <?php
        endif;
    endif;
    ?>

    <!-- RUO Disclaimer on all research articles -->
    <?php if ($is_research) : ?>
    <section class="nav-section nav-section--disclaimer">
        <div class="nav-container">
            <?php nav_sitewide_disclaimer(); ?>
        </div>
    </section>
    <?php endif; ?>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
