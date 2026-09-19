<?php
/**
 * Saiyoky theme bootstrap.
 *
 * @package Saiyoky
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SAIYOKY_VERSION', '0.9.2' );

function saiyoky_setup(): void {
	load_theme_textdomain( 'saiyoky', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary navigation', 'saiyoky' ),
			'footer'  => __( 'Footer navigation', 'saiyoky' ),
		)
	);
}
add_action( 'after_setup_theme', 'saiyoky_setup' );

function saiyoky_assets(): void {
	$main_css = get_template_directory() . '/assets/css/main.css';
	$main_js  = get_template_directory() . '/assets/js/main.js';

	wp_enqueue_style(
		'saiyoky-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array(),
		file_exists( $main_css ) ? (string) filemtime( $main_css ) : SAIYOKY_VERSION
	);

	wp_enqueue_script(
		'saiyoky-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array(),
		file_exists( $main_js ) ? (string) filemtime( $main_js ) : SAIYOKY_VERSION,
		true
	);

	wp_script_add_data( 'saiyoky-main', 'strategy', 'defer' );
}
add_action( 'wp_enqueue_scripts', 'saiyoky_assets' );

/**
 * Keep the public frontend lean without changing WooCommerce/admin behavior.
 */
function saiyoky_frontend_performance(): void {
	if ( is_admin() ) {
		return;
	}

	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );

}
add_action( 'init', 'saiyoky_frontend_performance' );

function saiyoky_is_storefront_view(): bool {
	return function_exists( 'is_woocommerce' )
		&& ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() );
}

function saiyoky_trim_non_storefront_assets(): void {
	if ( is_admin() ) {
		return;
	}

	if ( ! is_user_logged_in() ) {
		wp_dequeue_style( 'dashicons' );
		wp_deregister_style( 'dashicons' );
	}

	if ( saiyoky_is_storefront_view() ) {
		return;
	}

	$styles = array(
		'woocommerce-general',
		'woocommerce-layout',
		'woocommerce-smallscreen',
		'wc-blocks-style',
		'wc-blocks-vendors-style',
		'wc-blocks-packages-style',
		'wc-blocks-style-all-products',
	);

	foreach ( $styles as $style ) {
		wp_dequeue_style( $style );
	}

	$scripts = array(
		'wc-add-to-cart',
		'woocommerce',
		'wc-cart-fragments',
		'jquery-blockui',
		'js-cookie',
		'sourcebuster-js',
		'wc-order-attribution',
	);

	foreach ( $scripts as $script ) {
		wp_dequeue_script( $script );
	}
}
add_action( 'wp_enqueue_scripts', 'saiyoky_trim_non_storefront_assets', 999 );
// WooCommerce Blocks may enqueue its global stylesheet after wp_enqueue_scripts.
// Run the same idempotent cleanup immediately before styles are printed.
add_action( 'wp_print_styles', 'saiyoky_trim_non_storefront_assets', 999 );

/**
 * Prevent late WooCommerce Blocks styles from being printed on content pages.
 * Some WooCommerce releases enqueue this handle after the normal dequeue pass.
 */
function saiyoky_filter_non_storefront_style_tag( string $html, string $handle ): string {
	if ( is_admin() || saiyoky_is_storefront_view() ) {
		return $html;
	}

	$storefront_only_styles = array(
		'woocommerce-general',
		'woocommerce-layout',
		'woocommerce-smallscreen',
		'wc-blocks-style',
		'wc-blocks-vendors-style',
		'wc-blocks-packages-style',
		'wc-blocks-style-all-products',
	);

	return in_array( $handle, $storefront_only_styles, true ) ? '' : $html;
}
add_filter( 'style_loader_tag', 'saiyoky_filter_non_storefront_style_tag', 10, 2 );

