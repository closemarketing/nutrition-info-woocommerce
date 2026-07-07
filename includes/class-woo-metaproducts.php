<?php
/**
 * Product Meta data
 *
 * @package    CLOSE\NutritionInfo
 * @author     David Pérez <david@closemarketing.es>
 * @copyright  2021 Closemarketing
 * @version    1.0
 */

namespace CLOSE\NutritionInfo;

defined( 'ABSPATH' ) || exit;

/**
 * Class Meta Products.
 *
 * WooCommerce adds meta products.
 *
 * @since 1.0
 */
class MetaProducts {

	/**
	 * Construct of Class
	 */
	public function __construct() {

		add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_my_custom_product_data_tab2' ), 98, 1 );
		add_action( 'woocommerce_product_data_panels', array( $this, 'add_custom_fields_to_product_composition' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'woocommerce_process_product_meta_fields_save' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );

		// Meta Info.
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_my_custom_product_data_tab' ), 99, 1 );

		// This action will add custom fields to the added custom tabs under Products Data metabox.
		add_action( 'woocommerce_product_data_panels', array( $this, 'add_my_custom_product_data_fields' ) );
	}

	/**
	 * Enqueue admin stylesheet on product edit screens.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_admin_styles( $hook ) {
		if ( ( 'post.php' !== $hook && 'post-new.php' !== $hook ) || 'product' !== get_post_type() ) {
			return;
		}
		wp_enqueue_style( 'niw-admin', NIW_PLUGIN_URL . 'assets/css/admin.css', array(), NIW_BUNDLE_VERSION );
	}

	/**
	 * Add composition tab to product data tabs.
	 *
	 * @param array $product_composition_tabs Product data tabs.
	 * @return array
	 */
	public function add_my_custom_product_data_tab2( $product_composition_tabs ) {
		$product_composition_tabs['composition-tab'] = array(
			'label'  => __( 'Composition & Allergens', 'nutrition-info-woocommerce' ),
			'target' => 'ingredients_composition',
		);
		return $product_composition_tabs;
	}

	/**
	 * Add custom fields to product composition tab.
	 */
	public function add_custom_fields_to_product_composition() {
		global $woocommerce, $post;
		?>
		<div id="ingredients_composition" class="panel woocommerce_options_panel">
			<?php
			$allergens            = new Allergens();
			$array_allergens_name = $allergens->show_allergens_name();
			woocommerce_wp_textarea_input(
				array(
					'id'          => 'niw_ingredients',
					'class'       => '',
					'label'       => __( 'Ingredients', 'nutrition-info-woocommerce' ),
					'description' => '',
					'desc_tip'    => false,
					'placeholder' => __( 'Ingredients', 'nutrition-info-woocommerce' ),
				)
			);

			echo '<h2>' . esc_html__( 'Allergens', 'nutrition-info-woocommerce' ) . '</h2>';
			echo '<div class="niw-allergens-grid">';
			foreach ( $array_allergens_name as $key => $value ) {
				woocommerce_wp_checkbox(
					array(
						'id'            => 'niw_all_' . $key,
						'wrapper_class' => 'niw-allergens-grid__item',
						'label'         => '',
						'description'   => esc_html( $value ),
					)
				);
			}
			woocommerce_wp_checkbox(
				array(
					'id'            => 'niw_all_vegan',
					'wrapper_class' => 'niw-allergens-grid__item',
					'label'         => '',
					'description'   => __( 'Vegan', 'nutrition-info-woocommerce' ),
				)
			);
			echo '</div>';
			?>
		</div>
		<?php
	}

	/**
	 * Add nutritional info tab to product data tabs.
	 *
	 * @param array $product_data_tabs Product data tabs.
	 * @return array
	 */
	public function add_my_custom_product_data_tab( $product_data_tabs ) {
		$product_data_tabs['my-custom-tab'] = array(
			'label'  => __( 'Nutritional Info', 'nutrition-info-woocommerce' ),
			'target' => 'my_custom_product_data',
		);
		return $product_data_tabs;
	}

	/**
	 * Add custom nutritional fields to product data panel.
	 */
	public function add_my_custom_product_data_fields() {
		global $woocommerce, $post;
		?>
		<!-- id below must match target registered in above add_my_custom_product_data_tab function -->
		<div id="my_custom_product_data" class="panel woocommerce_options_panel">
			<p class="niw-nutrition-note">
				<strong><?php esc_html_e( 'All values below are always per 100 g of product.', 'nutrition-info-woocommerce' ); ?></strong>
			</p>
			<table class="niw-nutrition-admin-table">
				<colgroup>
					<col class="niw-nutrition-admin-table__col--label" />
					<col class="niw-nutrition-admin-table__col--input" />
					<col class="niw-nutrition-admin-table__col--unit" />
				</colgroup>
				<thead>
					<tr>
						<th><?php esc_html_e( 'Nutrient', 'nutrition-info-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Value per 100 g', 'nutrition-info-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Unit', 'nutrition-info-woocommerce' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( niw_get_nutrition_fields() as $key => $field ) : ?>
					<tr class="<?php echo $field['sub'] ? 'niw-nutrition-admin-table__row--sub' : ''; ?>">
						<td class="niw-nutrition-admin-table__label">
							<span id="niw_<?php echo esc_attr( $key ); ?>_label"><?php echo esc_html( ( $field['sub'] ? '- ' : '' ) . $field['label'] ); ?></span>
						</td>
						<td class="niw-nutrition-admin-table__input">
							<input
								type="<?php echo 'vitamin_mineral' === $key ? 'text' : 'number'; ?>"
								<?php if ( 'vitamin_mineral' !== $key ) : ?>
								step="0.01"
								min="0"
								<?php endif; ?>
								id="niw_<?php echo esc_attr( $key ); ?>"
								name="niw_<?php echo esc_attr( $key ); ?>"
								value="<?php echo esc_attr( get_post_meta( $post->ID, 'niw_' . $key, true ) ); ?>"
								placeholder="<?php echo 'vitamin_mineral' === $key ? esc_attr__( 'none', 'nutrition-info-woocommerce' ) : '0'; ?>"
								aria-labelledby="niw_<?php echo esc_attr( $key ); ?>_label"
							/>
						</td>
						<td class="niw-nutrition-admin-table__unit"><?php echo esc_html( $field['unit'] ); ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Save custom product meta fields.
	 *
	 * @param int $post_id Post ID.
	 */
	public function woocommerce_process_product_meta_fields_save( $post_id ) {
		if ( ! isset( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['woocommerce_meta_nonce'] ), 'woocommerce_save_data' ) ) {
			return;
		}

		// Nutrition fields — numeric ones are always stored as a plain number per 100 g.
		foreach ( array_keys( niw_get_nutrition_fields() ) as $key ) {
			$composition = 'niw_' . $key;
			if ( ! isset( $_POST[ $composition ] ) ) {
				continue;
			}
			$value = wp_unslash( $_POST[ $composition ] );
			if ( 'vitamin_mineral' === $key ) {
				update_post_meta( $post_id, $composition, sanitize_text_field( $value ) );
			} else {
				update_post_meta( $post_id, $composition, '' === $value ? '' : wc_format_decimal( $value ) );
			}
		}

		if ( isset( $_POST['niw_ingredients'] ) ) {
			update_post_meta( $post_id, 'niw_ingredients', sanitize_text_field( wp_unslash( $_POST['niw_ingredients'] ) ) );
		}

		// Other tab.
		$allergens            = new Allergens();
		$array_allergens_name = $allergens->show_allergens_name();

		$all_allergens_names = array();
		$all_allergens_not   = array();
		foreach ( $array_allergens_name as $key => $value ) {

			$post_meta = isset( $_POST[ 'niw_all_' . $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'niw_all_' . $key ] ) ) : '';
			update_post_meta( $post_id, 'niw_all_' . $key, $post_meta );

			if ( $post_meta ) {
				$all_allergens_names[] = $value;
			}
			if ( ! isset( $_POST[ 'niw_all_' . $key ] ) ) {
				/* translators: %s: allergen name */
				$all_allergens_not[] = sprintf( __( 'Without %s', 'nutrition-info-woocommerce' ), $value );
			}
		}

		$post_meta = isset( $_POST['niw_all_vegan'] ) ? sanitize_text_field( wp_unslash( $_POST['niw_all_vegan'] ) ) : '';
		update_post_meta( $post_id, 'niw_all_vegan', $post_meta );

		// Not allergens.
		update_post_meta( $post_id, 'niw_all_allergens_names', sanitize_text_field( implode( ', ', $all_allergens_names ) ) );
		update_post_meta( $post_id, 'niw_all_allergens_not', sanitize_text_field( implode( ', ', $all_allergens_not ) ) );
	}
}
