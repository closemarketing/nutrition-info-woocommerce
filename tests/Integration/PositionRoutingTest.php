<?php

use Yoast\WPTestUtils\WPIntegration\TestCase;
use CLOSE\NutritionInfo\Hooks;

/**
 * Hooks::register_position_hooks() only runs once, in the constructor — it
 * reads the position option at instantiation time. In real WordPress that
 * means a settings change takes effect on the next request (a fresh Hooks
 * instance), which is what these tests simulate by constructing a new Hooks()
 * after changing the option, rather than relying on the instance already
 * bootstrapped for the test run.
 */
class PositionRoutingTest extends TestCase {

	public function tear_down() {
		delete_option( 'wc_nutrients_settings_tab_position' );
		parent::tear_down();
	}

	/**
	 * @dataProvider provide_positions_with_priority
	 */
	public function test_position_hooks_nutrition_info_onto_single_product_summary( $position, $priority ) {
		update_option( 'wc_nutrients_settings_tab_position', $position );
		new Hooks();

		$this->assertSame( $priority, has_action( 'woocommerce_single_product_summary', 'CLOSE\NutritionInfo\niw_nutrition_info' ) );
		$this->assertSame( $priority, has_action( 'woocommerce_single_product_summary', 'CLOSE\NutritionInfo\niw_composition_info' ) );
	}

	public function provide_positions_with_priority() {
		return array(
			'after price'          => array( 'after_price', 15 ),
			'after excerpt'        => array( 'after_excerpt', 25 ),
			'after add to cart'    => array( 'after_add_to_cart', 35 ),
		);
	}

	public function test_in_description_tab_position_overrides_the_description_tab_instead() {
		update_option( 'wc_nutrients_settings_tab_position', 'in_description_tab' );
		$hooks = new Hooks();

		$this->assertSame( 98, has_filter( 'woocommerce_product_tabs', array( $hooks, 'override_description_tab' ) ) );
		$this->assertFalse( has_action( 'woocommerce_single_product_summary', 'CLOSE\NutritionInfo\niw_nutrition_info' ) );
	}

	public function test_hidden_position_registers_no_automatic_output() {
		update_option( 'wc_nutrients_settings_tab_position', 'hidden' );
		new Hooks();

		$this->assertFalse( has_action( 'woocommerce_single_product_summary', 'CLOSE\NutritionInfo\niw_nutrition_info' ) );
		$this->assertFalse( has_filter( 'woocommerce_product_tabs', 'CLOSE\NutritionInfo\Hooks::override_description_tab' ) );
	}

	public function test_after_meta_is_a_dead_option_value_with_no_matching_hook() {
		// "After product metadata" is offered in the settings dropdown but no
		// hook was ever registered for it — selecting it silently shows nothing.
		update_option( 'wc_nutrients_settings_tab_position', 'after_meta' );
		new Hooks();

		$this->assertFalse( has_action( 'woocommerce_single_product_summary', 'CLOSE\NutritionInfo\niw_nutrition_info' ) );
	}
}