function saiyoky_preload_frontend_assets(): void {
	if ( is_admin() ) {
		return;
	}

	$theme_uri = get_template_directory_uri();

	echo '<link rel="preload" href="' . esc_url( $theme_uri . '/assets/images/brand/logo-symbol-sharp.png' ) . '" as="image" type="image/png">' . "\n";

	if ( is_front_page() ) {
		echo '<link rel="preload" href="' . esc_url( $theme_uri . '/assets/images/banner-1.jpg' ) . '" as="image" type="image/jpeg" fetchpriority="high">' . "\n";
	}

	if ( is_front_page() || is_page( 'kontakt' ) || is_page_template( 'page-kontakt.php' ) ) {
		echo '<link rel="preconnect" href="https://www.google.com" crossorigin>' . "\n";
		echo '<link rel="preconnect" href="https://maps.gstatic.com" crossorigin>' . "\n";
	}
}
add_action( 'wp_head', 'saiyoky_preload_frontend_assets', 1 );

/**
 * Use authenticated SMTP when the hosting credentials are defined in wp-config.php.
 */
function saiyoky_configure_smtp( PHPMailer\PHPMailer\PHPMailer $phpmailer ): void {
	if ( ! defined( 'SAIYOKY_SMTP_HOST' ) || ! defined( 'SAIYOKY_SMTP_USER' ) || ! defined( 'SAIYOKY_SMTP_PASSWORD' ) ) {
		return;
	}

	$phpmailer->isSMTP();
	$phpmailer->Host       = SAIYOKY_SMTP_HOST;
	$phpmailer->Port       = defined( 'SAIYOKY_SMTP_PORT' ) ? (int) SAIYOKY_SMTP_PORT : 587;
	$phpmailer->SMTPAuth   = true;
	$phpmailer->Username   = SAIYOKY_SMTP_USER;
	$phpmailer->Password   = SAIYOKY_SMTP_PASSWORD;
	$phpmailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
	$phpmailer->SMTPOptions = array(
		'ssl' => array(
			'verify_peer'       => false,
			'verify_peer_name'  => false,
			'allow_self_signed' => true,
		),
	);
	$phpmailer->setFrom( SAIYOKY_SMTP_USER, 'Saiyoky Sushi Fürstenau', false );
	$phpmailer->Sender = SAIYOKY_SMTP_USER;
}
add_action( 'phpmailer_init', 'saiyoky_configure_smtp' );

function saiyoky_products_per_page(): int {
	return 24;
}
add_filter( 'loop_shop_per_page', 'saiyoky_products_per_page' );

/**
 * The restaurant menu is intentionally a single, image-free ordering surface.
 * Product detail URLs therefore return visitors to the menu while preserving
 * the normal cart and checkout flow.
 */
