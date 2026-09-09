<?php
/**
 * PHPUnit bootstrap file for nutrition-info-woocommerce.
 */

define( 'TESTS_PLUGIN_DIR', dirname( __DIR__ ) );

// Define WP_CORE_DIR if not already defined.
if ( ! defined( 'WP_CORE_DIR' ) ) {
	$_wp_core_dir = getenv( 'WP_CORE_DIR' );
	if ( ! $_wp_core_dir ) {
		$_wp_core_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress';
	}
	define( 'WP_CORE_DIR', $_wp_core_dir );
}

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL;
	exit( 1 );
}

require_once $_tests_dir . '/includes/functions.php';

/**
 * Load WooCommerce, then this plugin, before WP bootstraps fully.
 */
function niw_manually_load_plugin() {
	require_once WP_CORE_DIR . '/wp-content/plugins/woocommerce/woocommerce.php';
	require_once TESTS_PLUGIN_DIR . '/nutrition-info-woocommerce.php';
}
tests_add_filter( 'muplugins_loaded', 'niw_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
