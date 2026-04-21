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
            <?php if (has_post_thumbnail()) : ?>
                <?php echo wp_get_attachment_image(get_post_thumbnail_id(), 'product-card', false, ['loading' => 'lazy']); ?>
            <?php else : ?>
                <img
                    src="<?php echo esc_url(nav_get_category_placeholder($cat_slug)); ?>"
                    alt="<?php the_title_attribute(); ?>"
                    class="nav-product-card__placeholder-img"
                    loading="lazy"
                    width="400"
                    height="400"
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
                <span class="nav-product-card__action"><?php esc_html_e('View →', 'navigate-peptides'); ?></span>
            </div>
        </div>
    </a>
</li>
