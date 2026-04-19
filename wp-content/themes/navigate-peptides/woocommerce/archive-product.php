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
                        <span class="nav-category-card__link">View Compounds →</span>
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
        <?php if (woocommerce_product_loop()) : ?>

            <?php woocommerce_product_loop_start(); ?>

            <?php while (have_posts()) : the_post(); ?>
                <?php
                global $product;
                $color = nav_get_product_category_color($product);
                $subtitle = get_post_meta($product->get_id(), '_nav_technical_subtitle', true);
                ?>
                <li <?php wc_product_class('nav-product-card', $product); ?> style="--cat-color: <?php echo esc_attr($color); ?>">
                    <a href="<?php the_permalink(); ?>" class="nav-product-card__link">
                        <div class="nav-product-card__accent"></div>
                        <div class="nav-product-card__image">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php echo wp_get_attachment_image(get_post_thumbnail_id(), 'product-card', false, ['loading' => 'lazy']); ?>
                            <?php else :
                                $terms = get_the_terms($product->get_id(), 'product_cat');
                                $cat_slug = ($terms && !is_wp_error($terms)) ? $terms[0]->slug : '';
                            ?>
                                <img src="<?php echo esc_url(nav_get_category_placeholder($cat_slug)); ?>" alt="<?php the_title_attribute(); ?>" class="nav-product-card__placeholder-img" loading="lazy" width="400" height="400">
                            <?php endif; ?>
                        </div>
                        <div class="nav-product-card__body">
                            <h3 class="nav-product-card__title"><?php echo esc_html(get_the_title()); ?></h3>
                            <?php if ($subtitle) : ?>
                                <p class="nav-product-card__subtitle"><?php echo esc_html($subtitle); ?></p>
                            <?php endif; ?>
                            <p class="nav-product-card__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 15)); ?></p>
                            <div class="nav-product-card__footer">
                                <span class="nav-product-card__price"><?php echo $product->get_price_html(); ?></span>
                                <span class="nav-product-card__action">Details →</span>
                            </div>
                            <p class="nav-product-card__disclaimer"><?php echo esc_html(nav_get_disclaimer('product')); ?></p>
                        </div>
                    </a>
                </li>
            <?php endwhile; ?>

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
