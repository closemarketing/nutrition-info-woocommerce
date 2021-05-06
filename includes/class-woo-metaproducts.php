<?php
/**
 * Product Meta data
 *
 * @package    WordPress
 * @author     David Pérez <david@closemarketing.es>
 * @copyright  2021 Closemarketing
 * @version    1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Meta Products.
 *
 * WooCommerce adds meta products.
 *
 * @since 1.0
 */
class NIW_MetaProducts {

	/**
	 * Construct of Class
	 */
	public function __construct() {

		add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_my_custom_product_data_tab2' ) , 98 , 1 );
		add_action( 'woocommerce_product_data_panels', array( $this, 'add_custom_fields_to_product_composition' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'woocommerce_process_product_meta_fields_save' ) );

		// Meta Info.
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_my_custom_product_data_tab' ) , 99 , 1 );

		// This action will add custom fields to the added custom tabs under Products Data metabox
		add_action( 'woocommerce_product_data_panels', array( $this, 'add_my_custom_product_data_fields' ) );
	}

	/**
	 * # Functions
	 * ---------------------------------------------------------------------------------------------------- */

	function add_my_custom_product_data_tab2( $product_composition_tabs ) {
		$product_composition_tabs['composition-tab'] = array(
			'label' => __( 'Composition & Allergens', 'nutrition-info-woocommerce' ),
			'target' => 'ingredients_composition',
		);
		return $product_composition_tabs;
	}

	function add_custom_fields_to_product_composition() {
		global $woocommerce, $post;
		?>
		<div id="ingredients_composition" class="panel woocommerce_options_panel">
			<?php
			$allergens = new Allergens();
			$array_allergens_name = $allergens->show_allergens_name();
			woocommerce_wp_textarea_input(
				array(
					'id'          => NIW_PLUGIN_PREFIX . 'ingredients',
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
						'id'            => NIW_PLUGIN_PREFIX . $key,
						'wrapper_class' => '',
						'label'         => '',
						'description'   => __( $value, 'nutrition-info-woocommerce' ),
					)
				);
			}

