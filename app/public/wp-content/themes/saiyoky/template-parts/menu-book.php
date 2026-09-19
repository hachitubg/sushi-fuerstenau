<?php
/**
 * Single-page viewer for the official printed menu.
 *
 * @package Saiyoky
 */

$page_language = function_exists( 'saiyoky_current_language' ) ? saiyoky_current_language() : get_locale();
$is_english    = str_starts_with( strtolower( (string) $page_language ), 'en' );
$asset_base    = get_template_directory_uri() . '/assets/menu-book/';
$book_pages    = array();
for ( $page = 1; $page <= 20; $page++ ) {
	$book_pages[] = $asset_base . sprintf( 'page-%02d.webp', $page );
}
$book_id = wp_unique_id( 'saiyoky-menu-book-' );
?>
<section id="menu-book" class="menu-book-section" aria-labelledby="<?php echo esc_attr( $book_id ); ?>-title">
	<div class="content-container">
		<header class="menu-book-heading" data-reveal>
			<div>
				<p class="eyebrow"><?php echo esc_html( $is_english ? 'The Saiyoky menu' : 'Die Saiyoky-Speisekarte' ); ?></p>
				<h2 id="<?php echo esc_attr( $book_id ); ?>-title"><?php echo esc_html( $is_english ? 'Discover a menu made for every appetite' : 'Entdecken Sie Vielfalt für jeden Geschmack' ); ?></h2>
				<p><?php echo esc_html( $is_english ? 'From freshly rolled sushi and handmade specialties to warming Asian dishes and refreshing drinks – explore each page of the Saiyoky menu at your own pace.' : 'Von frisch gerolltem Sushi und hausgemachten Spezialitäten bis zu warmen asiatischen Gerichten und erfrischenden Getränken – entdecken Sie Seite für Seite die ganze Auswahl von Saiyoky.' ); ?></p>
			</div>
		</header>

		<div class="menu-book menu-showcase" data-menu-book data-book-id="<?php echo esc_attr( $book_id ); ?>" data-page-label="<?php echo esc_attr( $is_english ? 'Saiyoky menu, page' : 'Saiyoky-Speisekarte, Seite' ); ?>" data-reveal>
			<script type="application/json" data-menu-book-pages><?php echo wp_json_encode( $book_pages, JSON_UNESCAPED_SLASHES ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></script>
			<div class="menu-book__toolbar">
				<span>Saiyoky <?php echo esc_html( $is_english ? 'Asian Kitchen' : 'Asiatische Küche' ); ?></span>
				<strong><?php echo esc_html( $is_english ? 'Page' : 'Seite' ); ?> <output data-book-counter aria-live="polite">1 / 20</output></strong>
				<a href="<?php echo esc_url( $asset_base . 'saiyoky-speisekarte-2026.pdf' ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $is_english ? 'Open PDF' : 'PDF öffnen' ); ?></a>
			</div>
			<div class="menu-showcase__frame">
				<button class="menu-showcase__page menu-book__stage" type="button" data-book-open aria-label="<?php echo esc_attr( $is_english ? 'Enlarge the current menu page' : 'Aktuelle Menüseite vergrößern' ); ?>">
					<span class="menu-showcase__page-number" aria-hidden="true">01</span>
					<img data-book-image src="<?php echo esc_url( $book_pages[0] ); ?>" alt="<?php echo esc_attr( $is_english ? 'Saiyoky menu, page 1' : 'Saiyoky-Speisekarte, Seite 1' ); ?>" width="935" height="1312" decoding="async" fetchpriority="low">
					<span class="menu-showcase__zoom"><?php echo esc_html( $is_english ? 'Enlarge' : 'Vergrößern' ); ?> <span aria-hidden="true">＋</span></span>
				</button>
				<button class="menu-book-nav menu-book-nav--prev" type="button" data-book-prev aria-label="<?php echo esc_attr( $is_english ? 'Previous menu page' : 'Vorherige Menüseite' ); ?>"><span aria-hidden="true">&#8249;</span></button>
				<button class="menu-book-nav menu-book-nav--next" type="button" data-book-next aria-label="<?php echo esc_attr( $is_english ? 'Next menu page' : 'Nächste Menüseite' ); ?>"><span aria-hidden="true">&#8250;</span></button>
			</div>
			<dialog class="menu-showcase__lightbox" data-book-lightbox aria-label="<?php echo esc_attr( $is_english ? 'Enlarged menu page' : 'Vergrößerte Menüseite' ); ?>">
				<button class="menu-showcase__lightbox-close" type="button" data-book-close aria-label="<?php echo esc_attr( $is_english ? 'Close' : 'Schließen' ); ?>">×</button>
				<button class="menu-showcase__lightbox-nav menu-showcase__lightbox-nav--prev" type="button" data-book-modal-prev aria-label="<?php echo esc_attr( $is_english ? 'Previous page' : 'Vorherige Seite' ); ?>">←</button>
				<img data-book-modal-image src="<?php echo esc_url( $book_pages[0] ); ?>" alt="<?php echo esc_attr( $is_english ? 'Saiyoky menu, page 1' : 'Saiyoky-Speisekarte, Seite 1' ); ?>" width="935" height="1312" decoding="async">
				<button class="menu-showcase__lightbox-nav menu-showcase__lightbox-nav--next" type="button" data-book-modal-next aria-label="<?php echo esc_attr( $is_english ? 'Next page' : 'Nächste Seite' ); ?>">→</button>
			</dialog>
		</div>
	</div>
</section>
