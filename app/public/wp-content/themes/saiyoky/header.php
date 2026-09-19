<?php
/**
 * Landing page header.
 *
 * @package Saiyoky
 */

$is_english     = function_exists( 'saiyoky_current_language' ) && str_starts_with( strtolower( saiyoky_current_language() ), 'en' );
$phone          = function_exists( 'saiyoky_business_detail' ) ? saiyoky_business_detail( 'phone' ) : '0590 1825 9476';
$whatsapp_phone = '49' . ltrim( preg_replace( '/\D+/', '', $phone ), '0' );
$whatsapp_url   = 'https://wa.me/' . $whatsapp_phone;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php echo esc_html( $is_english ? 'Skip to content' : 'Zum Inhalt springen' ); ?></a>
<header class="site-header">
	<div class="site-header__inner content-container">
		<a class="site-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<img class="site-brand__mark" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/brand/logo-symbol-sharp.png' ); ?>" width="912" height="900" alt="" fetchpriority="high">
			<span class="site-brand__copy"><strong>SAIYOKY</strong><small>Fine Sushi · Street Food</small></span>
		</a>
		<nav class="primary-navigation" aria-label="<?php echo esc_attr( $is_english ? 'Main navigation' : 'Hauptnavigation' ); ?>">
			<ul id="primary-menu">
				<li><a href="<?php echo esc_url( home_url( '/#about' ) ); ?>"><?php echo esc_html( $is_english ? 'About us' : 'Über uns' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/#menu-book' ) ); ?>"><?php echo esc_html( $is_english ? 'Menu' : 'Speisekarte' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/#reserve' ) ); ?>"><?php echo esc_html( $is_english ? 'Reserve' : 'Reservieren' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/#visit' ) ); ?>"><?php echo esc_html( $is_english ? 'Opening hours' : 'Öffnungszeiten' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/#contact' ) ); ?>"><?php echo esc_html( $is_english ? 'Contact' : 'Kontakt' ); ?></a></li>
			</ul>
		</nav>
		<div class="header-actions">
			<a class="header-whatsapp" href="<?php echo esc_url( $whatsapp_url ); ?>" target="_blank" rel="noopener noreferrer">WhatsApp</a>
		</div>
		<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-menu">
			<span class="screen-reader-text"><?php echo esc_html( $is_english ? 'Open menu' : 'Menü öffnen' ); ?></span>
			<span aria-hidden="true"><span class="menu-toggle__bar"></span><span class="menu-toggle__bar"></span><span class="menu-toggle__bar"></span></span>
		</button>
	</div>
</header>
