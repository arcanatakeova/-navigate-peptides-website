<?php
/**
 * WooCommerce Single Product — 7-Point Structure
 *
 * 1. Product name
 * 2. Technical subtitle
 * 3. Short scientific description (1 line)
 * 4. Research focus (bullet points)
 * 5. Studies available
 * 6. Purity / COA information
 * 7. Research-use disclaimer
 *
 * @package NavigatePeptides
 */

defined('ABSPATH') || exit;

get_header();

while (have_posts()) : the_post();
    global $product;

    $cat_color  = nav_get_product_category_color($product);
    $subtitle   = get_post_meta($product->get_id(), '_nav_technical_subtitle', true);
    $cas        = get_post_meta($product->get_id(), '_nav_cas_number', true);
    $mw         = get_post_meta($product->get_id(), '_nav_molecular_weight', true);
    $sequence   = get_post_meta($product->get_id(), '_nav_sequence', true);
    $purity     = get_post_meta($product->get_id(), '_nav_purity', true);
    $form       = get_post_meta($product->get_id(), '_nav_form', true);
    $storage    = get_post_meta($product->get_id(), '_nav_storage', true);
    $focus      = get_post_meta($product->get_id(), '_nav_research_focus', true);
    $batch      = get_post_meta($product->get_id(), '_nav_batch_number', true);
    $lab        = get_post_meta($product->get_id(), '_nav_testing_lab', true);
    $coa_url    = get_post_meta($product->get_id(), '_nav_coa_pdf', true);

    $terms = get_the_terms($product->get_id(), 'product_cat');
    $cat   = ($terms && !is_wp_error($terms)) ? $terms[0] : null;
?>

<!-- Breadcrumb -->
<?php woocommerce_breadcrumb(); ?>

