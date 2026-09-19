<?php
/**
 * Reservation page.
 *
 * @package Saiyoky
 */

get_header();
$page_language = function_exists( 'saiyoky_current_language' ) ? saiyoky_current_language() : get_locale();
$is_english    = str_starts_with( strtolower( (string) $page_language ), 'en' );
?>
<main id="main" class="site-main editorial-page">
	<section class="inner-hero inner-hero--compact inner-hero--reservation">
		<div class="content-container">
			<p class="eyebrow"><?php echo esc_html( $is_english ? 'Your table at Saiyoky' : 'Ihr Tisch bei Saiyoky' ); ?></p>
			<h1><?php echo esc_html( $is_english ? 'Reserve a table' : 'Tisch reservieren' ); ?></h1>
			<p><?php echo esc_html( $is_english ? 'Send us your reservation request. We will confirm your table by phone or email.' : 'Senden Sie uns Ihre Reservierungsanfrage. Wir bestätigen Ihren Tisch anschließend telefonisch oder per E-Mail.' ); ?></p>
		</div>
	</section>
	<section class="reservation-layout content-container">
		<div><?php get_template_part( 'template-parts/reservation-form' ); ?></div>
		<aside class="reservation-aside">
			<h2><?php echo esc_html( $is_english ? 'Prefer to speak to us?' : 'Lieber direkt sprechen?' ); ?></h2>
			<p><?php echo esc_html( $is_english ? 'For short-notice reservations, please call us.' : 'Für kurzfristige Reservierungen rufen Sie uns bitte an.' ); ?></p>
			<a class="button button--outline" href="<?php echo esc_url( saiyoky_phone_href() ); ?>"><?php echo esc_html( saiyoky_business_detail( 'phone' ) ); ?></a>
			<hr>
			<p><strong><?php echo esc_html( $is_english ? 'Opening hours' : 'Öffnungszeiten' ); ?></strong><br><?php echo esc_html( saiyoky_business_detail( 'hours_week' ) ); ?></p>
		</aside>
	</section>
</main>
<?php get_footer(); ?>
