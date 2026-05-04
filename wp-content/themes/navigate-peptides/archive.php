<?php
/**
 * Archive template (research articles, research categories).
 *
 * @package NavigatePeptides
 */

get_header();

$is_research_tax = is_tax('research_category');
$is_research_pt  = is_post_type_archive('nav_research');
$current_term    = $is_research_tax ? get_queried_object() : null;

// All research categories for the filter bar
$all_categories  = get_terms([
    'taxonomy'   => 'research_category',
    'hide_empty' => false,
]);
?>

<section class="nav-page-hero">
    <div class="nav-container">
        <?php if ($is_research_tax || $is_research_pt) : ?>
            <!-- Breadcrumbs for research -->
            <nav class="nav-breadcrumb nav-breadcrumb--inline" aria-label="<?php esc_attr_e('Breadcrumb', 'navigate-peptides'); ?>">
                <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'navigate-peptides'); ?></a>
                <span class="nav-breadcrumb__sep">/</span>
                <a href="<?php echo esc_url(home_url('/research/')); ?>"><?php esc_html_e('Research', 'navigate-peptides'); ?></a>
                <?php if ($is_research_tax) : ?>
                    <span class="nav-breadcrumb__sep">/</span>
                    <span aria-current="page"><?php echo esc_html($current_term->name); ?></span>
                <?php endif; ?>
            </nav>
        <?php endif; ?>

        <span class="nav-kicker"><?php echo esc_html(post_type_archive_title('', false) ?: __('Research Archive', 'navigate-peptides')); ?></span>
        <h1 class="nav-page-hero__title"><?php
            // Strip the "Archives:" / "Category:" / "Tag:" prefix WP
            // adds via the_archive_title() — it leaks WP plumbing into
            // a public hero. Use the bare term/post-type label instead.
            if (!empty($current_term)) {
                echo esc_html($current_term->name);
            } else {
                echo esc_html(post_type_archive_title('', false) ?: get_the_archive_title());
            }
        ?></h1>
        <?php the_archive_description('<p class="nav-page-hero__subtitle">', '</p>'); ?>
    </div>
</section>

<?php if ($is_research_tax || $is_research_pt) : ?>
<!-- Research Category Filter Tabs -->
<section class="nav-section nav-section--compact">
    <div class="nav-container">
        <nav class="nav-filter-tabs" aria-label="<?php esc_attr_e('Research categories', 'navigate-peptides'); ?>">
            <a href="<?php echo esc_url(get_post_type_archive_link('nav_research')); ?>"
               class="nav-filter-tab <?php echo $is_research_pt ? 'is-active' : ''; ?>">
                <?php esc_html_e('All Articles', 'navigate-peptides'); ?>
            </a>
            <?php if (!is_wp_error($all_categories) && !empty($all_categories)) :
                foreach ($all_categories as $term) :
                    $is_current = $is_research_tax && $current_term && $current_term->term_id === $term->term_id;
                ?>
                    <a href="<?php echo esc_url(get_term_link($term)); ?>"
                       class="nav-filter-tab <?php echo $is_current ? 'is-active' : ''; ?>">
                        <?php echo esc_html($term->name); ?>
                    </a>
                <?php endforeach;
            endif; ?>
        </nav>
    </div>
</section>
<?php endif; ?>

<section class="nav-section">
    <div class="nav-container">
        <?php if (have_posts()) : ?>
            <div class="nav-post-grid">
                <?php while (have_posts()) : the_post();
                    $post_terms = get_the_terms(get_the_ID(), 'research_category');
                    $primary_term = ($post_terms && !is_wp_error($post_terms)) ? $post_terms[0] : null;
                ?>
                    <article class="nav-post-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>" class="nav-post-card__image">
                                <?php the_post_thumbnail('product-card', ['loading' => 'lazy']); ?>
                            </a>
                        <?php endif; ?>
                        <div class="nav-post-card__body">
                            <?php if ($primary_term) : ?>
                                <a href="<?php echo esc_url(get_term_link($primary_term)); ?>" class="nav-post-card__tag">
                                    <?php echo esc_html($primary_term->name); ?>
                                </a>
                            <?php endif; ?>
                            <h3 class="nav-post-card__title">
                                <a href="<?php the_permalink(); ?>"><?php echo esc_html(get_the_title()); ?></a>
                            </h3>
                            <p class="nav-post-card__meta">
                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                    <?php echo esc_html(get_the_date()); ?>
                                </time>
                            </p>
                            <p class="nav-post-card__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 24)); ?></p>
                            <a href="<?php the_permalink(); ?>" class="nav-post-card__link"><?php esc_html_e('Read more →', 'navigate-peptides'); ?></a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            <?php the_posts_pagination(['class' => 'nav-pagination']); ?>
        <?php else : ?>
            <div class="nav-empty-state">
                <h3 class="nav-empty-state__title"><?php esc_html_e('No articles published yet', 'navigate-peptides'); ?></h3>
                <p><?php esc_html_e('Content is being prepared. In the meantime, explore our compound catalog or review our quality standards.', 'navigate-peptides'); ?></p>
                <div class="nav-cta-actions nav-cta-actions--center">
                    <a href="<?php echo esc_url(home_url('/compounds/')); ?>" class="nav-btn nav-btn--primary"><?php esc_html_e('Browse Compounds', 'navigate-peptides'); ?></a>
                    <a href="<?php echo esc_url(home_url('/quality/')); ?>" class="nav-btn nav-btn--outline"><?php esc_html_e('Quality Standards', 'navigate-peptides'); ?></a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
