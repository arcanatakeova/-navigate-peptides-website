<?php
/**
 * WooCommerce Product Archive (Shop / Category pages)
 *
 * @package NavigatePeptides
 */

defined('ABSPATH') || exit;

get_header();

$current_cat   = get_queried_object();
$is_category   = is_product_category() && $current_cat && !empty($current_cat->slug);
$cat_color     = $is_category ? nav_get_category_color($current_cat->slug) : '#474C50';
?>

<section class="nav-page-hero" <?php if ($is_category) echo 'style="--cat-color: ' . esc_attr($cat_color) . '"'; ?>>
    <div class="nav-container">
        <?php if ($is_category) : ?>
            <div class="nav-page-hero__accent" style="background-color: <?php echo esc_attr($cat_color); ?>"></div>
        <?php endif; ?>
        <h1 class="nav-page-hero__title">
            <?php
            if ($is_category) {
                echo esc_html($current_cat->name);
            } else {
                echo esc_html__('Research Compound Library', 'navigate-peptides');
            }
            ?>
        </h1>
        <p class="nav-page-hero__subtitle">
            <?php
            if ($is_category && $current_cat && !empty($current_cat->description)) {
                echo esc_html($current_cat->description);
            } elseif ($is_category && $current_cat) {
                // Category exists but has no description — emit a category-
                // specific default so the page doesn't render with an empty <p>.
                printf(
                    esc_html__('Browse %s peptide compounds. Every batch ships with third-party verified molecular identity and purity data.', 'navigate-peptides'),
                    esc_html($current_cat->name)
                );
            } else {
                echo esc_html__('Browse peptide compounds organized by research application. Each category contains compounds with verified certificates of analysis.', 'navigate-peptides');
            }
            ?>
        </p>
    </div>
</section>

<?php if (!$is_category) : ?>
<!-- Category Grid (on main shop page) -->
<section class="nav-section">
    <div class="nav-container">
        <div class="nav-category-grid">
            <?php
            $categories = [
                ['name' => 'Metabolic Research',     'slug' => 'metabolic-research',     'color' => '#2F4666', 'desc' => 'Compounds studied for metabolic pathway modulation and mitochondrial signaling research.'],
                ['name' => 'Tissue Repair Research', 'slug' => 'tissue-repair-research', 'color' => '#9C843E', 'desc' => 'Compounds investigated for extracellular matrix, collagen, and growth-factor pathway research.'],
                ['name' => 'Cognitive Research',     'slug' => 'cognitive-research',     'color' => '#5E507F', 'desc' => 'Compounds studied for neurotrophic pathways and synaptic signaling mechanisms.'],
                ['name' => 'Inflammation Research',  'slug' => 'inflammation-research',  'color' => '#4A141C', 'desc' => 'Compounds studied for cytokine modulation and immune-signaling mechanisms.'],
                ['name' => 'Cellular Research',      'slug' => 'cellular-research',      'color' => '#8E5660', 'desc' => 'Compounds investigated for cellular signaling pathways and proliferation mechanisms.'],
                ['name' => 'Dermal Research',        'slug' => 'dermal-research',        'color' => '#4A6B5F', 'desc' => 'Compounds studied for epidermal pathway analysis and structural protein research.'],
                ['name' => 'Longevity Research',     'slug' => 'longevity-research',     'color' => '#2E5C6A', 'desc' => 'Peptides investigated for cellular aging, senescence, and lifespan extension pathways.'],
                ['name' => 'Research Blends',        'slug' => 'research-blends',        'color' => '#474C50', 'desc' => 'Multi-peptide formulations designed for synergistic pathway research applications.'],
            ];
            foreach ($categories as $cat) :
                $link = get_term_link($cat['slug'], 'product_cat');
                if (is_wp_error($link)) $link = '#';
            ?>
                <a href="<?php echo esc_url($link); ?>" class="nav-category-card" style="--cat-color: <?php echo esc_attr($cat['color']); ?>">
                    <div class="nav-category-card__bar"></div>
                    <div class="nav-category-card__visual">
                        <img src="<?php echo esc_url(nav_get_category_placeholder($cat['slug'])); ?>" alt="<?php echo esc_attr($cat['name']); ?> illustration" loading="lazy" width="400" height="400">
                    </div>
                    <div class="nav-category-card__body">
                        <h3 class="nav-category-card__title"><?php echo esc_html($cat['name']); ?></h3>
                        <p class="nav-category-card__desc"><?php echo esc_html($cat['desc']); ?></p>
                        <span class="nav-category-card__link"><?php esc_html_e('View Compounds →', 'navigate-peptides'); ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Products -->
<section class="nav-section">
    <div class="nav-container">
        <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="nav-archive-search">
            <label for="nav-archive-search-input" class="screen-reader-text">
                <?php esc_html_e('Search compounds by name, subtitle, or sequence', 'navigate-peptides'); ?>
            </label>
            <input
                id="nav-archive-search-input"
                type="search"
                name="s"
                class="nav-archive-search__input"
                placeholder="<?php esc_attr_e('Filter by compound name, subtitle, or sequence…', 'navigate-peptides'); ?>"
                value="<?php echo esc_attr(get_search_query()); ?>"
                autocomplete="off"
            >
            <input type="hidden" name="post_type" value="product">
            <button type="submit" class="nav-archive-search__submit" aria-label="<?php esc_attr_e('Search', 'navigate-peptides'); ?>">
                <svg class="nav-icon nav-icon--sm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
            </button>
        </form>
    </div>
</section>
<section class="nav-section nav-section--products">
    <div class="nav-container">
        <?php if (woocommerce_product_loop()) : ?>

            <?php
            woocommerce_product_loop_start();

            // Prime the meta cache once for every product in the loop so
            // the per-card get_post_meta calls in the shared card template
            // hit the object cache instead of round-tripping to the DB.
            global $wp_query;
            if (!empty($wp_query->posts)) {
                $nav_archive_ids = wp_list_pluck($wp_query->posts, 'ID');
                update_meta_cache('post', $nav_archive_ids);
            }

            while (have_posts()) :
                the_post();
                get_template_part('template-parts/product-card', null, [
                    'show_excerpt' => true,
                ]);
            endwhile;
            ?>

            <?php woocommerce_product_loop_end(); ?>

            <?php woocommerce_pagination(); ?>

        <?php elseif ($is_category) : ?>
            <div class="nav-empty-state">
                <p><?php esc_html_e('Compounds for this category are being prepared. Check back soon.', 'navigate-peptides'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
