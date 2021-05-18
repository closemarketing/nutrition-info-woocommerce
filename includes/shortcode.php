<?php
// [footag foo="bar"]
function niw_shortcode_func ( $atts ) {
    nutritionInfo();
}
add_shortcode( 'nutritiontable', 'niw_shortcode_func ' ); ?>
