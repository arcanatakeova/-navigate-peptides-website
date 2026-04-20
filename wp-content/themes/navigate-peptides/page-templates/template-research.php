<?php
/**
 * Template Name: Research Hub
 *
 * @package NavigatePeptides
 */

get_header();

$latest_articles = new WP_Query([
    'post_type'      => 'nav_research',
    'posts_per_page' => 3,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
]);
?>

<section class="nav-page-hero">
    <div class="nav-container">
        <h1 class="nav-page-hero__title"><?php esc_html_e('Research Hub', 'navigate-peptides'); ?></h1>
        <p class="nav-page-hero__subtitle">
            <?php esc_html_e('Scientific resources for peptide research. Pathway analysis, mechanism-of-action documentation, and referenced preclinical studies — organized for research professionals.', 'navigate-peptides'); ?>
        </p>
    </div>
</section>

<section class="nav-section">
    <div class="nav-container">
        <div class="nav-section__header">
            <h2 class="nav-section__title-caps"><?php esc_html_e('Research Categories', 'navigate-peptides'); ?></h2>
        </div>
        <div class="nav-card-grid nav-card-grid--2">
            <?php
            $sections = [
                ['slug' => 'intelligence', 'tag' => __('Analysis', 'navigate-peptides'),    'title' => __('Research Intelligence', 'navigate-peptides'), 'desc' => __('Curated analysis of peptide research developments, regulatory updates, and preclinical findings from peer-reviewed sources.', 'navigate-peptides')],
                ['slug' => 'library',      'tag' => __('Database', 'navigate-peptides'),    'title' => __('Research Library', 'navigate-peptides'),      'desc' => __('Comprehensive database of compound profiles, mechanism-of-action summaries, and referenced preclinical studies.', 'navigate-peptides')],
                ['slug' => 'framework',    'tag' => __('Methodology', 'navigate-peptides'), 'title' => __('Research Framework', 'navigate-peptides'),    'desc' => __('Methodological guidelines for peptide research including handling protocols, storage requirements, and documentation standards.', 'navigate-peptides')],
                ['slug' => 'emerging',     'tag' => __('Frontier', 'navigate-peptides'),    'title' => __('Emerging Research', 'navigate-peptides'),     'desc' => __('Coverage of novel peptide compounds and newly published research exploring uncharacterized signaling pathways.', 'navigate-peptides')],
            ];
            foreach ($sections as $s) :
                $link = get_term_link($s['slug'], 'research_category');
                if (is_wp_error($link)) $link = home_url('/research/');
            ?>
                <a href="<?php echo esc_url($link); ?>" class="nav-link-card">
                    <span class="nav-link-card__tag"><?php echo esc_html($s['tag']); ?></span>
                    <h3 class="nav-link-card__title"><?php echo esc_html($s['title']); ?></h3>
                    <p class="nav-link-card__desc"><?php echo esc_html($s['desc']); ?></p>
                    <span class="nav-link-card__action"><?php esc_html_e('Explore →', 'navigate-peptides'); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if ($latest_articles->have_posts()) : ?>
<!-- Latest Research Articles -->
<section class="nav-section nav-section--dark">
    <div class="nav-container">
        <div class="nav-section__header nav-section__header--split">
            <h2 class="nav-section__title-caps"><?php esc_html_e('Latest Articles', 'navigate-peptides'); ?></h2>
            <a href="<?php echo esc_url(get_post_type_archive_link('nav_research')); ?>" class="nav-section__link">
                <?php esc_html_e('View all articles →', 'navigate-peptides'); ?>
            </a>
        </div>
        <div class="nav-post-grid">
            <?php while ($latest_articles->have_posts()) : $latest_articles->the_post();
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
                        <p class="nav-post-card__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 22)); ?></p>
                        <a href="<?php the_permalink(); ?>" class="nav-post-card__link"><?php esc_html_e('Read more →', 'navigate-peptides'); ?></a>
                    </div>
                </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
