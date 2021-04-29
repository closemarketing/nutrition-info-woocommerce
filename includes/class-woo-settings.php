<?php
/**
 * Class Settings
 *
 * @package    WordPress
 * @author     David Pérez <david@closemarketing.es>
 * @copyright  2021 Closemarketing
 * @version    1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Settings
 */
class WC_Nutrients_Settings_Tab {
	/**
	 * Bootstraps the class and hooks required actions & filters.
	 *
	 **/
	public static function init() {
		add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
		add_action( 'woocommerce_settings_tabs_nutrients_settings_tab', __CLASS__ . '::settings_tab' );
		add_action( 'woocommerce_update_options_nutrients_settings_tab', __CLASS__ . '::update_settings' );
	}


	/**
	 * Add a new settings tab to the WooCommerce settings tabs array.
	 *
	 * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
	 * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
	 */
	public static function add_settings_tab( $settings_tabs ) {
		$settings_tabs['nutrients_settings_tab'] = __( 'Nutrients', 'nutrition-info-woocommerce' );
		return $settings_tabs;
	}
	/**
	 * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
	 *
	 * @uses woocommerce_admin_fields()
	 * @uses self::get_settings()
	 */
	public static function settings_tab() {
		woocommerce_admin_fields( self::get_settings() );
	}
	/**
	 * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
	 *
	 * @uses woocommerce_update_options()
	 * @uses self::get_settings()
	 */
	public static function update_settings() {
		woocommerce_update_options( self::get_settings() );
	}
	/**
	 * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
	 *
	 * @return array Array of settings for @see woocommerce_admin_fields() function.
	 */
	public static function get_settings() {
		$settings = array(
			'section_title' => array(
			'name'     => __( 'Nutrients Settings', 'nutrition-info-woocommerce' ),
			'type'     => 'title',
			'desc'     => '',
			'id'       => 'wc_nutrients_settings_tab_section_title'
			),
			'title' => array(
			'name' => __( 'Title', 'nutrition-info-woocommerce' ),
			'type' => 'text',
			'desc' => __( 'This is some helper text', 'nutrition-info-woocommerce' ),
			'id'   => 'wc_nutrients_settings_tab_title',
			'default' => __('Nutritional Information', 'nutrition-info-woocommerce')
			),
			'per_volume_text' => array(
			'name' => __( 'Per volume text', 'nutrition-info-woocommerce' ),
			'type' => 'text',
			'desc' => __( 'This is some helper text', 'nutrition-info-woocommerce' ),
			'id'   => 'wc_nutrients_settings_tab_per_volume_text',
			'default' => __('Content pr. 100 g', 'nutrition-info-woocommerce')
			),
			'position' => array(
			'name' => __( 'Position', 'nutrition-info-woocommerce' ),
			'type' => 'select',
			'desc' => __( 'Where to display the tabel.', 'nutrition-info-woocommerce' ),
			'id'   => 'wc_nutrients_settings_tab_position',
			'options' => array(
				'tab' => __('Seperate tab', 'nutrition-info-woocommerce'),
				'in_description_tab' => __('In description tab', 'nutrition-info-woocommerce'),
				'after_price' => __('After price', 'nutrition-info-woocommerce'),
				'after_excerpt' => __('After excerpt', 'nutrition-info-woocommerce'),
				'after_add_to_cart' => __('After "Add to Cart" button', 'nutrition-info-woocommerce'),
				'after_meta' => __('After product metadata', 'nutrition-info-woocommerce'),
				'hidden' => __('Manual placement (hidden)', 'nutrition-info-woocommerce')
			)
			),
			'styling' => array(
			'name' => __( 'Styling', 'nutrition-info-woocommerce' ),
			'type' => 'checkbox',
			'desc' => __( 'Include stylesheet.', 'nutrition-info-woocommerce' ),
			'id'   => 'wc_nutrients_settings_tab_styling',
			'default' => 'yes'
			),
			'section_end' => array(
			'type' => 'sectionend',
			'id' => 'wc_nutrients_settings_tab_section_end'
			)
		);
		return apply_filters( 'wc_nutrients_settings_tab_settings', $settings );
	}
}
WC_Nutrients_Settings_Tab::init(); ?>
