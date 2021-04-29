<?php

add_filter( 'woocommerce_product_data_tabs', 'add_my_custom_product_data_tab2' , 98 , 1 );
function add_my_custom_product_data_tab2( $product_composition_tabs ) {
    $product_composition_tabs['composition-tab'] = array(
        'label' => __( 'Composition & Allergens', 'composition_info' ),
        'target' => 'ingredients_composition',
    );
    return $product_composition_tabs;
}

// This action will add custom fields to the custom product composition tab added in the Product Data metabox
add_action( 'woocommerce_product_data_panels', 'add_custom_fields_to_product_composition' );
function add_custom_fields_to_product_composition() {
	global $woocommerce, $post;
	?>
	<!-- id below must match target registered in above add_my_custom_product_data_tab function -->
	<div id="ingredients_composition" class="panel woocommerce_options_panel">
		<?php
		$allergens = new Allergens();
		$array_allergens_name = $allergens->show_allergens_name();
		woocommerce_wp_text_input( array(
		'id'          => NIW_PLUGIN_PREFIX . 'ingredients',
		'class'       => '',
		'label'       => __('Ingredients separated by commas', 'composition_info'),
		'description' => __('Ingredients', 'composition_info'),
		'desc_tip'    => false,
		'placeholder' => __('ingredient, ingreditent', 'composition_info')
		) );

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
		
		$gluten = get_post_meta( get_the_ID(), NIW_PLUGIN_PREFIX . 'Gluten', true  );
		$vegan = get_post_meta( get_the_ID(), NIW_PLUGIN_PREFIX . 'Lacteal', true  );
		$lacteal = get_post_meta( get_the_ID(), NIW_PLUGIN_PREFIX . 'Vegan', true  );

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

// Save custom fields data of products tab:
add_action( 'woocommerce_process_product_meta', 'woocommerce_process_product_meta_fields_save_composition' );
function woocommerce_process_product_meta_fields_save_composition( $post_id ){
	update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'ingredients', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'ingredients'] ) );
	$allergens = new Allergens();
	$array_allergens_name = $allergens->show_allergens_name();

	foreach ($array_allergens_name as $key => $value) {
		update_post_meta( $post_id, NIW_PLUGIN_PREFIX . $value, stripslashes( $_POST[NIW_PLUGIN_PREFIX . $value] ) );
	}
	update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'activated_allergens', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'activated_allergens'] ) );
	
} ?>