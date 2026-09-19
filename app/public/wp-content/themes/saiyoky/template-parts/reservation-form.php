<?php
/**
 * Shared reservation form.
 *
 * @package Saiyoky
 */

$page_language    = function_exists( 'saiyoky_current_language' ) ? saiyoky_current_language() : get_locale();
$is_english       = str_starts_with( strtolower( (string) $page_language ), 'en' );
$booking_language = (string) $page_language;
$status           = isset( $_GET['reservation'] ) ? sanitize_key( wp_unslash( $_GET['reservation'] ) ) : '';
$field_id         = wp_unique_id( 'saiyoky-reservation-' );
?>
<?php if ( 'success' === $status ) : ?>
	<div class="form-status form-status--success" role="status"><?php echo esc_html( $is_english ? 'Thank you! We have received your request. Your reservation is confirmed only after our reply.' : 'Vielen Dank! Ihre Anfrage wurde gespeichert. Die Reservierung ist erst nach unserer Bestätigung verbindlich.' ); ?></div>
<?php endif; ?>
<?php if ( in_array( $status, array( 'invalid', 'error' ), true ) ) : ?>
	<div class="form-status form-status--error" role="alert"><?php echo esc_html( $is_english ? 'Please check your details or call us directly.' : 'Bitte prüfen Sie Ihre Angaben oder rufen Sie uns direkt an.' ); ?></div>
<?php endif; ?>
<form class="reservation-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
	<input type="hidden" name="action" value="saiyoky_reservation">
	<input type="hidden" name="booking_language" value="<?php echo esc_attr( $booking_language ); ?>">
	<?php wp_nonce_field( 'saiyoky_reservation', 'saiyoky_reservation_nonce' ); ?>
	<div class="form-honeypot" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
	<div class="form-field form-field--wide"><label for="<?php echo esc_attr( $field_id ); ?>-name"><?php echo esc_html( $is_english ? 'Name' : 'Name' ); ?> *</label><input id="<?php echo esc_attr( $field_id ); ?>-name" name="guest_name" type="text" required autocomplete="name"></div>
	<div class="form-field"><label for="<?php echo esc_attr( $field_id ); ?>-phone"><?php echo esc_html( $is_english ? 'Phone' : 'Telefon' ); ?> *</label><input id="<?php echo esc_attr( $field_id ); ?>-phone" name="guest_phone" type="tel" required autocomplete="tel"></div>
	<div class="form-field"><label for="<?php echo esc_attr( $field_id ); ?>-email"><?php echo esc_html( $is_english ? 'Email' : 'E-Mail' ); ?></label><input id="<?php echo esc_attr( $field_id ); ?>-email" name="guest_email" type="email" autocomplete="email"></div>
	<div class="form-field"><label for="<?php echo esc_attr( $field_id ); ?>-date"><?php echo esc_html( $is_english ? 'Date' : 'Datum' ); ?> *</label><input id="<?php echo esc_attr( $field_id ); ?>-date" name="booking_date" type="date" min="<?php echo esc_attr( wp_date( 'Y-m-d' ) ); ?>" required></div>
	<div class="form-field"><label for="<?php echo esc_attr( $field_id ); ?>-time"><?php echo esc_html( $is_english ? 'Time' : 'Uhrzeit' ); ?> *</label><input id="<?php echo esc_attr( $field_id ); ?>-time" name="booking_time" type="time" min="11:00" max="22:00" step="60" required></div>
	<div class="form-field form-field--wide"><label for="<?php echo esc_attr( $field_id ); ?>-guests"><?php echo esc_html( $is_english ? 'Guests' : 'Personen' ); ?> *</label><select id="<?php echo esc_attr( $field_id ); ?>-guests" name="guest_count" required><option value=""><?php echo esc_html( $is_english ? 'Please select' : 'Bitte wählen' ); ?></option><?php for ( $i = 1; $i <= 20; $i++ ) : ?><option value="<?php echo esc_attr( (string) $i ); ?>"><?php echo esc_html( (string) $i ); ?></option><?php endfor; ?></select></div>
	<div class="form-field form-field--wide"><label for="<?php echo esc_attr( $field_id ); ?>-message"><?php echo esc_html( $is_english ? 'Message or special requests' : 'Nachricht oder besondere Wünsche' ); ?></label><textarea id="<?php echo esc_attr( $field_id ); ?>-message" name="guest_message" rows="4"></textarea></div>
	<label class="form-consent form-field--wide"><input type="checkbox" required> <span><?php echo esc_html( $is_english ? 'I agree that my details may be processed to handle this reservation request.' : 'Ich stimme der Verarbeitung meiner Angaben zur Bearbeitung der Reservierungsanfrage zu.' ); ?></span></label>
	<div class="form-field--wide"><button class="button" type="submit"><?php echo esc_html( $is_english ? 'Send request' : 'Anfrage senden' ); ?></button></div>
</form>
