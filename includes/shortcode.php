<?php
/**
 * Shortcode for nutrition info table.
 *
 * @package CLOSE\NutritionInfo
 */

namespace CLOSE\NutritionInfo;

/**
 * Render nutrition info shortcode.
 *
 * @param array $atts Shortcode attributes (unused).
 */
function niw_shortcode_func( $atts ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
	niw_nutrition_info();
}
add_shortcode( 'nutritiontable', __NAMESPACE__ . '\\niw_shortcode_func' );
