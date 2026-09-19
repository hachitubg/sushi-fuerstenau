<?php
/**
 * Contact page.
 *
 * @package Saiyoky
 */

get_header();

$map_query      = saiyoky_business_detail( 'street' ) . ', ' . saiyoky_business_detail( 'city' ) . ', Germany';
$map_embed_url  = add_query_arg( array( 'q' => $map_query, 'output' => 'embed' ), 'https://www.google.com/maps' );
$directions_url = add_query_arg( array( 'api' => '1', 'query' => $map_query ), 'https://www.google.com/maps/search/' );
?>
<main id="main" class="site-main editorial-page">
	<section class="inner-hero inner-hero--compact inner-hero--contact">
		<div class="content-container">
			<p class="eyebrow"><?php esc_html_e( 'Kontakt & Anfahrt', 'saiyoky' ); ?></p>
			<h1><?php esc_html_e( 'Wir freuen uns auf Sie.', 'saiyoky' ); ?></h1>
		</div>
	</section>
	<section class="contact-map-section content-container" data-reveal>
		<header class="section-heading contact-map-heading">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'So finden Sie uns', 'saiyoky' ); ?></p>
				<h2><?php esc_html_e( 'Mitten in Fürstenau', 'saiyoky' ); ?></h2>
				<p><?php esc_html_e( 'Planen Sie Ihre Anfahrt und öffnen Sie die Route direkt in Google Maps.', 'saiyoky' ); ?></p>
			</div>
			<a class="button button--primary" href="<?php echo esc_url( $directions_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Route öffnen', 'saiyoky' ); ?> →</a>
		</header>
		<div class="contact-map-frame">
			<iframe src="<?php echo esc_url( $map_embed_url ); ?>" title="<?php esc_attr_e( 'Karte mit dem Standort von Saiyoky Fürstenau', 'saiyoky' ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
			<div class="contact-map-address">
				<strong>Saiyoky</strong>
				<span><?php echo esc_html( saiyoky_business_detail( 'street' ) ); ?><br><?php echo esc_html( saiyoky_business_detail( 'city' ) ); ?></span>
			</div>
		</div>
	</section>
	<section class="contact-grid content-container">
		<div class="contact-card">
			<p class="eyebrow"><?php esc_html_e( 'Adresse', 'saiyoky' ); ?></p>
			<h2><?php esc_html_e( 'Saiyoky Fürstenau', 'saiyoky' ); ?></h2>
			<address><?php echo esc_html( saiyoky_business_detail( 'street' ) ); ?><br><?php echo esc_html( saiyoky_business_detail( 'city' ) ); ?><br><?php esc_html_e( 'Deutschland', 'saiyoky' ); ?></address>
			<a class="text-link" href="<?php echo esc_url( $directions_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Route öffnen', 'saiyoky' ); ?> →</a>
		</div>
		<div class="contact-card">
			<p class="eyebrow"><?php esc_html_e( 'Direkter Kontakt', 'saiyoky' ); ?></p>
			<h2><?php esc_html_e( 'Bestellung & Fragen', 'saiyoky' ); ?></h2>
			<p><a href="<?php echo esc_url( saiyoky_phone_href() ); ?>"><?php echo esc_html( saiyoky_business_detail( 'phone' ) ); ?></a><br><a href="mailto:<?php echo esc_attr( saiyoky_business_detail( 'email' ) ); ?>"><?php echo esc_html( saiyoky_business_detail( 'email' ) ); ?></a></p>
			<p class="contact-note"><?php esc_html_e( 'Die E-Mail-Adresse ist vorläufig und wird vor Veröffentlichung bestätigt.', 'saiyoky' ); ?></p>
		</div>
		<div class="contact-card">
			<p class="eyebrow"><?php esc_html_e( 'Öffnungszeiten', 'saiyoky' ); ?></p>
			<h2><?php esc_html_e( 'Heute frisch für Sie', 'saiyoky' ); ?></h2>
			<p><?php echo esc_html( saiyoky_business_detail( 'hours_week' ) ); ?></p>
			<a class="text-link" href="<?php echo esc_url( saiyoky_business_detail( 'facebook' ) ); ?>" target="_blank" rel="noopener noreferrer">Facebook →</a>
		</div>
	</section>
</main>
<?php get_footer(); ?>
