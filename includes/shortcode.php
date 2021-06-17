<?php
// [footag foo="bar"]
function niw_shortcode_func ( $atts ) {
	niw_nutrition_info();
}
add_shortcode( 'nutritiontable', 'niw_shortcode_func ' ); ?>
