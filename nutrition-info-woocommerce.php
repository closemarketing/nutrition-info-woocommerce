<?php
/**
 * Plugin Name: Nutrition Info for WooCommerce
 * Plugin URI:  https://www.closemarketing.net/plugin/nutrition-info-woocommerce
 * Description: Display nutritional information and allergen icons on your WooCommerce product pages.
 * Version:     1.0.0
 * Author:      Closemarketing
 * Author URI:  https://www.closemarketing.es
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: nutrition-info-woocommerce
 * Requires plugins:   woocommerce
 * Domain Path: /languages
 *
 * @package nutrition-info-woocommerce
 */

defined( 'ABSPATH' ) || exit;

define( 'NIW_BUNDLE_VERSION', '1.0.0' );
define( 'NIW_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'NIW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once NIW_PLUGIN_PATH . 'vendor/autoload.php';

add_action( 'plugins_loaded', 'niw_load_textdomain' );
/**
 * Load plugin textdomain.
 */
function niw_load_textdomain(): void {
	load_plugin_textdomain( 'nutrition-info-woocommerce', false, basename( __DIR__ ) . '/languages/' );
}

add_action(
	'plugins_loaded',
	function (): void {
		require_once NIW_PLUGIN_PATH . 'includes/template.php';
		require_once NIW_PLUGIN_PATH . 'includes/product-tab.php';
		require_once NIW_PLUGIN_PATH . 'includes/shortcode.php';

		\CLOSE\NutritionInfo\WooSettings::init();
		new \CLOSE\NutritionInfo\MetaProducts();
		new \CLOSE\NutritionInfo\Hooks();
	}
);
