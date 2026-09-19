<?php
/**
 * Product card used by catalogue loops.
 *
 * @package Saiyoky
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product || ! $product->is_visible() ) {
	return;
}

$description = $product->get_short_description();
if ( '' === trim( wp_strip_all_tags( $description ) ) ) {
	$description = $product->get_description();
}
$description = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $description ) ), 30, '…' );

?>
<li <?php wc_product_class( 'menu-product-card', $product ); ?>>
	<div class="menu-product-card__content">
		<div class="menu-product-card__heading">
			<h2 class="woocommerce-loop-product__title">
				<?php if ( $product->get_sku() ) : ?><span class="menu-product-card__sku"><?php echo esc_html( $product->get_sku() ); ?></span><?php endif; ?>
				<span><?php the_title(); ?></span>
			</h2>
			<div class="menu-product-card__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
		</div>
		<?php if ( $description ) : ?>
			<p class="menu-product-card__description"><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>
		<div class="menu-product-card__action">
			<?php woocommerce_template_loop_add_to_cart(); ?>
		</div>
	</div>
</li>