<section class="nav-section nav-product-single" style="--cat-color: <?php echo esc_attr($cat_color); ?>">
    <div class="nav-container">
        <div class="nav-product-single__grid">

            <!-- Left: Product Image / 3D Model -->
            <div class="nav-product-single__media">
                <div class="nav-product-single__image">
                    <?php
                    // Check for 3D model GLB file
                    $glb_url = get_post_meta($product->get_id(), '_nav_3d_model_url', true);
                    ?>
                    <?php if ($glb_url) : ?>
                        <model-viewer
                            src="<?php echo esc_url($glb_url); ?>"
                            alt="<?php echo esc_attr(get_the_title()); ?> — 3D interactive model"
                            auto-rotate
                            camera-controls
                            rotation-per-second="10deg"
                            camera-orbit="20deg 75deg 105%"
                            environment-image="neutral"
                            shadow-intensity="0.4"
                            exposure="1.1"
                            style="width:100%;height:100%;min-height:400px;"
                            loading="eager"
                        >
                            <?php if (has_post_thumbnail()) : ?>
                                <img slot="poster" src="<?php echo esc_url(get_the_post_thumbnail_url(null, 'product-hero')); ?>" alt="<?php the_title_attribute(); ?>">
                            <?php endif; ?>
                        </model-viewer>
                    <?php elseif (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('product-hero'); ?>
                    <?php else :
                        $cat_slug_img = $cat ? $cat->slug : '';
                        $cat_svg_path = NAV_THEME_URI . '/assets/images/categories/' . $cat_slug_img . '.svg';
                        $generic_svg  = NAV_THEME_URI . '/assets/images/product-placeholder.svg';
                        $hero_src = $cat_slug_img ? $cat_svg_path : $generic_svg;
                    ?>
                        <img src="<?php echo esc_url($hero_src); ?>" alt="<?php the_title_attribute(); ?>" class="nav-product-single__placeholder-img" width="800" height="800">
                    <?php endif; ?>
                </div>
                <?php if ($product->get_gallery_image_ids()) : ?>
                    <div class="nav-product-single__gallery">
                        <?php foreach ($product->get_gallery_image_ids() as $img_id) : ?>
                            <div class="nav-product-single__thumb">
                                <?php echo wp_get_attachment_image($img_id, 'product-card'); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right: Product Details (7-Point Structure) -->
            <div class="nav-product-single__details">

                <!-- Category badge -->
                <?php if ($cat) : ?>
                    <span class="nav-product-single__cat-badge" style="background-color: <?php echo esc_attr($cat_color); ?>20; color: <?php echo esc_attr($cat_color); ?>">
                        <?php echo esc_html($cat->name); ?>
                    </span>
                <?php endif; ?>

                <!-- 1. Product Name -->
                <h1 class="nav-product-single__title"><?php the_title(); ?></h1>

                <!-- 2. Technical Subtitle -->
                <?php if ($subtitle) : ?>
                    <p class="nav-product-single__subtitle"><?php echo esc_html($subtitle); ?></p>
                <?php endif; ?>

                <!-- 3. Short Scientific Description -->
                <div class="nav-product-single__desc">
                    <?php echo wpautop(wp_kses_post($product->get_short_description())); ?>
                </div>

                <!-- Technical Specifications -->
                <?php if ($cas || $mw || $sequence || $form || $storage) : ?>
                    <div class="nav-product-single__specs">
                        <?php if ($cas) : ?>
                            <div class="nav-spec">
                                <span class="nav-spec__label">CAS Number</span>
                                <span class="nav-spec__value"><?php echo esc_html($cas); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($mw) : ?>
                            <div class="nav-spec">
                                <span class="nav-spec__label">Molecular Weight</span>
                                <span class="nav-spec__value"><?php echo esc_html($mw); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($sequence) : ?>
                            <div class="nav-spec nav-spec--full">
                                <span class="nav-spec__label">Sequence</span>
                                <span class="nav-spec__value"><?php echo esc_html($sequence); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($form) : ?>
                            <div class="nav-spec">
                                <span class="nav-spec__label">Form</span>
                                <span class="nav-spec__value"><?php echo esc_html($form); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($storage) : ?>
                            <div class="nav-spec">
                                <span class="nav-spec__label">Storage</span>
                                <span class="nav-spec__value"><?php echo esc_html($storage); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- 4. Research Focus -->
                <?php if ($focus) : ?>
                    <div class="nav-product-single__focus">
                        <h2>Research Focus</h2>
                        <ul>
                            <?php
                            $items = array_filter(array_map('trim', explode("\n", $focus)));
                            foreach ($items as $item) :
                            ?>
                                <li>
                                    <span class="nav-bullet" style="background-color: <?php echo esc_attr($cat_color); ?>"></span>
                                    <?php echo esc_html($item); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- 6. Purity / COA Information -->
                <?php if ($purity || $batch || $lab || $coa_url) : ?>
                    <div class="nav-product-single__coa">
                        <h2>Purity &amp; Verification</h2>
                        <div class="nav-product-single__coa-grid">
                            <?php if ($purity) : ?>
                                <span class="nav-mono"><?php echo esc_html($purity); ?></span>
                            <?php endif; ?>
                            <?php if ($coa_url) : ?>
                                <a href="<?php echo esc_url($coa_url); ?>" class="nav-product-single__coa-link" target="_blank" rel="noopener">
                                    View Certificate of Analysis →
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Price + Add to Cart -->
                <div class="nav-product-single__purchase">
                    <span class="nav-product-single__price"><?php echo $product->get_price_html(); ?></span>
                    <?php woocommerce_template_single_add_to_cart(); ?>
                </div>

                <!-- 7. Research-Use Disclaimer -->
                <div class="nav-product-single__disclaimer">
                    <p class="nav-disclaimer--strong"><?php echo esc_html(nav_get_disclaimer('product')); ?></p>
                    <p><?php echo esc_html(nav_get_disclaimer('sitewide')); ?></p>
                </div>
            </div>
        </div>

        <!-- Product Tabs (Description / Research Focus / COA) -->
        <div class="nav-product-single__tabs">
            <?php woocommerce_output_product_data_tabs(); ?>
        </div>

        <!-- Related Products -->
        <?php woocommerce_output_related_products(); ?>
    </div>
</section>

<?php endwhile; ?>

<?php get_footer(); ?>
