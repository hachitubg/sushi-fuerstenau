<?php
/**
 * About page.
 *
 * @package Saiyoky
 */

get_header();

$page_language = function_exists( 'saiyoky_current_language' ) ? saiyoky_current_language() : get_locale();
$is_english    = str_starts_with( strtolower( (string) $page_language ), 'en' );
?>
<main id="main" class="site-main editorial-page">
	<section class="inner-hero inner-hero--about">
		<div class="content-container">
			<p class="eyebrow">Saiyoky Fürstenau</p>
			<h1><?php echo esc_html( $is_english ? 'Asian cuisine with character.' : 'Asiatische Küche mit Charakter.' ); ?></h1>
			<p><?php echo esc_html( $is_english ? 'Fresh sushi, Vietnamese favourites and modern Asian dishes – prepared with care for every guest.' : 'Frisches Sushi, vietnamesische Lieblingsgerichte und moderne asiatische Küche – mit Sorgfalt für jeden Gast zubereitet.' ); ?></p>
		</div>
	</section>
	<section class="editorial-split content-container">
		<div class="editorial-split__image">
			<picture>
				<source srcset="<?php echo esc_url( get_template_directory_uri() . '/assets/images/headers/header-about.webp' ); ?>" type="image/webp">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/headers/header-about.jpeg' ); ?>" alt="<?php echo esc_attr( $is_english ? 'A Saiyoky chef preparing fresh sushi' : 'Ein Saiyoky-Koch bereitet frisches Sushi zu' ); ?>" loading="lazy" decoding="async" width="1921" height="819">
			</picture>
		</div>
		<div class="editorial-split__content">
			<p class="eyebrow"><?php echo esc_html( $is_english ? 'Our kitchen' : 'Unsere Küche' ); ?></p>
			<h2><?php echo esc_html( $is_english ? 'Craft, freshness and flavour in every dish' : 'Handwerk, Frische und Geschmack auf jedem Teller' ); ?></h2>
			<p><?php echo esc_html( $is_english ? 'Saiyoky brings together the precision of Japanese sushi culture and the warmth of Vietnamese and Asian cooking. We prepare every order individually, with carefully selected ingredients and a clear focus on balanced flavour.' : 'Saiyoky verbindet die Präzision japanischer Sushi-Kultur mit der Wärme der vietnamesischen und asiatischen Küche. Jede Bestellung wird individuell zubereitet – mit sorgfältig ausgewählten Zutaten und einem klaren Blick für ausgewogenen Geschmack.' ); ?></p>
			<p><?php echo esc_html( $is_english ? 'Our restaurant in Fürstenau is a place for an unhurried meal, a quick collection or convenient delivery. Whichever way you choose, you receive the same care from our kitchen.' : 'Unser Restaurant in Fürstenau ist ein Ort für ein entspanntes Essen, eine schnelle Abholung oder eine bequeme Lieferung. Wie Sie sich auch entscheiden: Sie erhalten immer dieselbe Sorgfalt aus unserer Küche.' ); ?></p>
			<a class="button" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php echo esc_html( $is_english ? 'Discover our menu' : 'Speisekarte entdecken' ); ?></a>
		</div>
	</section>
	<section class="values-section">
		<div class="content-container values-grid">
			<div data-reveal><span>01</span><h3><?php esc_html_e( 'Frisch', 'saiyoky' ); ?></h3><p><?php esc_html_e( 'Wir bereiten Ihre Bestellung frisch zu.', 'saiyoky' ); ?></p></div>
			<div data-reveal><span>02</span><h3><?php esc_html_e( 'Vielfältig', 'saiyoky' ); ?></h3><p><?php esc_html_e( 'Sushi und ausgewählte asiatische Lieblingsgerichte.', 'saiyoky' ); ?></p></div>
			<div data-reveal><span>03</span><h3><?php esc_html_e( 'Flexibel', 'saiyoky' ); ?></h3><p><?php esc_html_e( 'Bei uns essen, abholen oder liefern lassen.', 'saiyoky' ); ?></p></div>
		</div>
	</section>

	<?php get_template_part( 'template-parts/menu-book' ); ?>

	<section class="about-contact-section">
		<div class="content-container about-contact-layout">
			<div class="about-contact-intro" data-reveal>
				<p class="eyebrow"><?php esc_html_e( 'Alles auf einen Blick', 'saiyoky' ); ?></p>
				<h2><?php esc_html_e( 'Besuchen oder kontaktieren Sie uns', 'saiyoky' ); ?></h2>
				<p><?php esc_html_e( 'Ob Tischreservierung, Bestellung oder eine Frage zu unserer Speisekarte – wir freuen uns, von Ihnen zu hören.', 'saiyoky' ); ?></p>
				<div class="button-row">
					<a class="button button--primary" href="<?php echo esc_url( home_url( '/tisch-reservieren/' ) ); ?>"><?php esc_html_e( 'Tisch reservieren', 'saiyoky' ); ?></a>
					<a class="button button--ghost" href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>"><?php esc_html_e( 'Zum Kontakt', 'saiyoky' ); ?></a>
				</div>
			</div>
			<div class="about-contact-details" data-reveal>
				<div><span>01</span><p class="eyebrow"><?php esc_html_e( 'Adresse', 'saiyoky' ); ?></p><address><?php echo esc_html( saiyoky_business_detail( 'street' ) ); ?><br><?php echo esc_html( saiyoky_business_detail( 'city' ) ); ?><br><?php esc_html_e( 'Deutschland', 'saiyoky' ); ?></address></div>
				<div><span>02</span><p class="eyebrow"><?php esc_html_e( 'Direkter Kontakt', 'saiyoky' ); ?></p><a href="<?php echo esc_url( saiyoky_phone_href() ); ?>"><?php echo esc_html( saiyoky_business_detail( 'phone' ) ); ?></a><br><a href="mailto:<?php echo esc_attr( saiyoky_business_detail( 'email' ) ); ?>"><?php echo esc_html( saiyoky_business_detail( 'email' ) ); ?></a></div>
				<div><span>03</span><p class="eyebrow"><?php esc_html_e( 'Öffnungszeiten', 'saiyoky' ); ?></p><p><?php echo esc_html( saiyoky_business_detail( 'hours_week' ) ); ?></p></div>
				<div><span>04</span><p class="eyebrow"><?php esc_html_e( 'Online', 'saiyoky' ); ?></p><a href="<?php echo esc_url( saiyoky_business_detail( 'facebook' ) ); ?>" target="_blank" rel="noopener noreferrer">Facebook ↗</a></div>
			</div>
		</div>
	</section>
</main>
<?php get_footer(); ?>
