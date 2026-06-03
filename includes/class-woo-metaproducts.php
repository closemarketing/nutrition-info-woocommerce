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

		// Meta Info.
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_my_custom_product_data_tab' ), 99, 1 );

		// This action will add custom fields to the added custom tabs under Products Data metabox.
		add_action( 'woocommerce_product_data_panels', array( $this, 'add_my_custom_product_data_fields' ) );
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
			foreach ( $array_allergens_name as $key => $value ) {
				woocommerce_wp_checkbox(
					array(
						'id'            => 'niw_all_' . $key,
						'wrapper_class' => '',
						'label'         => '',
						'description'   => esc_html( $value ),
					)
				);
			}
			woocommerce_wp_checkbox(
				array(
					'id'            => 'niw_all_vegan',
					'wrapper_class' => '',
					'label'         => '',
					'description'   => __( 'Vegan', 'nutrition-info-woocommerce' ),
				)
			);
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
			<?php
			woocommerce_wp_text_input(
				array(
					'id'          => 'niw_energy',
					'class'       => '',
					'label'       => __( 'Energy', 'nutrition-info-woocommerce' ),
					'description' => __( '(KJ/kcal)', 'nutrition-info-woocommerce' ),
					'desc_tip'    => false,
					'placeholder' => __( '0 Kj / 0 kcal', 'nutrition-info-woocommerce' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => 'niw_fat',
					'class'       => '',
					'label'       => __( 'Fat', 'nutrition-info-woocommerce' ),
					'description' => __( '(gram)', 'nutrition-info-woocommerce' ),
					'desc_tip'    => false,
					'placeholder' => __( '0 g', 'nutrition-info-woocommerce' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => 'niw_saturated_fat',
					'class'       => '',
					'label'       => __( 'Saturated fatty acids', 'nutrition-info-woocommerce' ),
					'description' => __( '(gram)', 'nutrition-info-woocommerce' ),
					'desc_tip'    => false,
					'placeholder' => __( '0 g', 'nutrition-info-woocommerce' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => 'niw_monounsaturated_fat',
					'class'       => '',
					'label'       => __( 'Monounsaturated fatty acids', 'nutrition-info-woocommerce' ),
					'description' => __( '(gram)', 'nutrition-info-woocommerce' ),
					'desc_tip'    => false,
					'placeholder' => __( '0 g', 'nutrition-info-woocommerce' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => 'niw_polyunsaturated_fat',
					'class'       => '',
					'label'       => __( 'Polyunsaturated fatty acids', 'nutrition-info-woocommerce' ),
					'description' => __( '(gram)', 'nutrition-info-woocommerce' ),
					'desc_tip'    => false,
					'placeholder' => __( '0 g', 'nutrition-info-woocommerce' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => 'niw_carb',
					'class'       => '',
					'label'       => __( 'Carbohydrate', 'nutrition-info-woocommerce' ),
					'description' => __( '(gram)', 'nutrition-info-woocommerce' ),
					'desc_tip'    => false,
					'placeholder' => __( '0 g', 'nutrition-info-woocommerce' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => 'niw_sugar',
					'class'       => '',
					'label'       => __( 'Sugar', 'nutrition-info-woocommerce' ),
					'description' => __( '(gram)', 'nutrition-info-woocommerce' ),
					'desc_tip'    => false,
					'placeholder' => __( '0 g', 'nutrition-info-woocommerce' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => 'niw_polyol',
					'class'       => '',
					'label'       => __( 'Polyols', 'nutrition-info-woocommerce' ),
					'description' => __( '(gram)', 'nutrition-info-woocommerce' ),
					'desc_tip'    => false,
					'placeholder' => __( '0 g', 'nutrition-info-woocommerce' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => 'niw_starch',
					'class'       => '',
					'label'       => __( 'Starch', 'nutrition-info-woocommerce' ),
					'description' => __( '(gram)', 'nutrition-info-woocommerce' ),
					'desc_tip'    => false,
					'placeholder' => __( '0 g', 'nutrition-info-woocommerce' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => 'niw_fiber',
					'class'       => '',
					'label'       => __( 'Dietary fiber', 'nutrition-info-woocommerce' ),
					'description' => __( '(gram)', 'nutrition-info-woocommerce' ),
					'desc_tip'    => false,
					'placeholder' => __( '0 g', 'nutrition-info-woocommerce' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => 'niw_protein',
					'class'       => '',
					'label'       => __( 'Protein', 'nutrition-info-woocommerce' ),
					'description' => __( '(gram)', 'nutrition-info-woocommerce' ),
					'desc_tip'    => false,
					'placeholder' => __( '0 g', 'nutrition-info-woocommerce' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => 'niw_salt',
					'class'       => '',
					'label'       => __( 'Salt', 'nutrition-info-woocommerce' ),
					'description' => __( '(gram)', 'nutrition-info-woocommerce' ),
					'desc_tip'    => false,
					'placeholder' => __( '0 g', 'nutrition-info-woocommerce' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => 'niw_vitamin_mineral',
					'class'       => '',
					'label'       => __( 'Vitamins and minerals', 'nutrition-info-woocommerce' ),
					'description' => __( '(gram)', 'nutrition-info-woocommerce' ),
					'desc_tip'    => false,
					'placeholder' => __( 'none', 'nutrition-info-woocommerce' ),
				)
			);

			?>
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

		$metas_composition = array(
			'niw_energy',
			'niw_fat',
			'niw_saturated_fat',
			'niw_monounsaturated_fat',
			'niw_polyunsaturated_fat',
			'niw_carb',
			'niw_sugar',
			'niw_polyol',
			'niw_starch',
			'niw_fiber',
			'niw_protein',
			'niw_salt',
			'niw_vitamin_mineral',
			'niw_ingredients',
		);

		foreach ( $metas_composition as $composition ) {
			if ( isset( $_POST[ $composition ] ) ) {
				update_post_meta( $post_id, $composition, sanitize_text_field( wp_unslash( $_POST[ $composition ] ) ) );
			}
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
