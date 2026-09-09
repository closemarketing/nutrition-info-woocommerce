<?php

use Yoast\WPTestUtils\WPIntegration\TestCase;

/**
 * Exercises the real save path: firing the actual 'woocommerce_process_product_meta'
 * hook (the same hook WooCommerce core fires on product save), not calling the
 * save method directly, so a wiring mistake would be caught here too.
 */
class MetaProductsSaveTest extends TestCase {

	private $product_id;
	private $original_post;

	public function set_up() {
		parent::set_up();
		$product          = new WC_Product_Simple();
		$product->set_name( 'NIW Save Test Product' );
		$product->set_regular_price( '5.00' );
		$product->save();
		$this->product_id = $product->get_id();

		$this->original_post = $_POST;
	}

	public function tear_down() {
		$_POST = $this->original_post;
		parent::tear_down();
	}

	public function test_save_without_nonce_persists_nothing() {
		$_POST = array( 'niw_energy' => '999' );

		do_action( 'woocommerce_process_product_meta', $this->product_id );

		$this->assertSame( '', get_post_meta( $this->product_id, 'niw_energy', true ) );
	}

	public function test_save_with_valid_nonce_persists_numeric_fields() {
		$_POST = array(
			'woocommerce_meta_nonce' => wp_create_nonce( 'woocommerce_save_data' ),
			'niw_energy'             => '245.5',
			'niw_carb'               => '12.345',
		);

		do_action( 'woocommerce_process_product_meta', $this->product_id );

		$this->assertSame( '245.5', get_post_meta( $this->product_id, 'niw_energy', true ) );
		$this->assertSame( '12.345', get_post_meta( $this->product_id, 'niw_carb', true ) );
	}

	public function test_negative_number_is_stored_as_is_no_server_side_clamp() {
		// The admin field has min="0", but that is client-side only — the save
		// method never clamps or rejects a negative value server-side.
		$_POST = array(
			'woocommerce_meta_nonce' => wp_create_nonce( 'woocommerce_save_data' ),
			'niw_fat'                => '-3',
		);

		do_action( 'woocommerce_process_product_meta', $this->product_id );

		$this->assertSame( '-3', get_post_meta( $this->product_id, 'niw_fat', true ) );
	}

	public function test_non_numeric_junk_is_stored_as_empty_string() {
		$_POST = array(
			'woocommerce_meta_nonce'  => wp_create_nonce( 'woocommerce_save_data' ),
			'niw_saturated_fat'       => 'abc',
		);

		do_action( 'woocommerce_process_product_meta', $this->product_id );

		$this->assertSame( '', get_post_meta( $this->product_id, 'niw_saturated_fat', true ) );
	}

	public function test_vitamin_mineral_is_free_text_and_keeps_its_value_verbatim() {
		$_POST = array(
			'woocommerce_meta_nonce' => wp_create_nonce( 'woocommerce_save_data' ),
			'niw_vitamin_mineral'    => 'Vit C: 12mg',
		);

		do_action( 'woocommerce_process_product_meta', $this->product_id );

		$this->assertSame( 'Vit C: 12mg', get_post_meta( $this->product_id, 'niw_vitamin_mineral', true ) );
	}

	public function test_html_and_script_tags_are_stripped_from_free_text_fields() {
		$_POST = array(
			'woocommerce_meta_nonce' => wp_create_nonce( 'woocommerce_save_data' ),
			'niw_vitamin_mineral'    => 'Vit C <script>alert(1)</script>',
			'niw_ingredients'        => 'Harina, <b>leche</b>, azúcar',
		);

		do_action( 'woocommerce_process_product_meta', $this->product_id );

		$vitamin = get_post_meta( $this->product_id, 'niw_vitamin_mineral', true );
		$this->assertStringNotContainsString( '<script>', $vitamin );
		$this->assertStringNotContainsString( 'alert(1)', $vitamin );
		$this->assertSame( 'Harina, leche, azúcar', get_post_meta( $this->product_id, 'niw_ingredients', true ) );
	}

	public function test_unicode_and_emoji_survive_sanitization() {
		$_POST = array(
			'woocommerce_meta_nonce' => wp_create_nonce( 'woocommerce_save_data' ),
			'niw_ingredients'        => 'Huevo 🥚, azúcar — 100% natural',
		);

		do_action( 'woocommerce_process_product_meta', $this->product_id );

		$this->assertSame( 'Huevo 🥚, azúcar — 100% natural', get_post_meta( $this->product_id, 'niw_ingredients', true ) );
	}

	public function test_checked_allergens_are_saved_as_yes_and_unchecked_as_empty() {
		$_POST = array(
			'woocommerce_meta_nonce' => wp_create_nonce( 'woocommerce_save_data' ),
			'niw_all_gluten'         => 'yes',
			'niw_all_milk'           => 'yes',
		);

		do_action( 'woocommerce_process_product_meta', $this->product_id );

		$this->assertSame( 'yes', get_post_meta( $this->product_id, 'niw_all_gluten', true ) );
		$this->assertSame( 'yes', get_post_meta( $this->product_id, 'niw_all_milk', true ) );
		$this->assertSame( '', get_post_meta( $this->product_id, 'niw_all_fish', true ) );
	}

	public function test_derived_allergen_summary_fields_are_computed() {
		$_POST = array(
			'woocommerce_meta_nonce' => wp_create_nonce( 'woocommerce_save_data' ),
			'niw_all_gluten'         => 'yes',
			'niw_all_milk'           => 'yes',
		);

		do_action( 'woocommerce_process_product_meta', $this->product_id );

		$names = get_post_meta( $this->product_id, 'niw_all_allergens_names', true );
		$not   = get_post_meta( $this->product_id, 'niw_all_allergens_not', true );

		$this->assertStringContainsString( 'Gluten', $names );
		$this->assertStringContainsString( 'Milk', $names );
		$this->assertStringNotContainsString( 'Fish', $names );
		$this->assertStringContainsString( 'Without Fish', $not );
		$this->assertStringNotContainsString( 'Without Gluten', $not );
	}

	public function test_vegan_checkbox_saves_independently_of_the_allergen_list() {
		$_POST = array(
			'woocommerce_meta_nonce' => wp_create_nonce( 'woocommerce_save_data' ),
			'niw_all_vegan'          => 'yes',
		);

		do_action( 'woocommerce_process_product_meta', $this->product_id );

		$this->assertSame( 'yes', get_post_meta( $this->product_id, 'niw_all_vegan', true ) );
		// Vegan must not appear in the regular allergens summary — it's tracked separately.
		$this->assertStringNotContainsString( 'Vegan', get_post_meta( $this->product_id, 'niw_all_allergens_names', true ) );
	}
}
