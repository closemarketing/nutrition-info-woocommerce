<?php
/**
 * Shortcode for nutrition info table.
 *
 * @package nutrition-info-woocommerce
 */

/**
 * Render nutrition info shortcode.
 *
 * @param array $atts Shortcode attributes (unused).
 */
function niw_shortcode_func( $atts ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
	niw_nutrition_info();
}
add_shortcode( 'nutritiontable', 'niw_shortcode_func ' );
