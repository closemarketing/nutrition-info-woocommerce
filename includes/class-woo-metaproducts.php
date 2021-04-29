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

		add_filter( 'woocommerce_product_data_tabs', 'add_my_custom_product_data_tab2' , 98 , 1 );
		add_action( 'woocommerce_product_data_panels', 'add_custom_fields_to_product_composition' );
		add_action( 'woocommerce_process_product_meta', 'woocommerce_process_product_meta_fields_save_composition' );
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
			woocommerce_wp_text_input(
				array(
					'id'          => NIW_PLUGIN_PREFIX . 'ingredients',
					'class'       => '',
					'label'       => __('Ingredients separated by commas', 'nutrition-info-woocommerce'),
					'description' => __('Ingredients', 'nutrition-info-woocommerce'),
					'desc_tip'    => false,
					'placeholder' => __('ingredient, ingreditent', 'nutrition-info-woocommerce')
				)
			);

			foreach ($array_allergens_name as $key => $value) {
				woocommerce_wp_checkbox( 
					array( 
						'id'            => NIW_PLUGIN_PREFIX . $value, 
						'wrapper_class' => '', 
						'label'         => __( $value, 'woocommerce' ), 
						'description'   => __( '', 'woocommerce' ) 
					)
				);
			}
			
			$gluten  = get_post_meta( get_the_ID(), NIW_PLUGIN_PREFIX . 'Gluten', true );
			$vegan   = get_post_meta( get_the_ID(), NIW_PLUGIN_PREFIX . 'Lacteal', true );
			$lacteal = get_post_meta( get_the_ID(), NIW_PLUGIN_PREFIX . 'Vegan', true );

			$gluten_activated = '';
			$vegan_activated = '';
			$lacteal_activated = '';

			$number_allergens_actived = array();

			if( $gluten != 'yes' )
			{
				$gluten_activated = 'Gluten';
				array_push( $number_allergens_actived, 'Gluten' );
			}
			if( $vegan != 'yes' )
			{
				$vegan_activated = 'Vegan';
				array_push( $number_allergens_actived, 'Vegan' );
			}
			if( $lacteal != 'yes' )
			{
				$lacteal_activated = 'Lacteal';
				array_push( $number_allergens_actived, 'Lacteal' );
			}

			
			$select_field = array(
				'id' => NIW_PLUGIN_PREFIX . 'activated_allergens',
				'label' => __( 'Activated allergens', 'woocommerce' ),
				'options' => array(
					'Gluten' => __( $gluten_activated, 'woocommerce' ),
					'Lacteal' => __( $lacteal_activated, 'woocommerce' ),
					'Vegan' => __( $vegan_activated, 'woocommerce' )
					)
			);
			woocommerce_wp_select( $select_field );
			

			?>
		</div>
		<?php
	} 

	function woocommerce_process_product_meta_fields_save_composition( $post_id ){
		update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'ingredients', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'ingredients'] ) );
		$allergens = new Allergens();
		$array_allergens_name = $allergens->show_allergens_name();

		foreach ($array_allergens_name as $key => $value) {
			update_post_meta( $post_id, NIW_PLUGIN_PREFIX . $value, stripslashes( $_POST[NIW_PLUGIN_PREFIX . $value] ) );
		}
		update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'activated_allergens', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'activated_allergens'] ) );
		
	}
}

new NIW_MetaProducts();