function saiyoky_redirect_product_details(): void {
	if ( is_singular( 'product' ) && function_exists( 'wc_get_page_permalink' ) ) {
		wp_safe_redirect( wc_get_page_permalink( 'shop' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'saiyoky_redirect_product_details' );

/**
 * Store reservation requests in WordPress until the final restaurant email is available.
 */
function saiyoky_register_reservation_type(): void {
	register_post_type(
		'saiyoky_booking',
		array(
			'labels'       => array(
				'name'          => __( 'Reservierungen', 'saiyoky' ),
				'singular_name' => __( 'Reservierung', 'saiyoky' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-calendar-alt',
			'supports'     => array( 'title' ),
		)
	);
}
add_action( 'init', 'saiyoky_register_reservation_type' );

function saiyoky_handle_reservation(): void {
	$referer = wp_get_referer() ?: home_url( '/tisch-reservieren/' );

	if ( ! isset( $_POST['saiyoky_reservation_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['saiyoky_reservation_nonce'] ) ), 'saiyoky_reservation' ) ) {
		wp_safe_redirect( add_query_arg( 'reservation', 'invalid', $referer ) );
		exit;
	}

	if ( ! empty( $_POST['website'] ) ) {
		wp_safe_redirect( add_query_arg( 'reservation', 'success', $referer ) );
		exit;
	}

	$name   = isset( $_POST['guest_name'] ) ? sanitize_text_field( wp_unslash( $_POST['guest_name'] ) ) : '';
	$phone  = isset( $_POST['guest_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['guest_phone'] ) ) : '';
	$date   = isset( $_POST['booking_date'] ) ? sanitize_text_field( wp_unslash( $_POST['booking_date'] ) ) : '';
	$time   = isset( $_POST['booking_time'] ) ? sanitize_text_field( wp_unslash( $_POST['booking_time'] ) ) : '';
	$guests = isset( $_POST['guest_count'] ) ? absint( $_POST['guest_count'] ) : 0;

	if ( ! $name || ! $phone || ! $date || ! $time || $guests < 1 || $guests > 20 ) {
		wp_safe_redirect( add_query_arg( 'reservation', 'invalid', $referer ) );
		exit;
	}

	$booking_id = wp_insert_post(
		array(
			'post_type'   => 'saiyoky_booking',
			'post_status' => 'private',
			'post_title'  => sprintf( '%s — %s %s', $name, $date, $time ),
		),
		true
	);

	if ( is_wp_error( $booking_id ) ) {
		wp_safe_redirect( add_query_arg( 'reservation', 'error', $referer ) );
		exit;
	}

	$fields = array( 'guest_name', 'guest_phone', 'guest_email', 'booking_date', 'booking_time', 'guest_count', 'guest_message', 'booking_language' );
	foreach ( $fields as $field ) {
		$value = isset( $_POST[ $field ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $field ] ) ) : '';
		update_post_meta( $booking_id, '_' . $field, $value );
	}

	$notification_email = sanitize_email( (string) get_option( 'saiyoky_notification_email', get_option( 'admin_email' ) ) );
	if ( $notification_email ) {
		$email   = isset( $_POST['guest_email'] ) ? sanitize_email( wp_unslash( $_POST['guest_email'] ) ) : '';
		$message = isset( $_POST['guest_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['guest_message'] ) ) : '';
		$language = isset( $_POST['booking_language'] ) ? sanitize_text_field( wp_unslash( $_POST['booking_language'] ) ) : '';
		$subject = sprintf( '[Saiyoky] New table reservation: %s — %s %s', $name, $date, $time );
		$body    = implode(
			"\n",
			array(
				'Name: ' . $name,
				'Phone: ' . $phone,
				'Email: ' . ( $email ?: '—' ),
				'Date: ' . $date,
				'Time: ' . $time,
				'Guests: ' . $guests,
				'Language: ' . ( $language ?: '—' ),
				'Message: ' . ( $message ?: '—' ),
				'',
				'View reservations: ' . admin_url( 'edit.php?post_type=saiyoky_booking' ),
			)
		);
		$headers = $email ? array( 'Reply-To: ' . $name . ' <' . $email . '>' ) : array();
		wp_mail( $notification_email, $subject, $body, $headers );
	}

	wp_safe_redirect( add_query_arg( 'reservation', 'success', $referer ) );
	exit;
}
add_action( 'admin_post_nopriv_saiyoky_reservation', 'saiyoky_handle_reservation' );
add_action( 'admin_post_saiyoky_reservation', 'saiyoky_handle_reservation' );

/**
 * Limit the public menu search to product names.
 */
function saiyoky_prepare_menu_search( WP_Query $query ): void {
	if ( is_admin() || ! $query->is_main_query() || ! isset( $_GET['menu_search'] ) ) {
		return;
	}

	$search_term = sanitize_text_field( wp_unslash( $_GET['menu_search'] ) );
	if ( '' === $search_term ) {
		return;
	}

	$query->set( 'post_type', 'product' );
	$query->set( 'saiyoky_menu_search', $search_term );
}
add_action( 'pre_get_posts', 'saiyoky_prepare_menu_search' );

function saiyoky_product_title_search( string $search, WP_Query $query ): string {
	$search_term = $query->get( 'saiyoky_menu_search' );
	if ( ! is_string( $search_term ) || '' === $search_term ) {
		return $search;
	}

	global $wpdb;
	$like = '%' . $wpdb->esc_like( $search_term ) . '%';
	return $wpdb->prepare( " AND {$wpdb->posts}.post_title LIKE %s ", $like ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}
add_filter( 'posts_search', 'saiyoky_product_title_search', 10, 2 );

function saiyoky_booking_columns( array $columns ): array {
	return array(
		'cb'            => $columns['cb'],
		'title'         => __( 'Gast', 'saiyoky' ),
		'booking_date'  => __( 'Datum', 'saiyoky' ),
		'booking_time'  => __( 'Uhrzeit', 'saiyoky' ),
		'guest_count'   => __( 'Personen', 'saiyoky' ),
		'guest_phone'   => __( 'Telefon', 'saiyoky' ),
		'date'          => $columns['date'],
	);
}
add_filter( 'manage_saiyoky_booking_posts_columns', 'saiyoky_booking_columns' );

function saiyoky_booking_column_value( string $column, int $post_id ): void {
	$meta_keys = array(
		'booking_date' => '_booking_date',
		'booking_time' => '_booking_time',
		'guest_count'  => '_guest_count',
		'guest_phone'  => '_guest_phone',
	);
	if ( isset( $meta_keys[ $column ] ) ) {
		echo esc_html( (string) get_post_meta( $post_id, $meta_keys[ $column ], true ) );
	}
}
add_action( 'manage_saiyoky_booking_posts_custom_column', 'saiyoky_booking_column_value', 10, 2 );

/**
 * Add useful restaurant-customer fields to native WooCommerce registration.
 */
function saiyoky_registration_fields(): void {
	$first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '';
	$last_name  = isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '';
	$phone      = isset( $_POST['billing_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) ) : '';
	?>
	<div class="auth-field-grid">
		<p class="woocommerce-form-row form-row form-row-first">
			<label for="reg_first_name"><?php esc_html_e( 'Vorname', 'saiyoky' ); ?> <span class="required">*</span></label>
			<input type="text" class="woocommerce-Input input-text" name="first_name" id="reg_first_name" autocomplete="given-name" value="<?php echo esc_attr( $first_name ); ?>" required>
		</p>
		<p class="woocommerce-form-row form-row form-row-last">
			<label for="reg_last_name"><?php esc_html_e( 'Nachname', 'saiyoky' ); ?> <span class="required">*</span></label>
			<input type="text" class="woocommerce-Input input-text" name="last_name" id="reg_last_name" autocomplete="family-name" value="<?php echo esc_attr( $last_name ); ?>" required>
		</p>
	</div>
	<p class="woocommerce-form-row form-row form-row-wide">
		<label for="reg_billing_phone"><?php esc_html_e( 'Telefon', 'saiyoky' ); ?> <span class="required">*</span></label>
		<input type="tel" class="woocommerce-Input input-text" name="billing_phone" id="reg_billing_phone" autocomplete="tel" value="<?php echo esc_attr( $phone ); ?>" required>
	</p>
	<?php
}
add_action( 'woocommerce_register_form_start', 'saiyoky_registration_fields' );

function saiyoky_validate_registration_fields( WP_Error $errors ): WP_Error {
	if ( empty( $_POST['first_name'] ) ) {
		$errors->add( 'first_name_error', __( 'Bitte geben Sie Ihren Vornamen ein.', 'saiyoky' ) );
	}
	if ( empty( $_POST['last_name'] ) ) {
		$errors->add( 'last_name_error', __( 'Bitte geben Sie Ihren Nachnamen ein.', 'saiyoky' ) );
	}
	if ( empty( $_POST['billing_phone'] ) ) {
		$errors->add( 'billing_phone_error', __( 'Bitte geben Sie Ihre Telefonnummer ein.', 'saiyoky' ) );
	}
	return $errors;
}
add_filter( 'woocommerce_registration_errors', 'saiyoky_validate_registration_fields' );

function saiyoky_save_registration_fields( int $customer_id ): void {
	$fields = array( 'first_name', 'last_name', 'billing_phone' );
	foreach ( $fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_user_meta( $customer_id, $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
		}
	}
	if ( isset( $_POST['first_name'], $_POST['last_name'] ) ) {
		update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) );
		update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) );
	}
}
add_action( 'woocommerce_created_customer', 'saiyoky_save_registration_fields' );

function saiyoky_booking_meta_boxes(): void {
	add_meta_box(
		'saiyoky-booking-details',
		__( 'Reservierungsdetails', 'saiyoky' ),
		'saiyoky_booking_details_box',
		'saiyoky_booking',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_saiyoky_booking', 'saiyoky_booking_meta_boxes' );

function saiyoky_booking_details_box( WP_Post $post ): void {
	$fields = array(
		'guest_name'       => __( 'Name', 'saiyoky' ),
		'guest_phone'      => __( 'Telefon', 'saiyoky' ),
		'guest_email'      => __( 'E-Mail', 'saiyoky' ),
		'booking_date'     => __( 'Datum', 'saiyoky' ),
		'booking_time'     => __( 'Uhrzeit', 'saiyoky' ),
		'guest_count'      => __( 'Personen', 'saiyoky' ),
		'guest_message'    => __( 'Nachricht', 'saiyoky' ),
		'booking_language' => __( 'Sprache', 'saiyoky' ),
	);
	echo '<table class="widefat striped"><tbody>';
	foreach ( $fields as $key => $label ) {
		$value = (string) get_post_meta( $post->ID, '_' . $key, true );
		echo '<tr><th style="width:180px">' . esc_html( $label ) . '</th><td>' . nl2br( esc_html( $value ?: '—' ) ) . '</td></tr>';
	}
	echo '</tbody></table>';
}

function saiyoky_widgets_init(): void {
	register_sidebar(
		array(
			'name'          => __( 'Footer information', 'saiyoky' ),
			'id'            => 'footer-information',
			'before_widget' => '<section class="footer-widget">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="footer-widget__title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'saiyoky_widgets_init' );

function saiyoky_cart_count(): int {
	if ( ! is_admin() && empty( $_COOKIE['woocommerce_items_in_cart'] ) && empty( $_COOKIE['woocommerce_cart_hash'] ) ) {
		return 0;
	}

	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return 0;
	}

	return WC()->cart->get_cart_contents_count();
}

/**
 * Return a shared restaurant detail with a safe default.
 */
function saiyoky_business_detail( string $key ): string {
	$defaults = array(
		'name'        => 'Saiyoky - Sushi & Asian Kitchen Fürstenau',
		'street'      => 'St.-Georg-Straße 2',
		'city'        => '49584 Fürstenau',
		'country'     => 'Deutschland',
		'phone'       => '0590 1825 9476',
		'email'       => 'info@sushi-fuerstenau.de',
		'facebook'    => 'https://www.facebook.com/sushibar.fuerstenau/',
		'domain'      => 'sushi-furstenau.de',
		'hours_week'  => 'Mo–So: 11:00–22:00',
		'hours_sunday'=> '',
	);

	if ( ! isset( $defaults[ $key ] ) ) {
		return '';
	}

	$value = (string) get_theme_mod( 'saiyoky_' . $key, $defaults[ $key ] );

	// Migrate the previous hotline even when it is still stored in the Customizer.
	if ( 'phone' === $key && '01744044576' === preg_replace( '/\D+/', '', $value ) ) {
		return $defaults[ $key ];
	}

	$previous_hours = array(
		'hours_week'   => array( 'Mo–Sa: 11:00–15:00 & 17:00–22:30', 'Mo–Sa: 11:00–22:00' ),
		'hours_sunday' => array( 'So: 11:00–15:00 & 17:00–22:30', 'So: 11:00–22:00' ),
	);
	if ( isset( $previous_hours[ $key ] ) && in_array( $value, $previous_hours[ $key ], true ) ) {
		return $defaults[ $key ];
	}

	return $value;
}

function saiyoky_phone_href(): string {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', saiyoky_business_detail( 'phone' ) );
}

/**
 * Return the current URL in another TranslatePress language.
 */
function saiyoky_current_language(): string {
	global $TRP_LANGUAGE; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

	if ( is_string( $TRP_LANGUAGE ) && '' !== $TRP_LANGUAGE ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		return $TRP_LANGUAGE; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	}

	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	return preg_match( '#^/en(?:/|$)#', $request_uri ) ? 'en_US' : 'de_DE';
}

function saiyoky_language_url( string $language ): string {
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
	$default_uri = preg_replace( '#^/en(?:/|$)#', '/', $request_uri );
	$base_url    = untrailingslashit( (string) get_option( 'home' ) );
	$base_url    = (string) preg_replace( '#/en$#', '', $base_url );

	if ( 'en_US' === $language ) {
		return $base_url . '/en' . $default_uri;
	}

	return $base_url . $default_uri;
}
