<?php

use Yoast\WPTestUtils\WPIntegration\TestCase;
use CLOSE\NutritionInfo\WooSettings;

class WooSettingsTest extends TestCase {

	public function test_settings_tab_is_registered() {
		$tabs = apply_filters( 'woocommerce_settings_tabs_array', array() );

		$this->assertArrayHasKey( 'nutrients_settings_tab', $tabs );
	}

	public function test_get_settings_exposes_all_expected_fields() {
		$settings = WooSettings::get_settings();
		$ids      = wp_list_pluck( $settings, 'id' );

		$this->assertContains( 'wc_nutrients_settings_tab_title', $ids );
		$this->assertContains( 'wc_nutrients_settings_tab_per_volume_text', $ids );
		$this->assertContains( 'wc_nutrients_settings_tab_position', $ids );
		$this->assertContains( 'wc_nutrients_settings_tab_styling', $ids );
	}

	public function test_position_field_offers_every_documented_option() {
		$settings = WooSettings::get_settings();
		$position = null;
		foreach ( $settings as $field ) {
			if ( isset( $field['id'] ) && 'wc_nutrients_settings_tab_position' === $field['id'] ) {
				$position = $field;
			}
		}

		$this->assertNotNull( $position );
		$this->assertSame(
			array( 'tab', 'in_description_tab', 'after_price', 'after_excerpt', 'after_add_to_cart', 'after_meta', 'hidden' ),
			array_keys( $position['options'] )
		);
	}

	public function test_get_settings_is_filterable() {
		add_filter( 'wc_nutrients_settings_tab_settings', function ( $settings ) {
			$settings['custom_test_field'] = array( 'id' => 'niw_custom_test_field' );
			return $settings;
		} );

		$ids = wp_list_pluck( WooSettings::get_settings(), 'id' );

		$this->assertContains( 'niw_custom_test_field', $ids );

		remove_all_filters( 'wc_nutrients_settings_tab_settings' );
	}
}