			$array_special_allergens_name = $allergens->show_special_allergens_name();
			foreach ( $array_special_allergens_name as $key => $value ) {
				woocommerce_wp_checkbox( 
					array( 
						'id'            => NIW_PLUGIN_PREFIX . $key,
						'wrapper_class' => '',
						'label'         => '',
						'description'   => __( $value, 'nutrition-info-woocommerce' ),
					)
				);
			}
			?>
		</div>
		<?php
	} 

	/**
	 * # Meta info
	 * ---------------------------------------------------------------------------------------------------- */

	function add_my_custom_product_data_tab( $product_data_tabs ) {
		$product_data_tabs['my-custom-tab'] = array(
			'label' => __( 'Nutritional Info', 'nutrition-info-woocommerce' ),
			'target' => 'my_custom_product_data',
		);
		return $product_data_tabs;
	}

	function add_my_custom_product_data_fields() {
		global $woocommerce, $post;
		?>
		<!-- id below must match target registered in above add_my_custom_product_data_tab function -->
		<div id="my_custom_product_data" class="panel woocommerce_options_panel">
			<?php
			woocommerce_wp_text_input( array(
				'id'          => NIW_PLUGIN_PREFIX . 'energy',
				'class'       => '',
				'label'       => __('Energy', 'nutrition-info-woocommerce'),
				'description' => __('(KJ/kcal)', 'nutrition-info-woocommerce'),
				'desc_tip'    => false,
				'placeholder' => __('0 Kj / 0 kcal', 'nutrition-info-woocommerce')
			) );
			woocommerce_wp_text_input( array(
				'id'          => NIW_PLUGIN_PREFIX . 'fat',
				'class'       => '',
				'label'       => __('Fat', 'nutrition-info-woocommerce'),
				'description' => __('(gram)', 'nutrition-info-woocommerce'),
				'desc_tip'    => false,
				'placeholder' => __('0 g', 'nutrition-info-woocommerce')
			) );
			woocommerce_wp_text_input( array(
				'id'          => NIW_PLUGIN_PREFIX . 'saturated_fat',
				'class'       => '',
				'label'       => __('Saturated fatty acids', 'nutrition-info-woocommerce'),
				'description' => __('(gram)', 'nutrition-info-woocommerce'),
				'desc_tip'    => false,
				'placeholder' => __('0 g', 'nutrition-info-woocommerce')
			) );
			woocommerce_wp_text_input( array(
				'id'          => NIW_PLUGIN_PREFIX . 'monounsaturated_fat',
				'class'       => '',
				'label'       => __('Monounsaturated fatty acids', 'nutrition-info-woocommerce'),
				'description' => __('(gram)', 'nutrition-info-woocommerce'),
				'desc_tip'    => false,
				'placeholder' => __('0 g', 'nutrition-info-woocommerce')
			) );
			woocommerce_wp_text_input( array(
				'id'          => NIW_PLUGIN_PREFIX . 'polyunsaturated_fat',
				'class'       => '',
				'label'       => __('Polyunsaturated fatty acids', 'nutrition-info-woocommerce'),
				'description' => __('(gram)', 'nutrition-info-woocommerce'),
				'desc_tip'    => false,
				'placeholder' => __('0 g', 'nutrition-info-woocommerce')
			) );
			woocommerce_wp_text_input( array(
				'id'          => NIW_PLUGIN_PREFIX . 'carb',
				'class'       => '',
				'label'       => __('Carbohydrate', 'nutrition-info-woocommerce'),
				'description' => __('(gram)', 'nutrition-info-woocommerce'),
				'desc_tip'    => false,
				'placeholder' => __('0 g', 'nutrition-info-woocommerce')
			) );
			woocommerce_wp_text_input( array(
				'id'          => NIW_PLUGIN_PREFIX . 'sugar',
				'class'       => '',
				'label'       => __('Sugar', 'nutrition-info-woocommerce'),
				'description' => __('(gram)', 'nutrition-info-woocommerce'),
				'desc_tip'    => false,
				'placeholder' => __('0 g', 'nutrition-info-woocommerce')
			) );
			woocommerce_wp_text_input( array(
				'id'          => NIW_PLUGIN_PREFIX . 'polyol',
				'class'       => '',
				'label'       => __('Polyols', 'nutrition-info-woocommerce'),
				'description' => __('(gram)', 'nutrition-info-woocommerce'),
				'desc_tip'    => false,
				'placeholder' => __('0 g', 'nutrition-info-woocommerce')
			) );
			woocommerce_wp_text_input( array(
				'id'          => NIW_PLUGIN_PREFIX . 'starch',
				'class'       => '',
				'label'       => __('Starch', 'nutrition-info-woocommerce'),
				'description' => __('(gram)', 'nutrition-info-woocommerce'),
				'desc_tip'    => false,
				'placeholder' => __('0 g', 'nutrition-info-woocommerce')
			) );
			woocommerce_wp_text_input( array(
				'id'          => NIW_PLUGIN_PREFIX . 'fiber',
				'class'       => '',
				'label'       => __('Dietary fiber', 'nutrition-info-woocommerce'),
				'description' => __('(gram)', 'nutrition-info-woocommerce'),
				'desc_tip'    => false,
				'placeholder' => __('0 g', 'nutrition-info-woocommerce')
			) );
			woocommerce_wp_text_input( array(
				'id'          => NIW_PLUGIN_PREFIX . 'protein',
				'class'       => '',
				'label'       => __('Protein', 'nutrition-info-woocommerce'),
				'description' => __('(gram)', 'nutrition-info-woocommerce'),
				'desc_tip'    => false,
				'placeholder' => __('0 g', 'nutrition-info-woocommerce')
			) );
			woocommerce_wp_text_input( array(
				'id'          => NIW_PLUGIN_PREFIX . 'salt',
				'class'       => '',
				'label'       => __('Salt', 'nutrition-info-woocommerce'),
				'description' => __('(gram)', 'nutrition-info-woocommerce'),
				'desc_tip'    => false,
				'placeholder' => __('0 g', 'nutrition-info-woocommerce')
			) );
			woocommerce_wp_text_input( array(
				'id'          => NIW_PLUGIN_PREFIX . 'vitamin_mineral',
				'class'       => '',
				'label'       => __('Vitamins and minerals', 'nutrition-info-woocommerce'),
				'description' => __('(gram)', 'nutrition-info-woocommerce'),
				'desc_tip'    => false,
				'placeholder' => __('none', 'nutrition-info-woocommerce')
			) );

			?>
		</div>
		<?php
	}

	function woocommerce_process_product_meta_fields_save( $post_id ){
		update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'energy', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'energy'] ) );
		update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'fat', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'fat'] ) );
		update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'saturated_fat', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'saturated_fat'] ) );
		update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'monounsaturated_fat', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'monounsaturated_fat'] ) );
		update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'polyunsaturated_fat', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'polyunsaturated_fat'] ) );
		update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'carb', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'carb'] ) );
		update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'sugar', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'sugar'] ) );
		update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'polyol', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'polyol'] ) );
		update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'starch', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'starch'] ) );
		update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'fiber', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'fiber'] ) );
		update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'protein', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'protein'] ) );
		update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'salt', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'salt'] ) );
		update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'vitamin_mineral', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'vitamin_mineral'] ) );

		// Other tab.
		update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'ingredients', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'ingredients'] ) );
		$allergens = new Allergens();
		$array_allergens_name = $allergens->show_allergens_name();
		$allergens_especial   = $allergens->show_special_allergens_name();

		$allergens_especial_actived = array();
		foreach ( $array_allergens_name as $key => $value ) {

			$post_meta = isset( $_POST[ NIW_PLUGIN_PREFIX . $key ] ) ? $_POST[ NIW_PLUGIN_PREFIX . $key ] : '';
			update_post_meta( $post_id, NIW_PLUGIN_PREFIX . $key, stripslashes( $post_meta ) );
		}

		foreach ( $allergens_especial as $key => $value ) {
			$post_meta = isset( $_POST[ NIW_PLUGIN_PREFIX . $key ] ) ? $_POST[ NIW_PLUGIN_PREFIX . $key ] : '';
			update_post_meta( $post_id, NIW_PLUGIN_PREFIX . $key, stripslashes( $post_meta ) );
			if ( 'yes' == $post_meta ) {
				$allergens_especial_actived[] = $value;
			}
		}

		update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'activated_special_allergens', $allergens_especial_actived );
	}

}

new NIW_MetaProducts();
