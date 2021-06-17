<?php
/*
Plugin Name: Nutrition Info for WooCommerce
Plugin URI:  https://www.closemarketing.net/plugin/nutrition-info-woocommerce
Description: Display nutritional information on you woocommerce product pages.
Version:     0.1
Author:      Closemarketing
Author URI:  https://www.closemarketing.es
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: nutrition-info-woocommerce
Domain Path: /languages
*/

add_action( 'plugins_loaded', 'niw_load_textdomain' );
function niw_load_textdomain() {
	load_plugin_textdomain( 'nutrition-info-woocommerce', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}


define( 'NIW_BUNDLE_VERSION', '0.1' );
define( 'NIW_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'NIW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once NIW_PLUGIN_PATH . 'includes/class-woo-settings.php';
require_once NIW_PLUGIN_PATH . 'includes/class-woo-metaproducts.php';
require_once NIW_PLUGIN_PATH . 'includes/template.php';
require_once NIW_PLUGIN_PATH . 'includes/product-tab.php';
require_once NIW_PLUGIN_PATH . 'includes/allergens.php';

$kses_defaults = wp_kses_allowed_html( 'post' );
$svg_args      = array(
	'svg'   => array(
		'class'           => true,
		'aria-hidden'     => true,
		'aria-labelledby' => true,
		'role'            => true,
		'xmlns'           => true,
		'width'           => true,
		'height'          => true,
		'viewbox'         => true, // <= Must be lower case!
	),
	'g'     => array( 'fill' => true ),
	'title' => array( 'title' => true ),
	'path'  => array(
		'd'    => true,
		'fill' => true,
	),
);

$allowed_tags_svg = array_merge( $kses_defaults, $svg_args );

add_action( 'wp_enqueue_scripts', 'niw_styles_frontend' );
/**
 * Proper way to enqueue scripts and styles
 */
function niw_styles_frontend() {
	wp_enqueue_style( 'slider', NIW_PLUGIN_URL . '/css/styles.css', false, NIW_BUNDLE_VERSION, 'all' );
}

if ( get_option( 'wc_nutrients_settings_tab_position' ) == 'after_product_summary' ) {
	add_action( 'woocommerce_single_product_summary', 'niw_nutrition_info', '45' );
	add_action( 'woocommerce_single_product_summary', 'niw_composition_info', '45' );
}

if ( get_option( 'wc_nutrients_settings_tab_position' ) == 'after_add_to_cart' ) {
	add_action( 'woocommerce_single_product_summary', 'niw_nutrition_info', '35' );
	add_action( 'woocommerce_single_product_summary', 'niw_composition_info', '35' );
}

if ( get_option( 'wc_nutrients_settings_tab_position' ) == 'after_excerpt' ) {
	add_action( 'woocommerce_single_product_summary', 'niw_nutrition_info', '25' );
	add_action( 'woocommerce_single_product_summary', 'niw_composition_info', '25' );
}

if ( get_option( 'wc_nutrients_settings_tab_position' ) == 'after_price' ) {
	add_action( 'woocommerce_single_product_summary', 'niw_nutrition_info', '15' );
	add_action( 'woocommerce_single_product_summary', 'niw_composition_info', '15' );
}

if (get_option( 'wc_nutrients_settings_tab_position' ) == 'in_description_tab' ) {
	add_filter( 'woocommerce_product_tabs', 'niw_custom_description_tab', 98 );
}

function niw_custom_description_tab( $tabs ) {

	$tabs['description']['callback'] = 'niw_custom_description_tab_content';	// Custom description callback

	return $tabs;
}

function niw_custom_description_tab_content() {
	$heading = esc_html( apply_filters( 'niwcommerce_product_description_heading', __( 'Description', 'woocommerce' ) ) );
	if ( $heading ) {
		echo '<h2>' . esc_html( $heading ) . '</h2>';
	}
	the_content();
	niw_nutrition_info();
	niw_composition_info();
}

/**
 * Function that adds icons of allergens in the view of the products
 */
add_action( 'woocommerce_after_shop_loop_item_title', 'niw_add_allergens_icon', 5);
function niw_add_allergens_icon() {
	global $allowed_tags_svg;
	$all_allergens = new NIW_Allergens();
	echo "<div class='niw_icon_allergen_product'>";
	// Show activated allergens.
	foreach ( $all_allergens->show_allergens_name() as $key => $value ) {
		$allergens_active = get_post_meta( get_the_ID(), 'niw_all_' . $key, true  );
		if ( $allergens_active == "yes" ) {
			echo '<div class="niw_svg_container"><div class="niw_svg_container_span">' . esc_html__( $value, 'nutrition-info-woocommerce' ) . '</div>';
			echo wp_kses( $all_allergens->show_allergen_svg( $key ), $allowed_tags_svg );
			echo '</div>';
		}
	}
	echo '</div>';
}

/**
 * Function that adds icons of allergens in the view of each product
 */
add_action( 'woocommerce_single_product_summary', 'niw_add_special_allergens_icon_single_product', 6 );
function niw_add_special_allergens_icon_single_product() {
	global $allowed_tags_svg;
	$all_allergens = new NIW_Allergens();
	echo "<div class='niw_icon_allergen_product'>";
	$allergen_vegan = get_post_meta( get_the_ID(), 'niw_all_vegan', true  );
	if ( 'yes' == $allergen_vegan ) {
		echo '<div class="niw_svg_container"><div class="niw_svg_container_span">' . esc_html__( 'Vegan', 'nutrition-info-woocommerce' ) . '</div>';
		echo wp_kses( $all_allergens->show_allergen_svg_vegan(), $allowed_tags_svg );
		echo '</div>';
	}
	echo '</div>';

}

add_action( 'woocommerce_single_product_summary', 'niw_add_allergens_icon_single_product', 10 );
/**
 * Function that adds icons of allegens in the view of each product
 *
 * @return void
 */
function niw_add_allergens_icon_single_product() {
	$all_allergens = new NIW_Allergens();
	echo "<div class='niw_icon_allergen_product'>";
	// Show activated allergens
	foreach ( $all_allergens->show_allergens_name() as $key => $value ) {
		$allergens_active = get_post_meta( get_the_ID(), 'niw_all_' . $key, true );

		if( 'yes' == $allergens_active ) {
			echo '<div class="niw_svg_container"><div class="niw_svg_container_span">' . esc_html__( $value, 'nutrition-info-woocommerce' ) . '</div>';
			echo wp_kses( $all_allergens->show_allergen_svg( $key ), $allowed_tags_svg );
			echo '</div>';
		}
	}
	echo '</div>';
}


add_action( 'woocommerce_before_shop_loop_item_title', 'niw_template_loop_product_replaced_thumb', 10 );

function niw_template_loop_product_replaced_thumb() {
	echo '<div class="niw_icons_product">';
	// Show activated allergens.
	$all_allergens  = new NIW_Allergens();
	$allergen_vegan = get_post_meta( get_the_ID(), 'niw_all_' . 'vegan', true  );
	if ( 'yes' == $allergen_vegan ) {
		echo '<div class="niw_svg_container_span">' . __( 'Vegan', 'nutrition-info-woocommerce' ) . '</div>';
		echo wp_kses( $all_allergens->show_allergen_svg_vegan(), $allowed_tags_svg );
	}
	echo '</div>';
}