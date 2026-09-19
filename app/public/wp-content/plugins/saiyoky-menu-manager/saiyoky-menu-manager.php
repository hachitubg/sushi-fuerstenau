<?php
/**
 * Plugin Name: Saiyoky Menu Manager
 * Description: A simplified, localized menu manager for Saiyoky restaurant staff.
 * Version: 0.2.3
 * Author: Saiyoky
 * Text Domain: saiyoky-menu-manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Saiyoky_Menu_Manager {
	private const PAGE_SLUG = 'saiyoky-menu-manager';

	public static function init(): void {
		add_action( 'admin_menu', array( __CLASS__, 'register_page' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'admin_post_saiyoky_save_menu_item', array( __CLASS__, 'save_item' ) );
		add_action( 'admin_post_saiyoky_toggle_menu_item', array( __CLASS__, 'toggle_item' ) );
	}

	public static function register_page(): void {
		add_menu_page(
			self::text( 'menu_manager' ),
			self::text( 'menu_manager' ),
			'edit_products',
			self::PAGE_SLUG,
			array( __CLASS__, 'render_page' ),
			'dashicons-food',
			2
		);
	}

	public static function enqueue_assets( string $hook ): void {
		if ( 'toplevel_page_' . self::PAGE_SLUG !== $hook ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style(
			'saiyoky-menu-manager',
			plugin_dir_url( __FILE__ ) . 'assets/menu-manager.css',
			array(),
			'0.2.3'
		);
		wp_enqueue_script(
			'saiyoky-menu-manager',
			plugin_dir_url( __FILE__ ) . 'assets/menu-manager.js',
			array(),
			'0.2.3',
			true
		);
	}

	public static function render_page(): void {
		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( esc_html__( 'You do not have permission to manage the menu.', 'saiyoky-menu-manager' ) );
		}

		if ( ! class_exists( 'WooCommerce' ) ) {
			echo '<div class="notice notice-error"><p>WooCommerce must be active to use Menu Manager.</p></div>';
			return;
		}

		$view = isset( $_GET['view'] ) ? sanitize_key( wp_unslash( $_GET['view'] ) ) : 'list';
		if ( 'edit' === $view ) {
			self::render_editor();
			return;
		}

		self::render_list();
	}

	private static function render_list(): void {
		$search      = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
		$category_id = isset( $_GET['category'] ) ? absint( $_GET['category'] ) : 0;
		$availability = isset( $_GET['availability'] ) ? sanitize_key( wp_unslash( $_GET['availability'] ) ) : '';
		$page_number = max( 1, isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1 );
		$categories  = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
			)
		);

		$args = array(
			'post_type'      => 'product',
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => 20,
			'paged'          => $page_number,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		);

		if ( '' !== $search ) {
			$title_ids = get_posts(
				array(
					'post_type'      => 'product',
					'post_status'    => array( 'publish', 'draft', 'private' ),
					'posts_per_page' => -1,
					'fields'         => 'ids',
					's'              => $search,
					'no_found_rows'  => true,
				)
			);
			$sku_ids = get_posts(
				array(
					'post_type'      => 'product',
					'post_status'    => array( 'publish', 'draft', 'private' ),
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'meta_query'     => array(
						array(
							'key'     => '_sku',
							'value'   => $search,
							'compare' => 'LIKE',
						),
					),
					'no_found_rows'  => true,
				)
			);
			$matching_ids    = array_values( array_unique( array_merge( $title_ids, $sku_ids ) ) );
			$args['post__in'] = $matching_ids ? $matching_ids : array( 0 );
		}

		if ( $category_id ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => $category_id,
				),
			);
		}

		if ( in_array( $availability, array( 'instock', 'outofstock' ), true ) ) {
			$args['meta_query'] = array(
				array(
					'key'   => '_stock_status',
					'value' => $availability,
				),
			);
		}

		$query       = new WP_Query( $args );
		$total_count = (int) wp_count_posts( 'product' )->publish;
		$available   = self::count_products_by_stock( 'instock' );
		$unavailable = self::count_products_by_stock( 'outofstock' );
		?>
		<div class="wrap saiyoky-manager">
			<header class="smm-header">
				<div>
					<p class="smm-eyebrow">SAIYOKY RESTAURANT</p>
					<h1><?php echo esc_html( self::text( 'menu_manager' ) ); ?></h1>
					<p><?php echo esc_html( self::text( 'intro' ) ); ?></p>
				</div>
				<a class="smm-button smm-button--primary" href="<?php echo esc_url( self::page_url( array( 'view' => 'edit' ) ) ); ?>">
					<span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span> <?php echo esc_html( self::text( 'add_new_dish' ) ); ?>
				</a>
			</header>

			<?php self::render_notice(); ?>

			<section class="smm-stats" aria-label="Menu summary">
				<div class="smm-stat"><span><?php echo esc_html( self::text( 'published_dishes' ) ); ?></span><strong><?php echo esc_html( (string) $total_count ); ?></strong></div>
				<div class="smm-stat smm-stat--success"><span><?php echo esc_html( self::text( 'available_now' ) ); ?></span><strong><?php echo esc_html( (string) $available ); ?></strong></div>
				<div class="smm-stat smm-stat--muted"><span><?php echo esc_html( self::text( 'unavailable' ) ); ?></span><strong><?php echo esc_html( (string) $unavailable ); ?></strong></div>
				<div class="smm-stat"><span><?php echo esc_html( self::text( 'categories' ) ); ?></span><strong><?php echo esc_html( is_wp_error( $categories ) ? '0' : (string) count( $categories ) ); ?></strong></div>
			</section>

			<form class="smm-filters" method="get">
				<input type="hidden" name="page" value="<?php echo esc_attr( self::PAGE_SLUG ); ?>">
				<label>
					<span><?php echo esc_html( self::text( 'search_dishes' ) ); ?></span>
					<input type="search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php echo esc_attr( self::text( 'name_or_number' ) ); ?>">
				</label>
				<label>
					<span><?php echo esc_html( self::text( 'category' ) ); ?></span>
					<select name="category">
						<option value="0"><?php echo esc_html( self::text( 'all_categories' ) ); ?></option>
						<?php if ( ! is_wp_error( $categories ) ) : ?>
							<?php foreach ( $categories as $category ) : ?>
								<option value="<?php echo esc_attr( (string) $category->term_id ); ?>" <?php selected( $category_id, $category->term_id ); ?>><?php echo esc_html( $category->name ); ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</label>
				<label>
					<span><?php echo esc_html( self::text( 'availability' ) ); ?></span>
					<select name="availability">
						<option value=""><?php echo esc_html( self::text( 'all_dishes' ) ); ?></option>
						<option value="instock" <?php selected( $availability, 'instock' ); ?>><?php echo esc_html( self::text( 'available' ) ); ?></option>
						<option value="outofstock" <?php selected( $availability, 'outofstock' ); ?>><?php echo esc_html( self::text( 'unavailable' ) ); ?></option>
					</select>
				</label>
				<button class="smm-button" type="submit"><?php echo esc_html( self::text( 'apply_filters' ) ); ?></button>
				<a class="smm-clear" href="<?php echo esc_url( self::page_url() ); ?>"><?php echo esc_html( self::text( 'clear' ) ); ?></a>
			</form>

			<section class="smm-panel">
				<div class="smm-panel__heading">
					<h2><?php echo esc_html( self::text( 'all_dishes' ) ); ?></h2>
					<span><?php echo esc_html( sprintf( self::text( 'results' ), (int) $query->found_posts ) ); ?></span>
				</div>
				<div class="smm-table-wrap">
					<table class="smm-table">
						<thead><tr><th><?php echo esc_html( self::text( 'dish' ) ); ?></th><th><?php echo esc_html( self::text( 'category' ) ); ?></th><th><?php echo esc_html( self::text( 'price' ) ); ?></th><th><?php echo esc_html( self::text( 'availability' ) ); ?></th><th><span class="screen-reader-text"><?php echo esc_html( self::text( 'actions' ) ); ?></span></th></tr></thead>
						<tbody>
						<?php if ( $query->have_posts() ) : ?>
							<?php while ( $query->have_posts() ) : $query->the_post(); ?>
								<?php self::render_product_row( get_the_ID() ); ?>
							<?php endwhile; ?>
						<?php else : ?>
							<tr><td class="smm-empty" colspan="5"><?php echo esc_html( self::text( 'no_dishes' ) ); ?></td></tr>
						<?php endif; ?>
						</tbody>
					</table>
				</div>
				<?php
				$pagination = paginate_links(
					array(
						'base'      => add_query_arg( 'paged', '%#%' ),
						'format'    => '',
						'current'   => $page_number,
						'total'     => max( 1, (int) $query->max_num_pages ),
						'type'      => 'list',
						'prev_text' => '← Previous',
						'next_text' => 'Next →',
					)
				);
				if ( $pagination ) {
					echo '<nav class="smm-pagination" aria-label="Menu pages">' . wp_kses_post( $pagination ) . '</nav>';
				}
				?>
			</section>
		</div>
		<?php
		wp_reset_postdata();
	}

	private static function render_product_row( int $product_id ): void {
		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return;
		}

		$term_names  = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'names' ) );
		$terms       = is_wp_error( $term_names ) ? '' : implode( ', ', $term_names );
		$is_available = $product->is_in_stock() && 'publish' === get_post_status( $product_id );
		$toggle_to   = $is_available ? 'outofstock' : 'instock';
		$image_id    = $product->get_image_id();
		$english_name = self::get_english_value( $product_id, 'name', $product->get_name() );
		?>
		<tr>
			<td>
				<div class="smm-dish">
					<div class="smm-dish__image">
						<?php if ( $image_id ) : ?>
							<?php echo wp_get_attachment_image( $image_id, 'thumbnail', false, array( 'alt' => '' ) ); ?>
						<?php else : ?>
							<span class="dashicons dashicons-food" aria-hidden="true"></span>
						<?php endif; ?>
					</div>
					<div>
						<strong><?php echo esc_html( $product->get_name() ); ?></strong>
						<?php if ( $english_name && $english_name !== $product->get_name() ) : ?><span class="smm-dish__translation">EN: <?php echo esc_html( $english_name ); ?></span><?php endif; ?>
						<span><?php echo esc_html( $product->get_sku() ? 'Menu no. ' . $product->get_sku() : 'No menu number' ); ?></span>
					</div>
				</div>
			</td>
			<td><?php echo $terms ? esc_html( $terms ) : '<span class="smm-muted">Uncategorised</span>'; ?></td>
			<td><strong><?php echo wp_kses_post( $product->get_price_html() ?: '—' ); ?></strong></td>
			<td><span class="smm-badge <?php echo $is_available ? 'smm-badge--success' : 'smm-badge--muted'; ?>"><?php echo $is_available ? 'Available' : 'Unavailable'; ?></span></td>
			<td>
				<div class="smm-actions">
					<a class="smm-button smm-button--small" href="<?php echo esc_url( self::page_url( array( 'view' => 'edit', 'item' => $product_id ) ) ); ?>">Edit</a>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="saiyoky_toggle_menu_item">
						<input type="hidden" name="item_id" value="<?php echo esc_attr( (string) $product_id ); ?>">
						<input type="hidden" name="stock_status" value="<?php echo esc_attr( $toggle_to ); ?>">
						<?php wp_nonce_field( 'saiyoky_toggle_menu_item_' . $product_id ); ?>
						<button class="smm-link-button" type="submit"><?php echo $is_available ? 'Mark unavailable' : 'Make available'; ?></button>
					</form>
				</div>
			</td>
		</tr>
		<?php
	}

	private static function render_editor(): void {
		$item_id    = isset( $_GET['item'] ) ? absint( $_GET['item'] ) : 0;
		$product    = $item_id ? wc_get_product( $item_id ) : new WC_Product_Simple();
		$categories = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );

		if ( ! $product ) {
			wp_die( esc_html__( 'Dish not found.', 'saiyoky-menu-manager' ) );
		}

		$selected_categories = $item_id ? wp_get_post_terms( $item_id, 'product_cat', array( 'fields' => 'ids' ) ) : array();
		$image_id             = $product->get_image_id();
		$english_name         = $item_id ? self::get_english_value( $item_id, 'name', $product->get_name() ) : '';
		$english_short        = $item_id ? self::get_english_value( $item_id, 'short_description', $product->get_short_description() ) : '';
		$english_description  = $item_id ? self::get_english_value( $item_id, 'description', $product->get_description() ) : '';
		?>
		<div class="wrap saiyoky-manager">
			<header class="smm-header smm-header--editor">
				<div>
					<a class="smm-back" href="<?php echo esc_url( self::page_url() ); ?>">← Back to all dishes</a>
					<h1><?php echo esc_html( $item_id ? self::text( 'edit_dish' ) : self::text( 'add_new_dish' ) ); ?></h1>
					<p><?php echo esc_html( self::text( 'editor_intro' ) ); ?></p>
				</div>
			</header>

			<form class="smm-editor" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="saiyoky_save_menu_item">
				<input type="hidden" name="item_id" value="<?php echo esc_attr( (string) $item_id ); ?>">
				<?php wp_nonce_field( 'saiyoky_save_menu_item' ); ?>

				<div class="smm-editor__main">
					<section class="smm-card">
						<h2><?php echo esc_html( self::text( 'dish_information' ) ); ?></h2>
						<div class="smm-language-tabs" role="tablist" aria-label="Dish language">
							<button class="smm-language-tab is-active" type="button" role="tab" aria-selected="true" aria-controls="smm-language-de" id="smm-tab-de" data-smm-language="de"><span aria-hidden="true">🇩🇪</span> German</button>
							<button class="smm-language-tab" type="button" role="tab" aria-selected="false" aria-controls="smm-language-en" id="smm-tab-en" data-smm-language="en"><span aria-hidden="true">🇬🇧</span> English</button>
						</div>
						<div class="smm-language-panel is-active" id="smm-language-de" role="tabpanel" aria-labelledby="smm-tab-de" data-smm-language-panel="de">
							<p class="smm-language-note"><?php echo esc_html( self::text( 'default_website_language' ) ); ?></p>
							<label class="smm-field smm-field--full"><span><?php echo esc_html( self::text( 'dish_name_german' ) ); ?> <b>*</b></span><input type="text" name="dish_name_de" value="<?php echo esc_attr( $product->get_name() ); ?>" required></label>
							<label class="smm-field smm-field--full"><span><?php echo esc_html( self::text( 'short_description_german' ) ); ?></span><textarea name="dish_short_description_de" rows="4"><?php echo esc_textarea( $product->get_short_description() ); ?></textarea><small><?php echo esc_html( self::text( 'shown_near_price' ) ); ?></small></label>
							<label class="smm-field smm-field--full"><span><?php echo esc_html( self::text( 'full_description_german' ) ); ?></span><textarea name="dish_description_de" rows="7"><?php echo esc_textarea( $product->get_description() ); ?></textarea></label>
						</div>
						<div class="smm-language-panel" id="smm-language-en" role="tabpanel" aria-labelledby="smm-tab-en" data-smm-language-panel="en" hidden>
							<p class="smm-language-note"><?php echo esc_html( self::text( 'english_website_translation' ) ); ?></p>
							<label class="smm-field smm-field--full"><span><?php echo esc_html( self::text( 'dish_name_english' ) ); ?> <b>*</b></span><input type="text" name="dish_name_en" value="<?php echo esc_attr( $english_name ); ?>" required></label>
							<label class="smm-field smm-field--full"><span><?php echo esc_html( self::text( 'short_description_english' ) ); ?></span><textarea name="dish_short_description_en" rows="4"><?php echo esc_textarea( $english_short ); ?></textarea><small><?php echo esc_html( self::text( 'shown_near_price' ) ); ?></small></label>
							<label class="smm-field smm-field--full"><span><?php echo esc_html( self::text( 'full_description_english' ) ); ?></span><textarea name="dish_description_en" rows="7"><?php echo esc_textarea( $english_description ); ?></textarea></label>
						</div>
						<h3 class="smm-shared-heading"><?php echo esc_html( self::text( 'shared_details' ) ); ?></h3>
						<div class="smm-field-grid">
							<label class="smm-field"><span><?php echo esc_html( self::text( 'menu_number_sku' ) ); ?></span><input type="text" name="dish_sku" value="<?php echo esc_attr( $product->get_sku() ); ?>" placeholder="e.g. 101"></label>
							<label class="smm-field"><span>Price (€) <b>*</b></span><input type="number" name="dish_price" value="<?php echo esc_attr( $product->get_regular_price() ); ?>" min="0" step="0.01" required></label>
						</div>
					</section>

					<section class="smm-card">
						<h2><?php echo esc_html( self::text( 'categories' ) ); ?></h2>
						<p class="smm-help"><?php echo esc_html( self::text( 'choose_categories' ) ); ?></p>
						<div class="smm-category-grid">
							<?php if ( ! is_wp_error( $categories ) ) : ?>
								<?php foreach ( $categories as $category ) : ?>
									<label><input type="checkbox" name="dish_categories[]" value="<?php echo esc_attr( (string) $category->term_id ); ?>" <?php checked( in_array( $category->term_id, $selected_categories, true ) ); ?>> <span><?php echo esc_html( $category->name ); ?></span></label>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
						<label class="smm-field smm-field--full"><span><?php echo esc_html( self::text( 'new_category_optional' ) ); ?></span><input type="text" name="new_category" value="" placeholder="<?php echo esc_attr( self::text( 'new_category_placeholder' ) ); ?>"></label>
					</section>
				</div>

				<aside class="smm-editor__side">
					<section class="smm-card">
						<h2><?php echo esc_html( self::text( 'photo' ) ); ?></h2>
						<div class="smm-image-preview" id="smm-image-preview">
							<?php if ( $image_id ) : ?>
								<?php echo wp_get_attachment_image( $image_id, 'medium', false, array( 'alt' => '' ) ); ?>
							<?php else : ?>
								<span class="dashicons dashicons-format-image" aria-hidden="true"></span><p><?php echo esc_html( self::text( 'no_photo_selected' ) ); ?></p>
							<?php endif; ?>
						</div>
						<input type="hidden" id="smm-image-id" name="dish_image_id" value="<?php echo esc_attr( (string) $image_id ); ?>">
						<button class="smm-button smm-button--full" id="smm-select-image" type="button"><?php echo esc_html( self::text( 'choose_photo' ) ); ?></button>
						<button class="smm-link-button smm-remove-image" id="smm-remove-image" type="button" <?php echo $image_id ? '' : 'hidden'; ?>><?php echo esc_html( self::text( 'remove_photo' ) ); ?></button>
					</section>

					<section class="smm-card">
						<h2><?php echo esc_html( self::text( 'menu_status' ) ); ?></h2>
						<label class="smm-switch-row"><span><strong><?php echo esc_html( self::text( 'visible_on_website' ) ); ?></strong><small><?php echo esc_html( self::text( 'customers_can_see' ) ); ?></small></span><input type="checkbox" name="dish_published" value="1" <?php checked( ! $item_id || 'publish' === get_post_status( $item_id ) ); ?>></label>
						<label class="smm-switch-row"><span><strong><?php echo esc_html( self::text( 'available_to_order' ) ); ?></strong><small><?php echo esc_html( self::text( 'turn_off_sold_out' ) ); ?></small></span><input type="checkbox" name="dish_available" value="1" <?php checked( ! $item_id || $product->is_in_stock() ); ?>></label>
					</section>

					<div class="smm-save-box">
						<button class="smm-button smm-button--primary smm-button--full" type="submit"><?php echo esc_html( $item_id ? self::text( 'save_changes' ) : self::text( 'create_dish' ) ); ?></button>
						<a class="smm-button smm-button--full" href="<?php echo esc_url( self::page_url() ); ?>"><?php echo esc_html( self::text( 'cancel' ) ); ?></a>
					</div>
				</aside>
			</form>
		</div>
		<?php
	}

	public static function save_item(): void {
		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( esc_html__( 'You do not have permission to manage the menu.', 'saiyoky-menu-manager' ) );
		}
		check_admin_referer( 'saiyoky_save_menu_item' );

		$item_id = isset( $_POST['item_id'] ) ? absint( $_POST['item_id'] ) : 0;
		$product = $item_id ? wc_get_product( $item_id ) : new WC_Product_Simple();
		if ( ! $product ) {
			self::redirect_with_notice( 'error', 'Dish not found.' );
		}

		$old_sources = array(
			'name'              => $product->get_name(),
			'short_description' => $product->get_short_description(),
			'description'       => $product->get_description(),
		);
		$name  = isset( $_POST['dish_name_de'] ) ? sanitize_text_field( wp_unslash( $_POST['dish_name_de'] ) ) : '';
		$english_values = array(
			'name'              => isset( $_POST['dish_name_en'] ) ? sanitize_text_field( wp_unslash( $_POST['dish_name_en'] ) ) : '',
			'short_description' => isset( $_POST['dish_short_description_en'] ) ? wp_kses_post( wp_unslash( $_POST['dish_short_description_en'] ) ) : '',
			'description'       => isset( $_POST['dish_description_en'] ) ? wp_kses_post( wp_unslash( $_POST['dish_description_en'] ) ) : '',
		);
		$price = isset( $_POST['dish_price'] ) ? wc_format_decimal( wp_unslash( $_POST['dish_price'] ) ) : '';
		if ( '' === $name || '' === $english_values['name'] || '' === $price ) {
			self::redirect_with_notice( 'error', 'German name, English name and price are required.' );
		}

		$new_sources = array(
			'name'              => $name,
			'short_description' => isset( $_POST['dish_short_description_de'] ) ? wp_kses_post( wp_unslash( $_POST['dish_short_description_de'] ) ) : '',
			'description'       => isset( $_POST['dish_description_de'] ) ? wp_kses_post( wp_unslash( $_POST['dish_description_de'] ) ) : '',
		);

		try {
			$product->set_name( $name );
			$product->set_regular_price( $price );
			$product->set_price( $price );
			$product->set_sku( isset( $_POST['dish_sku'] ) ? sanitize_text_field( wp_unslash( $_POST['dish_sku'] ) ) : '' );
			$product->set_short_description( $new_sources['short_description'] );
			$product->set_description( $new_sources['description'] );
			$product->set_image_id( isset( $_POST['dish_image_id'] ) ? absint( $_POST['dish_image_id'] ) : 0 );
			$product->set_status( isset( $_POST['dish_published'] ) ? 'publish' : 'draft' );
			$product->set_stock_status( isset( $_POST['dish_available'] ) ? 'instock' : 'outofstock' );
			$product->set_catalog_visibility( 'visible' );
			$product->set_manage_stock( false );
			$saved_id = $product->save();
		} catch ( Exception $exception ) {
			self::redirect_with_notice( 'error', $exception->getMessage() );
		}

		$category_ids = isset( $_POST['dish_categories'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['dish_categories'] ) ) : array();
		$new_category = isset( $_POST['new_category'] ) ? sanitize_text_field( wp_unslash( $_POST['new_category'] ) ) : '';
		if ( $new_category ) {
			$term = term_exists( $new_category, 'product_cat' );
			if ( ! $term ) {
				$term = wp_insert_term( $new_category, 'product_cat' );
			}
			if ( ! is_wp_error( $term ) ) {
				$category_ids[] = (int) ( is_array( $term ) ? $term['term_id'] : $term );
			}
		}
		wp_set_object_terms( $saved_id, array_values( array_unique( $category_ids ) ), 'product_cat' );
		self::save_english_translations( $saved_id, $old_sources, $new_sources, $english_values );

		self::redirect_with_notice( 'success', $item_id ? 'Dish updated successfully.' : 'New dish created successfully.' );
	}

	public static function toggle_item(): void {
		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( esc_html__( 'You do not have permission to manage the menu.', 'saiyoky-menu-manager' ) );
		}

		$item_id = isset( $_POST['item_id'] ) ? absint( $_POST['item_id'] ) : 0;
		check_admin_referer( 'saiyoky_toggle_menu_item_' . $item_id );
		$product = wc_get_product( $item_id );
		if ( ! $product ) {
			self::redirect_with_notice( 'error', 'Dish not found.' );
		}

		$stock_status = isset( $_POST['stock_status'] ) && 'instock' === sanitize_key( wp_unslash( $_POST['stock_status'] ) ) ? 'instock' : 'outofstock';
		$product->set_stock_status( $stock_status );
		if ( 'instock' === $stock_status && 'publish' !== $product->get_status() ) {
			$product->set_status( 'publish' );
		}
		$product->save();
		self::redirect_with_notice( 'success', 'Availability updated.' );
	}

	private static function get_english_value( int $product_id, string $field, string $source ): string {
		$meta_key = '_smm_en_' . $field;
		$stored   = get_post_meta( $product_id, $meta_key, true );
		if ( is_string( $stored ) && '' !== $stored ) {
			return $stored;
		}

		$source = self::translation_text( $source );
		if ( '' === $source ) {
			return '';
		}

		global $wpdb;
		$dictionary_table = $wpdb->prefix . 'trp_dictionary_de_de_en_us';
		$original_table   = $wpdb->prefix . 'trp_original_strings';
		$meta_table       = $wpdb->prefix . 'trp_original_meta';
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $dictionary_table ) ) !== $dictionary_table ) {
			return '';
		}

		$translated = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT d.translated
				FROM `{$original_table}` s
				INNER JOIN `{$meta_table}` m ON m.original_id = s.id
				INNER JOIN `{$dictionary_table}` d ON d.original_id = s.id
				WHERE m.meta_key = 'post_parent_id' AND m.meta_value = %s AND s.original = %s
				ORDER BY d.status DESC, d.id DESC LIMIT 1",
				(string) $product_id,
				$source
			)
		);

		if ( ! is_string( $translated ) || '' === $translated ) {
			$translated = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT translated FROM `{$dictionary_table}` WHERE original = %s AND translated <> '' ORDER BY status DESC, id DESC LIMIT 1",
					$source
				)
			);
		}

		return is_string( $translated ) ? $translated : '';
	}

	private static function save_english_translations( int $product_id, array $old_sources, array $new_sources, array $english_values ): void {
		if ( self::translation_text( $new_sources['short_description'] ) === self::translation_text( $new_sources['description'] ) && '' !== self::translation_text( $new_sources['short_description'] ) ) {
			$english_values['description'] = $english_values['short_description'];
		}

		foreach ( $english_values as $field => $english_value ) {
			update_post_meta( $product_id, '_smm_en_' . $field, $english_value );
		}

		global $wpdb;
		$dictionary_table = $wpdb->prefix . 'trp_dictionary_de_de_en_us';
		$original_table   = $wpdb->prefix . 'trp_original_strings';
		$meta_table       = $wpdb->prefix . 'trp_original_meta';
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $dictionary_table ) ) !== $dictionary_table ) {
			return;
		}

		foreach ( array( 'name', 'short_description', 'description' ) as $field ) {
			$old_source = self::translation_text( $old_sources[ $field ] ?? '' );
			$new_source = self::translation_text( $new_sources[ $field ] ?? '' );
			$translated = self::translation_text( $english_values[ $field ] ?? '' );
			if ( '' === $new_source ) {
				continue;
			}

			$original_ids = array();
			if ( '' !== $old_source ) {
				$original_ids = $wpdb->get_col(
					$wpdb->prepare(
						"SELECT s.id FROM `{$original_table}` s INNER JOIN `{$meta_table}` m ON m.original_id = s.id WHERE m.meta_key = 'post_parent_id' AND m.meta_value = %s AND s.original = %s",
						(string) $product_id,
						$old_source
					)
				);
			}

			if ( ! $original_ids ) {
				$original_ids = $wpdb->get_col(
					$wpdb->prepare(
						"SELECT s.id FROM `{$original_table}` s INNER JOIN `{$meta_table}` m ON m.original_id = s.id WHERE m.meta_key = 'post_parent_id' AND m.meta_value = %s AND s.original = %s",
						(string) $product_id,
						$new_source
					)
				);
			}

			if ( ! $original_ids ) {
				$wpdb->insert( $original_table, array( 'original' => $new_source ), array( '%s' ) );
				$original_id = (int) $wpdb->insert_id;
				if ( $original_id ) {
					$wpdb->insert(
						$meta_table,
						array( 'original_id' => $original_id, 'meta_key' => 'post_parent_id', 'meta_value' => (string) $product_id ),
						array( '%d', '%s', '%s' )
					);
					$original_ids = array( $original_id );
				}
			}

			foreach ( array_map( 'absint', $original_ids ) as $original_id ) {
				$wpdb->update( $original_table, array( 'original' => $new_source ), array( 'id' => $original_id ), array( '%s' ), array( '%d' ) );
				$dictionary_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM `{$dictionary_table}` WHERE original_id = %d ORDER BY id DESC LIMIT 1", $original_id ) );
				$data = array(
					'original'    => $new_source,
					'translated'  => $translated,
					'status'      => '' === $translated ? 0 : 2,
					'block_type'  => 0,
					'original_id' => $original_id,
				);
				if ( $dictionary_id ) {
					$wpdb->update( $dictionary_table, $data, array( 'id' => (int) $dictionary_id ), array( '%s', '%s', '%d', '%d', '%d' ), array( '%d' ) );
				} else {
					$wpdb->insert( $dictionary_table, $data, array( '%s', '%s', '%d', '%d', '%d' ) );
				}
			}
		}
	}

	private static function translation_text( string $value ): string {
		return trim( html_entity_decode( wp_strip_all_tags( $value ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
	}

	private static function current_admin_language(): string {
		$locale = strtolower( get_user_locale( get_current_user_id() ) );

		if ( 0 === strpos( $locale, 'vi' ) ) {
			return 'vi';
		}

		if ( 0 === strpos( $locale, 'de' ) ) {
			return 'de';
		}

		return 'en';
	}

	private static function text( string $key ): string {
		$strings = array(
			'en' => array(
				'menu_manager' => 'Menu Manager',
				'intro' => 'Update dishes, prices, photos and availability without opening WooCommerce.',
				'add_new_dish' => 'Add new dish',
				'published_dishes' => 'Published dishes',
				'available_now' => 'Available now',
				'unavailable' => 'Unavailable',
				'categories' => 'Categories',
				'search_dishes' => 'Search dishes',
				'name_or_number' => 'Name or menu number',
				'category' => 'Category',
				'all_categories' => 'All categories',
				'availability' => 'Availability',
				'all_dishes' => 'All dishes',
				'available' => 'Available',
				'apply_filters' => 'Apply filters',
				'clear' => 'Clear',
				'results' => '%d results',
				'dish' => 'Dish',
				'price' => 'Price',
				'actions' => 'Actions',
				'no_dishes' => 'No dishes match these filters.',
				'edit_dish' => 'Edit dish',
				'editor_intro' => 'Only the fields needed for the restaurant menu are shown here.',
				'dish_information' => 'Dish information',
				'default_website_language' => 'Default website language',
				'dish_name_german' => 'Dish name (German)',
				'short_description_german' => 'Short description (German)',
				'full_description_german' => 'Full description (German)',
				'english_website_translation' => 'English website translation',
				'dish_name_english' => 'Dish name (English)',
				'short_description_english' => 'Short description (English)',
				'full_description_english' => 'Full description (English)',
				'shown_near_price' => 'Shown near the dish name and price.',
				'shared_details' => 'Shared details',
				'menu_number_sku' => 'Menu number / SKU',
				'choose_categories' => 'Choose one or more sections of the restaurant menu.',
				'new_category_optional' => 'New category (optional)',
				'new_category_placeholder' => 'Create and assign a new menu category',
				'photo' => 'Photo',
				'no_photo_selected' => 'No photo selected',
				'choose_photo' => 'Choose photo',
				'remove_photo' => 'Remove photo',
				'menu_status' => 'Menu status',
				'visible_on_website' => 'Visible on website',
				'customers_can_see' => 'Customers can see this dish.',
				'available_to_order' => 'Available to order',
				'turn_off_sold_out' => 'Turn off when temporarily sold out.',
				'save_changes' => 'Save changes',
				'create_dish' => 'Create dish',
				'cancel' => 'Cancel',
			),
			'vi' => array(
				'menu_manager' => 'Quản lý menu',
				'intro' => 'Cập nhật món ăn, giá, hình ảnh và trạng thái mà không cần mở WooCommerce.',
				'add_new_dish' => 'Thêm món mới',
				'published_dishes' => 'Món đang hiển thị',
				'available_now' => 'Đang bán',
				'unavailable' => 'Tạm hết',
				'categories' => 'Danh mục',
				'search_dishes' => 'Tìm món',
				'name_or_number' => 'Tên món hoặc số menu',
				'category' => 'Danh mục',
				'all_categories' => 'Tất cả danh mục',
				'availability' => 'Trạng thái',
				'all_dishes' => 'Tất cả món',
				'available' => 'Đang bán',
				'apply_filters' => 'Lọc',
				'clear' => 'Xóa lọc',
				'results' => '%d kết quả',
				'dish' => 'Món ăn',
				'price' => 'Giá',
				'actions' => 'Thao tác',
				'no_dishes' => 'Không có món nào khớp bộ lọc.',
				'edit_dish' => 'Sửa món',
				'editor_intro' => 'Chỉ hiển thị những thông tin cần thiết để quản lý menu nhà hàng.',
				'dish_information' => 'Thông tin món ăn',
				'default_website_language' => 'Ngôn ngữ mặc định của website',
				'dish_name_german' => 'Tên món (Tiếng Đức)',
				'short_description_german' => 'Mô tả ngắn (Tiếng Đức)',
				'full_description_german' => 'Mô tả đầy đủ (Tiếng Đức)',
				'english_website_translation' => 'Bản dịch Tiếng Anh trên website',
				'dish_name_english' => 'Tên món (Tiếng Anh)',
				'short_description_english' => 'Mô tả ngắn (Tiếng Anh)',
				'full_description_english' => 'Mô tả đầy đủ (Tiếng Anh)',
				'shown_near_price' => 'Hiển thị gần tên món và giá.',
				'shared_details' => 'Thông tin dùng chung',
				'menu_number_sku' => 'Số menu / SKU',
				'choose_categories' => 'Chọn một hoặc nhiều nhóm món trong menu nhà hàng.',
				'new_category_optional' => 'Danh mục mới (không bắt buộc)',
				'new_category_placeholder' => 'Tạo và gán danh mục menu mới',
				'photo' => 'Hình ảnh',
				'no_photo_selected' => 'Chưa chọn hình ảnh',
				'choose_photo' => 'Chọn hình ảnh',
				'remove_photo' => 'Xóa hình ảnh',
				'menu_status' => 'Trạng thái menu',
				'visible_on_website' => 'Hiển thị trên website',
				'customers_can_see' => 'Khách hàng có thể nhìn thấy món này.',
				'available_to_order' => 'Cho phép đặt món',
				'turn_off_sold_out' => 'Tắt khi món tạm hết.',
				'save_changes' => 'Lưu thay đổi',
				'create_dish' => 'Tạo món',
				'cancel' => 'Hủy',
			),
			'de' => array(
				'menu_manager' => 'Menü verwalten',
				'intro' => 'Gerichte, Preise, Fotos und Verfügbarkeit ohne WooCommerce ändern.',
				'add_new_dish' => 'Neues Gericht',
				'published_dishes' => 'Veröffentlichte Gerichte',
				'available_now' => 'Verfügbar',
				'unavailable' => 'Nicht verfügbar',
				'categories' => 'Kategorien',
				'search_dishes' => 'Gerichte suchen',
				'name_or_number' => 'Name oder Menünummer',
				'category' => 'Kategorie',
				'all_categories' => 'Alle Kategorien',
				'availability' => 'Verfügbarkeit',
				'all_dishes' => 'Alle Gerichte',
				'available' => 'Verfügbar',
				'apply_filters' => 'Filtern',
				'clear' => 'Zurücksetzen',
				'results' => '%d Ergebnisse',
				'dish' => 'Gericht',
				'price' => 'Preis',
				'actions' => 'Aktionen',
				'no_dishes' => 'Keine Gerichte passen zu diesen Filtern.',
				'edit_dish' => 'Gericht bearbeiten',
				'editor_intro' => 'Hier werden nur die wichtigsten Felder für das Restaurantmenü angezeigt.',
				'dish_information' => 'Gerichtsinformationen',
				'default_website_language' => 'Standardsprache der Website',
				'dish_name_german' => 'Gerichtsname (Deutsch)',
				'short_description_german' => 'Kurzbeschreibung (Deutsch)',
				'full_description_german' => 'Beschreibung (Deutsch)',
				'english_website_translation' => 'Englische Website-Übersetzung',
				'dish_name_english' => 'Gerichtsname (Englisch)',
				'short_description_english' => 'Kurzbeschreibung (Englisch)',
				'full_description_english' => 'Beschreibung (Englisch)',
				'shown_near_price' => 'Wird neben Name und Preis angezeigt.',
				'shared_details' => 'Gemeinsame Details',
				'menu_number_sku' => 'Menünummer / SKU',
				'choose_categories' => 'Wähle einen oder mehrere Bereiche des Restaurantmenüs.',
				'new_category_optional' => 'Neue Kategorie (optional)',
				'new_category_placeholder' => 'Neue Menükategorie erstellen und zuweisen',
				'photo' => 'Foto',
				'no_photo_selected' => 'Kein Foto ausgewählt',
				'choose_photo' => 'Foto auswählen',
				'remove_photo' => 'Foto entfernen',
				'menu_status' => 'Menüstatus',
				'visible_on_website' => 'Auf Website sichtbar',
				'customers_can_see' => 'Kunden können dieses Gericht sehen.',
				'available_to_order' => 'Bestellbar',
				'turn_off_sold_out' => 'Ausschalten, wenn vorübergehend ausverkauft.',
				'save_changes' => 'Änderungen speichern',
				'create_dish' => 'Gericht erstellen',
				'cancel' => 'Abbrechen',
			),
		);

		$language = self::current_admin_language();
		return $strings[ $language ][ $key ] ?? $strings['en'][ $key ] ?? $key;
	}

	private static function count_products_by_stock( string $stock_status ): int {
		$query = new WP_Query(
			array(
				'post_type'      => 'product',
				'post_status'    => array( 'publish', 'draft', 'private' ),
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_query'     => array(
					array(
						'key'   => '_stock_status',
						'value' => $stock_status,
					),
				),
			)
		);
		return (int) $query->found_posts;
	}

	private static function render_notice(): void {
		$type    = isset( $_GET['smm_notice'] ) ? sanitize_key( wp_unslash( $_GET['smm_notice'] ) ) : '';
		$message = isset( $_GET['smm_message'] ) ? sanitize_text_field( wp_unslash( $_GET['smm_message'] ) ) : '';
		if ( ! $type || ! $message ) {
			return;
		}
		echo '<div class="smm-notice smm-notice--' . esc_attr( 'error' === $type ? 'error' : 'success' ) . '"><span class="dashicons ' . esc_attr( 'error' === $type ? 'dashicons-warning' : 'dashicons-yes-alt' ) . '"></span><p>' . esc_html( $message ) . '</p></div>';
	}

	private static function redirect_with_notice( string $type, string $message ): void {
		wp_safe_redirect(
			self::page_url(
				array(
					'smm_notice'  => $type,
					'smm_message' => $message,
				)
			)
		);
		exit;
	}

	private static function page_url( array $args = array() ): string {
		return add_query_arg( array_merge( array( 'page' => self::PAGE_SLUG ), $args ), admin_url( 'admin.php' ) );
	}
}

Saiyoky_Menu_Manager::init();
