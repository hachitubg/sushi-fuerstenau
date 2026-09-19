<?php
/**
 * Single product presentation.
 *
 * @package Saiyoky
 */

defined( 'ABSPATH' ) || exit;

global $product;

do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'saiyoky-product', $product ); ?>>
	<nav class="product-breadcrumb" aria-label="<?php esc_attr_e( 'Brotkrümelnavigation', 'saiyoky' ); ?>">
		<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Speisekarte', 'saiyoky' ); ?></a>
		<span aria-hidden="true">›</span>
		<span><?php the_title(); ?></span>
	</nav>

	<div class="saiyoky-product__main">
		<div class="saiyoky-product__gallery">
			<?php do_action( 'woocommerce_before_single_product_summary' ); ?>
		</div>
		<div class="saiyoky-product__summary summary entry-summary">
			<?php if ( $product->get_sku() ) : ?><p class="eyebrow"><?php echo esc_html( $product->get_sku() ); ?></p><?php endif; ?>
			<?php do_action( 'woocommerce_single_product_summary' ); ?>
			<div class="product-service-points">
				<div><strong><?php esc_html_e( 'Frisch zubereitet', 'saiyoky' ); ?></strong><span><?php esc_html_e( 'Nach Ihrer Bestellung', 'saiyoky' ); ?></span></div>
				<div><strong><?php esc_html_e( 'Lieferung & Abholung', 'saiyoky' ); ?></strong><span><?php esc_html_e( 'Flexibel genießen', 'saiyoky' ); ?></span></div>
			</div>
		</div>
	</div>

	<div class="saiyoky-product__details">
		<?php do_action( 'woocommerce_after_single_product_summary' ); ?>
	</div>
</div>
<?php do_action( 'woocommerce_after_single_product' ); ?>

