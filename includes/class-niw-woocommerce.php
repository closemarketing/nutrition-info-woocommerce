<?php
/**
 * NIW WooCommerce Integration
 *
 * Adds allergens, nutritional info and ingredients tab to WooCommerce product editor.
 * Self-contained — no dependency on manage-menus.
 *
 * @package nutrition-info-woocommerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce product editor integration for nutrition and allergen data.
 */
class NIW_WooCommerce {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'render_panel' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_fields' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_styles' ) );
	}

	/**
	 * Enqueue admin scripts and styles on product edit screens.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_scripts( string $hook ) {
		if ( ( 'post.php' !== $hook && 'post-new.php' !== $hook ) || 'product' !== get_post_type() ) {
			return;
		}
		wp_enqueue_style( 'niw-admin', NIW_PLUGIN_URL . 'assets/css/admin.css', array(), NIW_BUNDLE_VERSION );
		wp_enqueue_script( 'niw-admin-ingredients', NIW_PLUGIN_URL . 'assets/js/admin-ingredients.js', array( 'jquery' ), NIW_BUNDLE_VERSION, true );
	}

	/**
	 * Enqueue public-facing styles.
	 */
	public function enqueue_public_styles() {
		wp_enqueue_style( 'niw-public', NIW_PLUGIN_URL . 'assets/css/blocks.css', array(), NIW_BUNDLE_VERSION );
	}

	/**
	 * Add nutrition tab to WooCommerce product data tabs.
	 *
	 * @param array $tabs Existing product data tabs.
	 * @return array
	 */
	public function add_tab( $tabs ) {
		$tabs['niw_nutrition'] = array(
			'label'    => __( 'Nutrition & Allergens', 'nutrition-info-woocommerce' ),
			'target'   => 'niw_nutrition_data',
			'class'    => array(),
			'priority' => 85,
		);
		return $tabs;
	}

	/**
	 * Render the nutrition data panel in the product editor.
	 */
	public function render_panel() {
		global $post;
		$post_id      = $post->ID;
		$allergens    = NIW_Data::get_allergens();
		$nutrients    = NIW_Data::get_nutrients();
		$group_labels = NIW_Data::get_group_labels();
		$icons_url    = NIW_PLUGIN_URL . 'assets/icons/';
		$ingredients  = get_post_meta( $post_id, 'food_ingredients', true );
		if ( ! is_array( $ingredients ) ) {
			$ingredients = array();
		}
		$serving = get_post_meta( $post_id, 'food_nutrient_serving', true );

		wp_nonce_field( 'niw_nutrition_metabox', 'niw_nutrition_nonce' );
		?>
		<div id="niw_nutrition_data" class="panel woocommerce_options_panel">

			<?php /* ---- ALLERGENS ---- */ ?>
			<div class="options_group">
				<p class="form-field"><label><strong><?php esc_html_e( 'Allergens', 'nutrition-info-woocommerce' ); ?></strong></label></p>
				<div class="niw-allergens-admin-wrapper">
					<?php
					foreach ( $allergens as $key => $label ) :
						$checked = get_post_meta( $post_id, 'food_allegerns_' . $key, true );
						?>
					<div class="niw-allergen-item">
						<input type="checkbox"
							name="food_allegerns_<?php echo esc_attr( $key ); ?>"
							id="niw_allergen_<?php echo esc_attr( $key ); ?>"
							value="1"
							<?php checked( $checked, 1 ); ?>>
						<label for="niw_allergen_<?php echo esc_attr( $key ); ?>">
							<img src="<?php echo esc_url( $icons_url . 'icon-' . $key . '.png' ); ?>"
								alt="<?php echo esc_attr( $label ); ?>"
								style="width:24px;height:24px;vertical-align:middle;margin-right:4px;">
							<?php echo esc_html( $label ); ?>
						</label>
					</div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php /* ---- NUTRITIONAL INFO ---- */ ?>
			<div class="options_group">
				<p class="form-field"><label><strong><?php esc_html_e( 'Nutritional Info', 'nutrition-info-woocommerce' ); ?></strong></label></p>
				<div class="form-field">
					<label for="food_nutrient_serving"><?php esc_html_e( 'Serving size', 'nutrition-info-woocommerce' ); ?></label>
					<input type="text" id="food_nutrient_serving" name="food_nutrient_serving"
						value="<?php echo esc_attr( $serving ); ?>"
						placeholder="<?php esc_attr_e( 'e.g. 100g, 1 serving (200g)', 'nutrition-info-woocommerce' ); ?>">
				</div>
				<table class="niw-nutrients-admin-table" style="width:100%;border-collapse:collapse;margin:0 12px;">
					<thead>
						<tr>
							<th style="text-align:left;padding:6px 8px;border-bottom:2px solid #ddd;"><?php esc_html_e( 'Nutrient', 'nutrition-info-woocommerce' ); ?></th>
							<th style="text-align:left;padding:6px 8px;border-bottom:2px solid #ddd;width:160px;"><?php esc_html_e( 'Amount', 'nutrition-info-woocommerce' ); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php
					$prev_group = '';
					foreach ( $nutrients as $key => $def ) :
						$group = $def['group'];
						if ( $group !== $prev_group ) :
							echo '<tr style="background:#f0f0f0;"><td colspan="2" style="padding:5px 8px;font-weight:600;font-size:11px;text-transform:uppercase;letter-spacing:.05em;">' . esc_html( $group_labels[ $group ] ) . '</td></tr>';
							$prev_group = $group;
						endif;
						$value  = get_post_meta( $post_id, 'food_nutrient_' . $key, true );
						$is_sub = ! empty( $def['sub'] );
						$label  = $is_sub ? '— ' . $def['label'] : $def['label'];
						?>
					<tr>
						<td style="padding:<?php echo $is_sub ? '4px 8px 4px 24px' : '4px 8px'; ?>;border-bottom:1px solid #eee;">
							<label for="food_nutrient_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
						</td>
						<td style="padding:4px 8px;border-bottom:1px solid #eee;">
							<input type="number" step="any" min="0" style="width:90px;"
								id="food_nutrient_<?php echo esc_attr( $key ); ?>"
								name="food_nutrient_<?php echo esc_attr( $key ); ?>"
								value="<?php echo esc_attr( $value ); ?>">
							<span style="color:#888;font-size:12px;"><?php echo esc_html( $def['unit'] ); ?></span>
						</td>
					</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<?php /* ---- INGREDIENTS ---- */ ?>
			<div class="options_group">
				<p class="form-field"><label><strong><?php esc_html_e( 'Ingredients', 'nutrition-info-woocommerce' ); ?></strong></label></p>
				<div class="form-field" style="padding:0 12px 12px;">
					<table id="niw-ingredients-table" style="width:100%;border-collapse:collapse;margin-bottom:10px;">
						<thead>
							<tr>
								<th style="text-align:left;padding:6px 8px;border-bottom:2px solid #ddd;width:55%;"><?php esc_html_e( 'Ingredient', 'nutrition-info-woocommerce' ); ?></th>
								<th style="text-align:left;padding:6px 8px;border-bottom:2px solid #ddd;width:35%;"><?php esc_html_e( 'Quantity', 'nutrition-info-woocommerce' ); ?></th>
								<th style="width:10%;border-bottom:2px solid #ddd;"></th>
							</tr>
						</thead>
						<tbody id="niw-ingredients-body">
							<?php foreach ( $ingredients as $i => $ingredient ) : ?>
							<tr class="niw-ingredient-row">
								<td style="padding:4px 8px;">
									<input type="text" name="food_ingredients[<?php echo esc_attr( $i ); ?>][name]"
										value="<?php echo esc_attr( $ingredient['name'] ?? '' ); ?>"
										placeholder="<?php esc_attr_e( 'e.g. Olive oil', 'nutrition-info-woocommerce' ); ?>"
										style="width:100%;">
								</td>
								<td style="padding:4px 8px;">
									<input type="text" name="food_ingredients[<?php echo esc_attr( $i ); ?>][quantity]"
										value="<?php echo esc_attr( $ingredient['quantity'] ?? '' ); ?>"
										placeholder="<?php esc_attr_e( 'e.g. 50g', 'nutrition-info-woocommerce' ); ?>"
										style="width:100%;">
								</td>
								<td style="padding:4px 8px;text-align:center;">
									<button type="button" class="niw-remove-ingredient button">✕</button>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<button type="button" id="niw-add-ingredient" class="button">
						+ <?php esc_html_e( 'Add ingredient', 'nutrition-info-woocommerce' ); ?>
					</button>
				</div>
			</div>

		</div>
		<?php
	}

	/**
	 * Save nutrition, allergen and ingredient fields from the product editor.
	 *
	 * @param int $post_id Product post ID.
	 */
	public function save_fields( $post_id ) {
		if ( ! isset( $_POST['niw_nutrition_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['niw_nutrition_nonce'] ) ), 'niw_nutrition_metabox' ) ) {
			return;
		}

		// Allergens.
		foreach ( NIW_Data::get_allergens() as $key => $label ) {
			update_post_meta( $post_id, 'food_allegerns_' . $key, isset( $_POST[ 'food_allegerns_' . $key ] ) ? 1 : 0 );
		}

		// Nutritional info.
		if ( isset( $_POST['food_nutrient_serving'] ) ) {
			update_post_meta( $post_id, 'food_nutrient_serving', sanitize_text_field( wp_unslash( $_POST['food_nutrient_serving'] ) ) );
		}
		foreach ( NIW_Data::get_nutrients() as $key => $def ) {
			$field = 'food_nutrient_' . $key;
			if ( isset( $_POST[ $field ] ) && '' !== $_POST[ $field ] ) {
				update_post_meta( $post_id, $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
			} else {
				delete_post_meta( $post_id, $field );
			}
		}

		// Ingredients.
		$ingredients     = array();
		$raw_ingredients = isset( $_POST['food_ingredients'] ) ? wp_unslash( $_POST['food_ingredients'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( ! empty( $raw_ingredients ) && is_array( $raw_ingredients ) ) {
			foreach ( $raw_ingredients as $item ) {
				$name     = sanitize_text_field( wp_unslash( $item['name'] ?? '' ) );
				$quantity = sanitize_text_field( wp_unslash( $item['quantity'] ?? '' ) );
				if ( '' !== $name ) {
					$ingredients[] = array(
						'name'     => $name,
						'quantity' => $quantity,
					);
				}
			}
		}
		update_post_meta( $post_id, 'food_ingredients', $ingredients );
	}
}
