<?php
/**
 * Saiyoky customer login and registration.
 *
 * @package Saiyoky
 * @version 9.9.0
 */

defined( 'ABSPATH' ) || exit;

$registration_enabled = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
$register_active      = isset( $_POST['register'] ) || ( isset( $_GET['register'] ) && '1' === $_GET['register'] );

do_action( 'woocommerce_before_customer_login_form' );
?>
<section class="account-auth" data-auth-tabs>
	<div class="account-auth__visual">
		<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/products/salmon-nigiri.jpeg' ); ?>" alt="<?php esc_attr_e( 'Sushi bei Saiyoky', 'saiyoky' ); ?>">
		<div class="account-auth__visual-copy">
			<p class="eyebrow">Saiyoky</p>
			<h2><?php esc_html_e( 'Ihr Genuss. Ihr Konto.', 'saiyoky' ); ?></h2>
			<p><?php esc_html_e( 'Bestellungen schneller abschließen, Adressen speichern und Ihre Bestellhistorie jederzeit einsehen.', 'saiyoky' ); ?></p>
			<div class="auth-benefits"><span>✓ <?php esc_html_e( 'Schneller bestellen', 'saiyoky' ); ?></span><span>✓ <?php esc_html_e( 'Bestellungen verfolgen', 'saiyoky' ); ?></span><span>✓ <?php esc_html_e( 'Adressen verwalten', 'saiyoky' ); ?></span></div>
		</div>
	</div>
	<div class="account-auth__forms">
		<div class="auth-tabs" role="tablist">
			<button type="button" class="<?php echo $register_active ? '' : 'is-active'; ?>" data-auth-target="login" role="tab"><?php esc_html_e( 'Anmelden', 'saiyoky' ); ?></button>
			<?php if ( $registration_enabled ) : ?><button type="button" class="<?php echo $register_active ? 'is-active' : ''; ?>" data-auth-target="register" role="tab"><?php esc_html_e( 'Registrieren', 'saiyoky' ); ?></button><?php endif; ?>
		</div>

		<div class="auth-panel" data-auth-panel="login" <?php echo $register_active ? 'hidden' : ''; ?>>
			<p class="eyebrow"><?php esc_html_e( 'Willkommen zurück', 'saiyoky' ); ?></p>
			<h2><?php esc_html_e( 'Bei Saiyoky anmelden', 'saiyoky' ); ?></h2>
			<p class="auth-panel__intro"><?php esc_html_e( 'Melden Sie sich an, um Ihre Bestellungen und Kontodaten zu verwalten.', 'saiyoky' ); ?></p>
			<form class="woocommerce-form woocommerce-form-login login" method="post" novalidate>
				<?php do_action( 'woocommerce_login_form_start' ); ?>
				<p class="woocommerce-form-row form-row form-row-wide"><label for="username"><?php esc_html_e( 'E-Mail oder Benutzername', 'saiyoky' ); ?> <span class="required">*</span></label><input type="text" class="woocommerce-Input input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required></p>
				<p class="woocommerce-form-row form-row form-row-wide"><label for="password"><?php esc_html_e( 'Passwort', 'saiyoky' ); ?> <span class="required">*</span></label><input class="woocommerce-Input input-text" type="password" name="password" id="password" autocomplete="current-password" required></p>
				<?php do_action( 'woocommerce_login_form' ); ?>
				<div class="auth-form-actions"><label class="woocommerce-form-login__rememberme"><input name="rememberme" type="checkbox" value="forever"> <span><?php esc_html_e( 'Angemeldet bleiben', 'saiyoky' ); ?></span></label><a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Passwort vergessen?', 'saiyoky' ); ?></a></div>
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<button type="submit" class="woocommerce-button button auth-submit" name="login" value="<?php esc_attr_e( 'Anmelden', 'saiyoky' ); ?>"><?php esc_html_e( 'Anmelden', 'saiyoky' ); ?> <span>→</span></button>
				<?php do_action( 'woocommerce_login_form_end' ); ?>
			</form>
		</div>

		<?php if ( $registration_enabled ) : ?>
		<div class="auth-panel" data-auth-panel="register" <?php echo $register_active ? '' : 'hidden'; ?>>
			<p class="eyebrow"><?php esc_html_e( 'Neu bei Saiyoky', 'saiyoky' ); ?></p>
			<h2><?php esc_html_e( 'Konto erstellen', 'saiyoky' ); ?></h2>
			<p class="auth-panel__intro"><?php esc_html_e( 'Erstellen Sie kostenlos Ihr Kundenkonto für eine schnellere Bestellung.', 'saiyoky' ); ?></p>
			<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?>>
				<?php do_action( 'woocommerce_register_form_start' ); ?>
				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?><p class="woocommerce-form-row form-row form-row-wide"><label for="reg_username"><?php esc_html_e( 'Benutzername', 'saiyoky' ); ?> <span class="required">*</span></label><input type="text" class="woocommerce-Input input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ! empty( $_POST['username'] ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required></p><?php endif; ?>
				<p class="woocommerce-form-row form-row form-row-wide"><label for="reg_email"><?php esc_html_e( 'E-Mail', 'saiyoky' ); ?> <span class="required">*</span></label><input type="email" class="woocommerce-Input input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ! empty( $_POST['email'] ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required></p>
				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?><p class="woocommerce-form-row form-row form-row-wide"><label for="reg_password"><?php esc_html_e( 'Passwort', 'saiyoky' ); ?> <span class="required">*</span></label><input type="password" class="woocommerce-Input input-text" name="password" id="reg_password" autocomplete="new-password" required></p><?php else : ?><p><?php esc_html_e( 'Sie erhalten per E-Mail einen Link zum Festlegen Ihres Passworts.', 'saiyoky' ); ?></p><?php endif; ?>
				<?php do_action( 'woocommerce_register_form' ); ?>
				<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
				<button type="submit" class="woocommerce-Button button auth-submit" name="register" value="<?php esc_attr_e( 'Registrieren', 'saiyoky' ); ?>"><?php esc_html_e( 'Kostenlos registrieren', 'saiyoky' ); ?> <span>→</span></button>
				<?php do_action( 'woocommerce_register_form_end' ); ?>
			</form>
		</div>
		<?php endif; ?>
	</div>
</section>
<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
