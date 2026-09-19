<?php
/**
 * WooCommerce catalogue and single-product wrapper.
 *
 * @package Saiyoky
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_singular( 'product' ) ) :
	?>
	<main id="main" class="site-main product-page content-container">
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<?php wc_get_template_part( 'content', 'single-product' ); ?>
		<?php endwhile; ?>
	</main>
	<?php
else :
	$current_category = is_product_category() ? get_queried_object() : null;
	$description      = $current_category instanceof WP_Term ? term_description( $current_category ) : '';
	$page_language    = function_exists( 'saiyoky_current_language' ) ? saiyoky_current_language() : get_locale();
	$is_english       = str_starts_with( strtolower( (string) $page_language ), 'en' );
	$menu_search      = isset( $_GET['menu_search'] ) ? sanitize_text_field( wp_unslash( $_GET['menu_search'] ) ) : '';
	$categories       = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
		)
	);
	$category_order   = array( 'mittagskarte', 'vorspeisen', 'hauptspeisen', 'beilagen', 'maki', 'nigiri', 'inside-out-rolls', 'special-homemade-rolls', 'tempura-roll-sushi', 'sashimi', 'sushi-menues', 'getraenke' );
	if ( ! is_wp_error( $categories ) ) {
		usort(
			$categories,
			static function ( WP_Term $first, WP_Term $second ) use ( $category_order ): int {
				return array_search( $first->slug, $category_order, true ) <=> array_search( $second->slug, $category_order, true );
			}
		);
	}
	?>
	<main id="main" class="shop-page">
		<section class="shop-hero">
			<div class="content-container">
				<p class="eyebrow">Saiyoky · Sushi & Asian Kitchen Fürstenau</p>
				<h1><?php woocommerce_page_title(); ?></h1>
				<?php if ( $description ) : ?>
					<div class="shop-hero__description"><?php echo wp_kses_post( $description ); ?></div>
				<?php else : ?>
					<p class="shop-hero__description"><?php echo esc_html( $is_english ? 'Choose your favourites from our complete menu. The clear name, description and price layout helps you find the right dish quickly.' : 'Wählen Sie Ihre Favoriten aus unserer vollständigen Speisekarte. Die klare Darstellung von Name, Beschreibung und Preis hilft Ihnen, schnell das passende Gericht zu finden.' ); ?></p>
				<?php endif; ?>
			</div>
		</section>

		<section class="shop-menu-section">
		<div class="content-container">
			<div class="shop-menu-tools">
			<form class="menu-search" role="search" method="get" action="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
				<label for="saiyoky-menu-search"><?php echo esc_html( $is_english ? 'Search dishes by name' : 'Gerichte nach Namen suchen' ); ?></label>
				<div class="menu-search__row">
					<span class="menu-search__icon" aria-hidden="true"></span>
					<input id="saiyoky-menu-search" name="menu_search" type="search" value="<?php echo esc_attr( $menu_search ); ?>" placeholder="<?php echo esc_attr( $is_english ? 'e.g. Edamame, Salmon, Maki…' : 'z. B. Edamame, Lachs, Maki…' ); ?>">
					<button class="button" type="submit"><?php echo esc_html( $is_english ? 'Search' : 'Suchen' ); ?></button>
				</div>
				<?php if ( '' !== $menu_search ) : ?>
					<p class="menu-search__summary"><?php echo esc_html( sprintf( $is_english ? 'Results for “%s”' : 'Ergebnisse für „%s“', $menu_search ) ); ?> <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php echo esc_html( $is_english ? 'Clear search' : 'Suche löschen' ); ?></a></p>
				<?php endif; ?>
			</form>
				<a class="button menu-image-button" href="<?php echo esc_url( is_shop() ? '#menu-book' : wc_get_page_permalink( 'shop' ) . '#menu-book' ); ?>">
					<?php echo esc_html( $is_english ? 'View full menu' : 'Vollständige Speisekarte' ); ?> <span aria-hidden="true">↓</span>
				</a>
			</div>

			<div class="shop-menu-panel">
				<nav class="menu-category-nav" aria-label="<?php echo esc_attr( $is_english ? 'Menu categories' : 'Speisekarten-Kategorien' ); ?>">
					<div class="menu-category-nav__inner">
						<a class="<?php echo is_shop() ? 'is-active' : ''; ?>" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php echo esc_html( $is_english ? 'All dishes' : 'Alle Gerichte' ); ?></a>
						<?php if ( ! is_wp_error( $categories ) ) : ?>
							<?php foreach ( $categories as $category ) : ?>
								<a class="<?php echo $current_category && (int) $current_category->term_id === (int) $category->term_id ? 'is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</nav>

				<div class="shop-catalog">
			<?php if ( woocommerce_product_loop() ) : ?>
				<div class="shop-toolbar">
					<?php woocommerce_result_count(); ?>
					<?php woocommerce_catalog_ordering(); ?>
				</div>
				<?php woocommerce_product_loop_start(); ?>
				<?php while ( have_posts() ) : ?>
					<?php the_post(); ?>
					<?php wc_get_template_part( 'content', 'product' ); ?>
				<?php endwhile; ?>
				<?php woocommerce_product_loop_end(); ?>
				<?php woocommerce_pagination(); ?>
			<?php else : ?>
				<div class="shop-empty">
					<h2><?php echo esc_html( $menu_search ? ( $is_english ? 'No matching dishes found.' : 'Keine passenden Gerichte gefunden.' ) : ( $is_english ? 'This category is being prepared.' : 'Diese Kategorie wird vorbereitet.' ) ); ?></h2>
					<p><?php echo esc_html( $menu_search ? ( $is_english ? 'Try another dish name or show the complete menu.' : 'Versuchen Sie einen anderen Namen oder zeigen Sie die komplette Speisekarte an.' ) : ( $is_english ? 'More dishes will follow shortly.' : 'Weitere Gerichte folgen in Kürze.' ) ); ?></p>
				</div>
			<?php endif; ?>
				</div>
			</div>
		</div>
		</section>

		<?php if ( is_shop() ) : ?>
			<?php get_template_part( 'template-parts/menu-book' ); ?>
		<?php endif; ?>
	</main>
	<?php
endif;

get_footer();
