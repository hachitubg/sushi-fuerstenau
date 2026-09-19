<?php
/**
 * Landing page footer.
 *
 * @package Saiyoky
 */
?>
<footer class="site-footer">
	<div class="site-footer__inner content-container">
		<div>
			<a class="site-footer__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/brand/logo-text.jpg' ); ?>" width="1288" height="1228" alt="Saiyoky Fine Sushi Street Food" loading="lazy" decoding="async">
			</a>
			<p>Sushi & Asian Kitchen Fürstenau</p>
		</div>
		<div>
			<p class="site-footer__heading">Kontakt</p>
			<address>
				<?php echo esc_html( saiyoky_business_detail( 'street' ) ); ?><br>
				<?php echo esc_html( saiyoky_business_detail( 'city' ) ); ?><br>
				<a href="<?php echo esc_url( saiyoky_phone_href() ); ?>"><?php echo esc_html( saiyoky_business_detail( 'phone' ) ); ?></a>
			</address>
		</div>
		<div class="site-footer__hours">
			<p class="site-footer__heading">Öffnungszeiten</p>
			<p><?php echo esc_html( saiyoky_business_detail( 'hours_week' ) ); ?></p>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
