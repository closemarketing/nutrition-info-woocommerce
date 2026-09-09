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
 * @return string
 */
function niw_shortcode_func( $atts ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
	ob_start();
	niw_nutrition_info();
	return ob_get_clean();
}
add_shortcode( 'nutritiontable', __NAMESPACE__ . '\\niw_shortcode_func' );
