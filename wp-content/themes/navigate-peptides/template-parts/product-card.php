<?php
/**
 * Shared product-card markup used by:
 *   - woocommerce/archive-product.php (shop + category + tag loops)
 *   - woocommerce/content-product.php (related products, upsells,
 *     shortcodes, any default Woo loop)
 *
 * Expects `$product` to be set in scope (global for archive, template arg
 * via `get_template_part('template-parts/product-card', null, ['product' => $product])`
 * for related products).
 *
 * @package NavigatePeptides
 */

defined('ABSPATH') || exit;

/** @var WC_Product|null $product */
if (!isset($product) || !($product instanceof WC_Product)) {
    global $product;
}
if (!($product instanceof WC_Product) || !$product->exists()) {
    return;
}

$nav_product_id       = $product->get_id();
$nav_card_color       = function_exists('nav_get_product_category_color')
    ? nav_get_product_category_color($product)
    : '#474C50';
$nav_card_subtitle    = (string) get_post_meta($nav_product_id, '_nav_technical_subtitle', true);
$nav_card_glb         = function_exists('nav_safe_glb_url')
    ? nav_safe_glb_url(get_post_meta($nav_product_id, '_nav_3d_model_url', true), $nav_product_id)
    : '';
$nav_card_image       = function_exists('nav_get_product_card_image')
    ? nav_get_product_card_image($product)
    : ['src' => '', 'srcset' => null, 'width' => 400, 'height' => 400];
$nav_card_show_excerpt = ! empty($args['show_excerpt']);
?>
<li <?php wc_product_class('nav-product-card', $product); ?> style="--cat-color: <?php echo esc_attr($nav_card_color); ?>">
    <a href="<?php the_permalink(); ?>" class="nav-product-card__link">
        <div class="nav-product-card__accent"></div>
        <div class="nav-product-card__image">
            <?php if ($nav_card_glb) : ?>
                <model-viewer
                    class="nav-product-card__viewer"
                    src="<?php echo esc_url($nav_card_glb); ?>"
                    alt="<?php echo esc_attr(get_the_title()); ?> — 3D vial"
                    auto-rotate
                    rotation-per-second="20deg"
                    interaction-prompt="none"
                    disable-zoom
                    disable-pan
                    disable-tap
                    camera-orbit="0deg 75deg 110%"
                    environment-image="neutral"
                    shadow-intensity="0.6"
                    exposure="1.2"
                    loading="lazy"
                    reveal="auto"
                    aria-hidden="true"
                >
                    <?php if (!empty($nav_card_image['src'])) : ?>
                        <img
                            slot="poster"
                            src="<?php echo esc_url($nav_card_image['src']); ?>"
                            alt=""
                            class="nav-product-card__img"
                            loading="lazy"
                            decoding="async"
                            width="<?php echo esc_attr((string) $nav_card_image['width']); ?>"
                            height="<?php echo esc_attr((string) $nav_card_image['height']); ?>"
                        >
                    <?php endif; ?>
                </model-viewer>
            <?php elseif (!empty($nav_card_image['src'])) : ?>
                <img
                    src="<?php echo esc_url($nav_card_image['src']); ?>"
                    <?php if (!empty($nav_card_image['srcset'])) : ?>srcset="<?php echo esc_attr($nav_card_image['srcset']); ?>"<?php endif; ?>
                    alt="<?php the_title_attribute(); ?>"
                    class="nav-product-card__img"
                    loading="lazy"
                    decoding="async"
                    width="<?php echo esc_attr((string) $nav_card_image['width']); ?>"
                    height="<?php echo esc_attr((string) $nav_card_image['height']); ?>"
                >
            <?php endif; ?>
        </div>
        <div class="nav-product-card__body">
            <h3 class="nav-product-card__title"><?php echo esc_html(get_the_title()); ?></h3>
            <?php if ($nav_card_subtitle) : ?>
                <p class="nav-product-card__subtitle"><?php echo esc_html($nav_card_subtitle); ?></p>
            <?php endif; ?>
            <?php if ($nav_card_show_excerpt) : ?>
                <p class="nav-product-card__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 15)); ?></p>
            <?php endif; ?>
            <div class="nav-product-card__footer">
                <span class="nav-product-card__price"><?php echo $product->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput -- WC returns trusted HTML ?></span>
                <span class="nav-product-card__action"><?php esc_html_e('View peptide →', 'navigate-peptides'); ?></span>
            </div>
            <?php if ($nav_card_show_excerpt) : ?>
                <p class="nav-product-card__disclaimer"><?php echo esc_html(nav_get_disclaimer('product')); ?></p>
            <?php endif; ?>
        </div>
    </a>
</li>
