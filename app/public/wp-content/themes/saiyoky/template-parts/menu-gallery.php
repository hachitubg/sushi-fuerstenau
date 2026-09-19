<?php
/**
 * Printed menu image gallery.
 *
 * @package Saiyoky
 */

defined( 'ABSPATH' ) || exit;

$is_english     = str_starts_with( strtolower( saiyoky_current_language() ), 'en' );
$menu_page_uri  = trailingslashit( get_template_directory_uri() . '/assets/images/menu-pages' );
$menu_thumb_uri = trailingslashit( get_template_directory_uri() . '/assets/images/menu-thumbs' );
$page_count     = 21;
?>
<section id="menu" class="menu-section">
	<div class="content-container">
		<header class="menu-section__heading" data-reveal>
			<p class="eyebrow"><?php echo esc_html( $is_english ? 'Our menu' : 'Unsere Speisekarte' ); ?></p>
			<h2><?php echo esc_html( $is_english ? 'Discover our complete menu' : 'Entdecken Sie unsere komplette Karte' ); ?></h2>
			<p><?php echo esc_html( $is_english ? 'Select a page to view the printed menu in full size.' : 'Wählen Sie eine Seite aus, um unsere gedruckte Speisekarte in voller Größe anzusehen.' ); ?></p>
		</header>
		<div class="menu-page-gallery" data-menu-gallery>
			<?php for ( $index = 1; $index <= $page_count; $index++ ) : ?>
				<?php $filename = sprintf( '%02d.webp', $index ); ?>
				<button class="menu-page-card" type="button" data-menu-index="<?php echo esc_attr( (string) ( $index - 1 ) ); ?>" data-menu-src="<?php echo esc_url( $menu_page_uri . $filename ); ?>" data-menu-title="<?php echo esc_attr( sprintf( $is_english ? 'Menu page %d' : 'Speisekarte, Seite %d', $index ) ); ?>" data-reveal>
					<span class="menu-page-card__image"><img src="<?php echo esc_url( $menu_thumb_uri . $filename ); ?>" alt="<?php echo esc_attr( sprintf( $is_english ? 'Menu page %d' : 'Speisekarte, Seite %d', $index ) ); ?>" loading="lazy" decoding="async" width="420" height="586"></span>
					<span class="menu-page-card__meta"><span><?php echo esc_html( sprintf( '%02d', $index ) ); ?></span><strong><?php echo esc_html( $is_english ? 'View page' : 'Seite ansehen' ); ?></strong><i aria-hidden="true">↗</i></span>
				</button>
			<?php endfor; ?>
		</div>
	</div>
</section>

<dialog class="menu-lightbox" data-menu-lightbox aria-label="<?php echo esc_attr( $is_english ? 'Large menu view' : 'Große Ansicht der Speisekarte' ); ?>">
	<div class="menu-lightbox__bar">
		<div><strong data-menu-lightbox-title></strong><span data-menu-lightbox-counter></span></div>
		<button type="button" data-menu-close aria-label="<?php echo esc_attr( $is_english ? 'Close' : 'Schließen' ); ?>">×</button>
	</div>
	<div class="menu-lightbox__stage">
		<button type="button" class="menu-lightbox__nav" data-menu-prev aria-label="<?php echo esc_attr( $is_english ? 'Previous page' : 'Vorherige Seite' ); ?>">‹</button>
		<img data-menu-lightbox-image alt="">
		<button type="button" class="menu-lightbox__nav" data-menu-next aria-label="<?php echo esc_attr( $is_english ? 'Next page' : 'Nächste Seite' ); ?>">›</button>
	</div>
</dialog>
