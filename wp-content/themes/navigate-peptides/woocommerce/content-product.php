<?php
/**
 * Branded product card template — rendered by WooCommerce wherever the
 * default product loop is invoked (related products, upsells, shortcodes,
 * and the fallback path on archives that don't use our archive-product.php).
 *
 * Mirrors the card in archive-product.php so related compounds on the
 * single-product page match the rest of the site instead of falling back
 * to Woo's white-box placeholder layout.
 *
 * @package NavigatePeptides
 */

defined('ABSPATH') || exit;

global $product;
if (!($product instanceof WC_Product) || !$product->exists()) {
    return;
}

$cat_color = function_exists('nav_get_product_category_color')
    ? nav_get_product_category_color($product)
    : '#474C50';

$subtitle = get_post_meta($product->get_id(), '_nav_technical_subtitle', true);

$terms     = get_the_terms($product->get_id(), 'product_cat');
$cat_slug  = ($terms && !is_wp_error($terms)) ? $terms[0]->slug : '';
?>
<li <?php wc_product_class('nav-product-card', $product); ?> style="--cat-color: <?php echo esc_attr($cat_color); ?>">
    <a href="<?php the_permalink(); ?>" class="nav-product-card__link">
        <div class="nav-product-card__accent"></div>
        <div class="nav-product-card__image">
            <?php
            $glb_url  = nav_safe_glb_url(
                get_post_meta($product->get_id(), '_nav_3d_model_url', true),
                $product->get_id()
            );
            $card_img = nav_get_product_card_image($product);
            ?>
            <?php if ($glb_url) : ?>
                <model-viewer
                    class="nav-product-card__viewer"
                    src="<?php echo esc_url($glb_url); ?>"
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
                    <img
                        slot="poster"
                        src="<?php echo esc_url($card_img['src']); ?>"
                        alt=""
                        class="nav-product-card__img"
                        loading="lazy"
                        decoding="async"
                        width="<?php echo esc_attr((string) $card_img['width']); ?>"
                        height="<?php echo esc_attr((string) $card_img['height']); ?>"
                    >
                </model-viewer>
            <?php else : ?>
                <img
                    src="<?php echo esc_url($card_img['src']); ?>"
                    <?php if (!empty($card_img['srcset'])) : ?>srcset="<?php echo esc_attr($card_img['srcset']); ?>"<?php endif; ?>
                    alt="<?php the_title_attribute(); ?>"
                    class="nav-product-card__img"
                    loading="lazy"
                    decoding="async"
                    width="<?php echo esc_attr((string) $card_img['width']); ?>"
                    height="<?php echo esc_attr((string) $card_img['height']); ?>"
                >
            <?php endif; ?>
        </div>
        <div class="nav-product-card__body">
            <h3 class="nav-product-card__title"><?php echo esc_html(get_the_title()); ?></h3>
            <?php if ($subtitle) : ?>
                <p class="nav-product-card__subtitle"><?php echo esc_html($subtitle); ?></p>
            <?php endif; ?>
            <div class="nav-product-card__footer">
                <span class="nav-product-card__price"><?php echo $product->get_price_html(); ?></span>
                <span class="nav-product-card__action"><?php esc_html_e('View peptide →', 'navigate-peptides'); ?></span>
            </div>
        </div>
    </a>
</li>
