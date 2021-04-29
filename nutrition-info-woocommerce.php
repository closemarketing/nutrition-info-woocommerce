<?php
/*
Plugin Name: Nutrition Info for WooCommerce
Plugin URI:  https://www.closemarketing.es
Description: Display nutritional information on you woocommerce product pages.
Version:     0.1
Author:      Closemarketing
Author URI:  https://www.closemarketing.es
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: nutrition-info-woocommerce
Domain Path: /languages
*/

function wni_load_textdomain() {
    load_plugin_textdomain( 'nutrition-info-woocommerce', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'wni_load_textdomain' );

define( 'WNI_BUNDLE_VERSION' , '0.5');
define( 'NIW_PLUGIN_PREFIX', 'niw_');
define( 'WNI_PLUGIN_PATH' , plugin_dir_path(__FILE__));
define( 'WNI_PLUGIN_URL' , plugin_dir_url(__FILE__));

include WNI_PLUGIN_PATH . 'includes/class-woo-settings.php';
include WNI_PLUGIN_PATH . 'includes/class-woo-metaproducts.php';
include WNI_PLUGIN_PATH . 'includes/template.php'; // Nutrients display function
include WNI_PLUGIN_PATH . 'includes/product-tab.php'; // Nutrients display function
include WNI_PLUGIN_PATH . 'includes/allergens.php'; // Allergens


add_action( 'wp_enqueue_scripts', 'niw_styles_frontend' );
/**
 * Proper way to enqueue scripts and styles
 */
function niw_styles_frontend() {
	wp_enqueue_style( 'slider', WNI_PLUGIN_URL . '/css/styles.css', false, WNI_BUNDLE_VERSION , 'all' );
}

if ( get_option( 'wc_nutrients_settings_tab_position' ) == 'after_product_summary' ) {
	add_action( 'woocommerce_single_product_summary', 'nutritionInfo', '45' );
	add_action( 'woocommerce_single_product_summary', 'compositionInfo', '45' );
}

if ( get_option( 'wc_nutrients_settings_tab_position' ) == 'after_add_to_cart' ) {
    add_action('woocommerce_single_product_summary', 'nutritionInfo', '35');
    add_action('woocommerce_single_product_summary', 'compositionInfo', '35');
}

if (get_option( 'wc_nutrients_settings_tab_position' ) == 'after_excerpt') {
    add_action('woocommerce_single_product_summary', 'nutritionInfo', '25');
    add_action('woocommerce_single_product_summary', 'compositionInfo', '25');
}

if (get_option( 'wc_nutrients_settings_tab_position' ) == 'after_price') {
    add_action('woocommerce_single_product_summary', 'nutritionInfo', '15');
    add_action('woocommerce_single_product_summary', 'compositionInfo', '15');
}

if (get_option( 'wc_nutrients_settings_tab_position' ) == 'in_description_tab') {
    add_filter( 'woocommerce_product_tabs', 'woo_custom_description_tab', 98 );
}

function woo_custom_description_tab( $tabs ) {

	$tabs['description']['callback'] = 'woo_custom_description_tab_content';	// Custom description callback

	return $tabs;
}

function woo_custom_description_tab_content() {
    $heading = esc_html( apply_filters( 'woocommerce_product_description_heading', __( 'Description', 'woocommerce' ) ) );
    ?>

    <?php if ( $heading ) : ?>
      <h2><?php echo $heading; ?></h2>
    <?php endif; ?>

    <?php the_content();
	nutritionInfo();
	compositionInfo();
}

/**
 * Function that adds icons of allergens in the view of the products
 */
add_action("woocommerce_after_shop_loop_item_title", "niw_add_allergens_icon", 5);
function niw_add_allergens_icon()
{
	$all_allergens = new Allergens();
	echo "<div class='niw_icon_allergen_product'>";
	// Show activated allergens
	foreach ($all_allergens->show_allergens_name() as $key => $value) {
		
		$allergens_active = get_post_meta( get_the_ID(), NIW_PLUGIN_PREFIX . $key, true  );
		if( $allergens_active == "yes" )
		{
			echo $all_allergens->show_allergen_svg($key);
		}
	}
	echo "</div>";
}

/**
 * Function that adds icons of allergens in the view of each product
 */
add_action("woocommerce_single_product_summary", "niw_add_special_allergens_icon_single_product", 6);
function niw_add_special_allergens_icon_single_product()
{
	$all_allergens = new Allergens();
	echo "<div class='niw_icon_allergen_product'>";
	// Show activated allergens
	foreach ($all_allergens->show_special_allergens_name() as $value) {
		$allergens_active = get_post_meta( get_the_ID(), NIW_PLUGIN_PREFIX . $value, true  );
		if( $allergens_active == "yes" )
		{
			echo $all_allergens->show_special_allergen_svg($key);
		}
	}
	echo "</div>";

}

/**
 * Function that adds icons of allergens in the view of each product
 */
add_action("woocommerce_single_product_summary", "niw_add_allergens_icon_single_product", 10);
function niw_add_allergens_icon_single_product()
{
	$all_allergens = new Allergens();
	echo "<div class='niw_icon_allergen_product'>";
	// Show activated allergens
	foreach ($all_allergens->show_allergens_name() as $key => $value) {
		
		$allergens_active = get_post_meta( get_the_ID(), NIW_PLUGIN_PREFIX . $key, true  );
		if( $allergens_active == "yes" )
		{
			echo $all_allergens->show_allergen_svg($key);
		}
	}
	echo "</div>";
}





/**
 * Feature that overlays product icons on featured image
 */
function replacing_template_loop_product_thumbnail() {
	// Adding something instead
	function wc_template_loop_product_replaced_thumb() {
		// Get outstanding image
		global $post;
		$thumbID = get_post_thumbnail_id( $post->ID );
		$img = wp_get_attachment_url( $thumbID );
		//*********************** */

		echo '<div class="niw-icons-product">';
		// Show activated allergens
		$all_allergens = new Allergens();
		$position_icon=180;
		foreach ($all_allergens->show_allergens_name() as $key => $value) {
			
			$allergens_active = get_post_meta( get_the_ID(), NIW_PLUGIN_PREFIX . $key, true  );
			if ( $allergens_active == 'yes' && $key == 'lacteal' ) {
				echo $all_allergens->show_special_allergen_svg('lacteal');
			}
			else if( $allergens_active == "yes" && $key == "gluten" ) {
				echo $all_allergens->show_special_allergen_svg('gluten');
			}
			else if( $allergens_active == "yes" && $key == "vegan" )
			{
				echo $all_allergens->show_special_allergen_svg('vegan');
			}
		}
		echo "</div>";
	}
	add_action( 'woocommerce_before_shop_loop_item_title', 'wc_template_loop_product_replaced_thumb', 10 );
}
add_action( 'woocommerce_init', 'replacing_template_loop_product_thumbnail');
