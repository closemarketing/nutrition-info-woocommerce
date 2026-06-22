<?php
/**
 * Product tab filters for nutrition and composition.
 *
 * @package CLOSE\NutritionInfo
 */

namespace CLOSE\NutritionInfo;

if ( 'tab' === get_option( 'wc_nutrients_settings_tab_position' ) ) {
	// Add tab to single product pages.
	add_filter( 'woocommerce_product_tabs', __NAMESPACE__ . '\\niw_nutritional_content_tab' );
	add_filter( 'woocommerce_product_tabs', __NAMESPACE__ . '\\niw_composition_content_tab' );
}

/**
 * Add composition tab.
 *
 * @param array $tabs Product tabs.
 * @return array
 */
function niw_composition_content_tab( $tabs ) {
	// Adds the new tab.
	$tabs['composition_tab'] = array(
		'title'    => __( 'Composition', 'nutrition-info-woocommerce' ),
		'priority' => 50,
		'callback' => __NAMESPACE__ . '\\niw_composition_content_tab_content',
	);

	return $tabs;
}

/**
 * Render composition tab content.
 */
function niw_composition_content_tab_content() {
	// The new tab content.
	niw_composition_info();
}

/**
 * Add nutritional info tab.
 *
 * @param array $tabs Product tabs.
 * @return array
 */
function niw_nutritional_content_tab( $tabs ) {
	// Adds the new tab.
	$tabs['nutrient_tab'] = array(
		'title'    => __( 'Nutrients', 'nutrition-info-woocommerce' ),
		'priority' => 50,
		'callback' => __NAMESPACE__ . '\\niw_nutritional_content_tab_content',
	);

	return $tabs;
}

/**
 * Render nutritional info tab content.
 */
function niw_nutritional_content_tab_content() {
	// The new tab content.
	echo '<h2>' . esc_html__( 'Nutritional Information', 'nutrition-info-woocommerce' ) . '</h2>';
	niw_nutrition_info();
}
