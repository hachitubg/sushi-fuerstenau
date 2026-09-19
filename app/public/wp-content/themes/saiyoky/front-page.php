<?php
/**
 * Minimal restaurant landing page.
 *
 * @package Saiyoky
 */

get_header();

$theme_images   = get_template_directory_uri() . '/assets/images/';
$uploads_url    = content_url( '/uploads' );
$is_english     = str_starts_with( strtolower( saiyoky_current_language() ), 'en' );
$phone          = saiyoky_business_detail( 'phone' );
$whatsapp_phone = '49' . ltrim( preg_replace( '/\D+/', '', $phone ), '0' );
$whatsapp_text  = $is_english ? 'Hello Saiyoky, I would like to ask about your menu.' : 'Hallo Saiyoky, ich habe eine Frage zu Ihrer Speisekarte.';
$whatsapp_url   = 'https://wa.me/' . $whatsapp_phone . '?text=' . rawurlencode( $whatsapp_text );
$map_query      = saiyoky_business_detail( 'street' ) . ', ' . saiyoky_business_detail( 'city' );
$map_url        = add_query_arg( array( 'api' => '1', 'query' => $map_query ), 'https://www.google.com/maps/search/' );
?>
<main id="main" class="site-main home-page">
	<section class="home-hero" aria-labelledby="home-title">
		<div class="content-container home-hero__inner">
			<div class="home-hero__copy">
				<img class="home-hero__logo" src="<?php echo esc_url( $theme_images . 'brand/logo-symbol-sharp.png' ); ?>" width="912" height="900" alt="" fetchpriority="high">
				<p class="eyebrow"><?php echo esc_html( $is_english ? 'Sushi & Asian Kitchen in Fürstenau' : 'Sushi & Asian Kitchen in Fürstenau' ); ?></p>
				<h1 id="home-title">Saiyoky <span><?php echo esc_html( $is_english ? 'Asian Kitchen' : 'Asiatische Küche' ); ?></span></h1>
				<p class="home-hero__intro"><?php echo esc_html( $is_english ? 'Fresh sushi and selected Asian specialities, carefully prepared for you in the heart of Fürstenau.' : 'Frisches Sushi und ausgewählte asiatische Spezialitäten, sorgfältig für Sie im Herzen von Fürstenau zubereitet.' ); ?></p>
				<div class="home-hero__buttons">
					<a class="button" href="#reserve"><?php echo esc_html( $is_english ? 'Book a table' : 'Tisch reservieren' ); ?></a>
					<a class="button button--light" href="#menu-book"><?php echo esc_html( $is_english ? 'View menu' : 'Speisekarte ansehen' ); ?></a>
				</div>
			</div>
			<figure class="home-hero__photo">
				<img src="<?php echo esc_url( $theme_images . 'banner-1.jpg' ); ?>" width="2552" height="2699" alt="<?php echo esc_attr( $is_english ? 'Fresh sushi at Saiyoky Fürstenau' : 'Frisches Sushi bei Saiyoky Fürstenau' ); ?>" fetchpriority="high">
			</figure>
		</div>
	</section>

	<section class="quick-contact" aria-label="<?php echo esc_attr( $is_english ? 'Restaurant information' : 'Restaurantinformationen' ); ?>">
		<div class="content-container quick-contact__grid">
			<div class="quick-contact__item">
				<small><?php echo esc_html( $is_english ? 'Opening hours' : 'Öffnungszeiten' ); ?></small>
				<strong><?php echo esc_html( saiyoky_business_detail( 'hours_week' ) ); ?></strong>
			</div>
			<a class="quick-contact__item" href="<?php echo esc_url( $map_url ); ?>" target="_blank" rel="noopener noreferrer">
				<small><?php echo esc_html( $is_english ? 'Address' : 'Adresse' ); ?></small>
				<strong><?php echo esc_html( saiyoky_business_detail( 'street' ) ); ?></strong>
				<span><?php echo esc_html( saiyoky_business_detail( 'city' ) ); ?></span>
			</a>
			<a class="quick-contact__item" href="<?php echo esc_url( saiyoky_phone_href() ); ?>">
				<small><?php echo esc_html( $is_english ? 'Phone' : 'Telefon' ); ?></small>
				<strong><?php echo esc_html( $phone ); ?></strong>
				<span><?php echo esc_html( $is_english ? 'Questions and reservations' : 'Fragen und Reservierungen' ); ?></span>
			</a>
		</div>
	</section>

	<section id="about" class="about-section">
		<div class="content-container about-section__grid">
			<div class="about-section__media about-section__media--legacy" data-reveal>
				<figure class="about-section__media-main"><img src="<?php echo esc_url( $theme_images . 'headers/header-about.webp' ); ?>" width="1921" height="819" alt="<?php echo esc_attr( $is_english ? 'Saiyoky kitchen and fresh sushi' : 'Saiyoky Küche und frisches Sushi' ); ?>" loading="lazy" decoding="async"></figure>
				<figure><img src="<?php echo esc_url( $theme_images . 'products/special-roll.jpeg' ); ?>" width="1200" height="800" alt="Special Roll" loading="lazy" decoding="async"></figure>
				<figure><img src="<?php echo esc_url( $theme_images . 'products/aburi-salmon.jpeg' ); ?>" width="1200" height="800" alt="Aburi Salmon" loading="lazy" decoding="async"></figure>
			</div>
			<div class="about-section__media about-section__media--uploads" data-reveal>
				<figure class="about-section__media-main"><img src="<?php echo esc_url( $theme_images . 'headers/header-contact.webp' ); ?>" width="1938" height="812" alt="Stimmungsvoller Gastraum bei Saiyoky" loading="lazy" decoding="async"></figure>
				<figure><img src="<?php echo esc_url( $theme_images . 'headers/header-reservation.webp' ); ?>" width="1983" height="793" alt="Reservierter Tisch bei Saiyoky" loading="lazy" decoding="async"></figure>
				<figure><img src="<?php echo esc_url( $uploads_url . '/2026/07/saiyoky-drinks.jpeg' ); ?>" width="850" height="1192" alt="Getränke bei Saiyoky" loading="lazy" decoding="async"></figure>
			</div>
			<div class="about-section__media about-section__media--attachments" data-reveal>
				<figure class="about-section__media-main"><img src="<?php echo esc_url( $theme_images . 'gallery/banner_4.jpg' ); ?>" width="2552" height="2723" alt="Warme asiatische Spezialitäten bei Saiyoky" loading="lazy" decoding="async"></figure>
				<figure><img src="<?php echo esc_url( $theme_images . 'gallery/banner_3.jpg' ); ?>" width="2552" height="2603" alt="Getränke bei Saiyoky" loading="lazy" decoding="async"></figure>
				<figure><img src="<?php echo esc_url( $theme_images . 'gallery/banner_2.jpg' ); ?>" width="2552" height="2891" alt="Dimsum und asiatische Spezialitäten" loading="lazy" decoding="async"></figure>
			</div>
			<div class="about-section__copy" data-reveal>
				<p class="eyebrow"><?php echo esc_html( $is_english ? 'About us' : 'Über uns' ); ?></p>
				<h2><?php echo esc_html( $is_english ? 'Freshly prepared, warmly served.' : 'Frisch zubereitet, herzlich serviert.' ); ?></h2>
				<p><?php echo esc_html( $is_english ? 'At Saiyoky, Japanese precision meets the variety of Asian cuisine. Our dishes are freshly prepared with carefully selected ingredients and a focus on balanced flavours.' : 'Bei Saiyoky trifft japanische Präzision auf die Vielfalt der asiatischen Küche. Unsere Gerichte werden frisch, mit sorgfältig ausgewählten Zutaten und einem Gespür für ausgewogene Aromen zubereitet.' ); ?></p>
				<p><?php echo esc_html( $is_english ? 'Whether for lunch, dinner or a relaxed evening with friends, our team looks forward to welcoming you in Fürstenau.' : 'Ob zum Mittagessen, Abendessen oder für einen entspannten Abend mit Freunden: Unser Team freut sich darauf, Sie in Fürstenau willkommen zu heißen.' ); ?></p>
				<div class="about-section__tags" aria-label="<?php echo esc_attr( $is_english ? 'Our offer' : 'Unser Angebot' ); ?>">
					<span class="about-section__tag">Sushi & Maki</span>
					<span class="about-section__tag"><?php echo esc_html( $is_english ? 'Warm Asian dishes' : 'Warme Asia-Gerichte' ); ?></span>
					<span class="about-section__tag"><?php echo esc_html( $is_english ? 'Contact via WhatsApp' : 'Kontakt per WhatsApp' ); ?></span>
				</div>
			</div>
		</div>
	</section>

	<section class="food-gallery-section" aria-labelledby="food-gallery-title">
		<div class="content-container">
			<header class="food-gallery-section__heading" data-reveal>
				<p class="eyebrow"><?php echo esc_html( $is_english ? 'From our kitchen' : 'Aus unserer Küche' ); ?></p>
				<h2 id="food-gallery-title"><?php echo esc_html( $is_english ? 'Real impressions from our menu' : 'Echte Eindrücke aus unserer Küche' ); ?></h2>
				<p><?php echo esc_html( $is_english ? 'Freshly prepared, clearly presented and exactly what awaits you at Saiyoky.' : 'Frisch zubereitet, klar präsentiert und genau das, was Sie bei Saiyoky erwartet.' ); ?></p>
			</header>
			<div class="food-gallery-grid food-gallery-grid--legacy">
				<figure class="food-gallery-card food-gallery-card--feature"><img src="<?php echo esc_url( $uploads_url . '/2026/07/saiyoky-special-roll.jpeg' ); ?>" alt="Special Roll" loading="lazy"><figcaption>Special Rolls</figcaption></figure>
				<figure class="food-gallery-card"><img src="<?php echo esc_url( $theme_images . 'products/aburi-salmon.jpeg' ); ?>" alt="Aburi Salmon" loading="lazy"><figcaption>Aburi Salmon</figcaption></figure>
				<figure class="food-gallery-card"><img src="<?php echo esc_url( $theme_images . 'products/tuna-nigiri.jpeg' ); ?>" alt="Tuna Nigiri" loading="lazy"><figcaption>Tuna Nigiri</figcaption></figure>
				<figure class="food-gallery-card"><img src="<?php echo esc_url( $theme_images . 'products/maki.jpeg' ); ?>" alt="Maki" loading="lazy"><figcaption>Maki</figcaption></figure>
				<figure class="food-gallery-card"><img src="<?php echo esc_url( $theme_images . 'products/unagi-nigiri.jpeg' ); ?>" alt="Unagi Nigiri" loading="lazy"><figcaption>Unagi Nigiri</figcaption></figure>
			</div>
			<div class="food-gallery-divider" data-reveal>
				<span><?php echo esc_html( $is_english ? 'Freshly prepared · selected ingredients · Asian variety' : 'Frisch zubereitet · ausgewählte Zutaten · asiatische Vielfalt' ); ?></span>
			</div>
			<div class="food-gallery-grid food-gallery-grid--uploads">
				<figure class="food-gallery-card food-gallery-card--feature"><img src="<?php echo esc_url( $uploads_url . '/saiyoky-generated-menu/mango-main.webp' ); ?>" alt="Mango Hauptgericht" loading="lazy"><figcaption>Mango Spezialität</figcaption></figure>
				<figure class="food-gallery-card food-gallery-card--wide"><img src="<?php echo esc_url( $uploads_url . '/saiyoky-generated-menu/beef-prawn.webp' ); ?>" alt="Rindfleisch mit Garnelen" loading="lazy"><figcaption>Beef & Prawn</figcaption></figure>
				<figure class="food-gallery-card"><img src="<?php echo esc_url( $uploads_url . '/saiyoky-generated-menu/salmon-tartar.webp' ); ?>" alt="Salmon Tartar" loading="lazy"><figcaption>Salmon Tartar</figcaption></figure>
				<figure class="food-gallery-card"><img src="<?php echo esc_url( $uploads_url . '/saiyoky-generated-menu/ha-cao.webp' ); ?>" alt="Ha Cao Dumplings" loading="lazy"><figcaption>Ha Cao</figcaption></figure>
				<figure class="food-gallery-card"><img src="<?php echo esc_url( $uploads_url . '/saiyoky-generated-menu/sate.webp' ); ?>" alt="Saté Spieße" loading="lazy"><figcaption>Saté</figcaption></figure>
				<figure class="food-gallery-card"><img src="<?php echo esc_url( $uploads_url . '/saiyoky-generated-menu/spring-rolls.webp' ); ?>" alt="Frühlingsrollen" loading="lazy"><figcaption>Frühlingsrollen</figcaption></figure>
				<figure class="food-gallery-card"><img src="<?php echo esc_url( $uploads_url . '/saiyoky-generated-menu/summer-rolls.webp' ); ?>" alt="Sommerrollen" loading="lazy"><figcaption>Sommerrollen</figcaption></figure>
				<figure class="food-gallery-card"><img src="<?php echo esc_url( $uploads_url . '/saiyoky-generated-menu/bun-bo.webp' ); ?>" alt="Bún Bò" loading="lazy"><figcaption>Bún Bò</figcaption></figure>
				<figure class="food-gallery-card"><img src="<?php echo esc_url( $uploads_url . '/saiyoky-generated-menu/pho.webp' ); ?>" alt="Pho" loading="lazy"><figcaption>Pho</figcaption></figure>
				<figure class="food-gallery-card"><img src="<?php echo esc_url( $uploads_url . '/saiyoky-generated-menu/curry.webp' ); ?>" alt="Curry mit Reis" loading="lazy"><figcaption>Asia-Curry</figcaption></figure>
				<figure class="food-gallery-card"><img src="<?php echo esc_url( $uploads_url . '/saiyoky-generated-menu/edamame.webp' ); ?>" alt="Edamame" loading="lazy"><figcaption>Edamame</figcaption></figure>
				<figure class="food-gallery-card"><img src="<?php echo esc_url( $uploads_url . '/saiyoky-generated-menu/canh-chua.webp' ); ?>" alt="Canh Chua" loading="lazy"><figcaption>Canh Chua</figcaption></figure>
				<figure class="food-gallery-card"><img src="<?php echo esc_url( $uploads_url . '/saiyoky-generated-menu/udon.webp' ); ?>" alt="Udon" loading="lazy"><figcaption>Udon</figcaption></figure>
				<figure class="food-gallery-card"><img src="<?php echo esc_url( $uploads_url . '/saiyoky-generated-menu/sweet-sour.webp' ); ?>" alt="Sweet and Sour Spezialität" loading="lazy"><figcaption>Sweet & Sour</figcaption></figure>
				<figure class="food-gallery-card"><img src="<?php echo esc_url( $uploads_url . '/saiyoky-generated-menu/green-garden.webp' ); ?>" alt="Green Garden Bowl" loading="lazy"><figcaption>Green Garden</figcaption></figure>
				<figure class="food-gallery-card"><img src="<?php echo esc_url( $uploads_url . '/saiyoky-generated-menu/childhood-1.webp' ); ?>" alt="Saiyoky Spezialität" loading="lazy"><figcaption>Unsere Klassiker</figcaption></figure>
			</div>
		</div>
	</section>

	<?php get_template_part( 'template-parts/menu-book' ); ?>

	<section id="reserve" class="reservation-section">
		<div class="content-container reservation-section__inner">
			<div class="reservation-section__intro">
				<p class="eyebrow"><?php echo esc_html( $is_english ? 'Reserve a table' : 'Tisch reservieren' ); ?></p>
				<h2><?php echo esc_html( $is_english ? 'Your table at Saiyoky.' : 'Ihr Tisch bei Saiyoky.' ); ?></h2>
				<p><?php echo esc_html( $is_english ? 'Send us your preferred date and time. We will confirm your request personally.' : 'Senden Sie uns Ihren Wunschtermin. Wir bestätigen Ihre Anfrage persönlich.' ); ?></p>
				<div class="reservation-section__hours">
					<strong><?php echo esc_html( $is_english ? 'Opening hours' : 'Öffnungszeiten' ); ?></strong>
					<span><?php echo esc_html( saiyoky_business_detail( 'hours_week' ) ); ?></span>
				</div>
			</div>
			<div class="reservation-section__form">
				<?php get_template_part( 'template-parts/reservation-form' ); ?>
			</div>
		</div>
	</section>

	<section id="visit" class="visit-section">
		<div class="content-container visit-section__inner">
			<div class="opening-hours-panel">
				<p class="eyebrow"><?php echo esc_html( $is_english ? 'Opening hours' : 'Öffnungszeiten' ); ?></p>
				<h2><?php echo esc_html( $is_english ? 'Welcome to Fürstenau' : 'Willkommen in Fürstenau' ); ?></h2>
				<ul class="opening-hours-list">
					<li><span>Montag</span><strong>11:00–22:00</strong></li>
					<li><span>Dienstag</span><strong>11:00–22:00</strong></li>
					<li><span>Mittwoch</span><strong>11:00–22:00</strong></li>
					<li><span>Donnerstag</span><strong>11:00–22:00</strong></li>
					<li><span>Freitag</span><strong>11:00–22:00</strong></li>
					<li><span>Samstag</span><strong>11:00–22:00</strong></li>
					<li><span>Sonntag</span><strong>11:00–22:00</strong></li>
				</ul>
			</div>
			<div id="contact" class="visit-contact-card">
				<p class="eyebrow"><?php echo esc_html( $is_english ? 'Contact' : 'Kontakt' ); ?></p>
				<h2>Saiyoky <?php echo esc_html( $is_english ? 'Asian Kitchen' : 'Asiatische Küche' ); ?></h2>
				<a class="visit-contact-card__address" href="<?php echo esc_url( $map_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( saiyoky_business_detail( 'street' ) ); ?><br><?php echo esc_html( saiyoky_business_detail( 'city' ) ); ?></a>
				<div class="visit-contact-card__actions">
					<a class="button" href="<?php echo esc_url( saiyoky_phone_href() ); ?>"><?php echo esc_html( $is_english ? 'Call' : 'Anrufen' ); ?></a>
					<a class="button" href="<?php echo esc_url( $whatsapp_url ); ?>" target="_blank" rel="noopener noreferrer">WhatsApp</a>
					<a class="button" href="<?php echo esc_url( $map_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $is_english ? 'Route' : 'Route' ); ?></a>
				</div>
				<div class="visit-section__map">
				<iframe src="<?php echo esc_url( add_query_arg( array( 'q' => $map_query, 'output' => 'embed' ), 'https://www.google.com/maps' ) ); ?>" title="<?php echo esc_attr( $is_english ? 'Map showing Saiyoky Fürstenau' : 'Karte mit dem Standort von Saiyoky Fürstenau' ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
				</div>
			</div>
		</div>
	</section>
</main>

<a class="whatsapp-float" href="<?php echo esc_url( $whatsapp_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $is_english ? 'Contact Saiyoky via WhatsApp' : 'Saiyoky über WhatsApp kontaktieren' ); ?>">WhatsApp</a>

<?php get_footer(); ?>
