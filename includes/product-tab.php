<?php
if ( get_option( 'wc_nutrients_settings_tab_position' ) == 'tab' ) {
	// Add tab to single product pages
	add_filter( 'woocommerce_product_tabs', 'niw_nutritional_content_tab' );
	add_filter( 'woocommerce_product_tabs', 'niw_composition_content_tab' );
}

function niw_composition_content_tab( $tabs ) {

	// Adds the new tab
	$tabs['composition_tab'] = array(
		'title' 	=> __( 'Composition', 'nutrition-info-woocommerce' ),
		'priority' 	=> 50,
		'callback' 	=> 'niw_composition_content_tab_content'
	);

	return $tabs;

}
function niw_composition_content_tab_content() {

	// The new tab content
	niw_composition_info();

}

function niw_nutritional_content_tab( $tabs ) {

	// Adds the new tab
	$tabs['nutrient_tab'] = array(
		'title' 	=> __( 'Nutrients', 'nutrition-info-woocommerce' ),
		'priority' 	=> 50,
		'callback' 	=> 'niw_nutritional_content_tab_content'
	);

	return $tabs;

}
function niw_nutritional_content_tab_content() {
	// The new tab content
	echo '<h2>' . __('New Product Tab', 'nutrition-info-woocommerce') . '</h2>';
	niw_nutrition_info();
}

