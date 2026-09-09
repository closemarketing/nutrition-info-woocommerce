<?php

use Yoast\WPTestUtils\WPIntegration\TestCase;
use CLOSE\NutritionInfo\Hooks;
use CLOSE\NutritionInfo\Allergens;

class HooksAllergenIconsTest extends TestCase {

	private $product_id;
	private Hooks $hooks;

	public function set_up() {
		parent::set_up();
		$product = new WC_Product_Simple();
		$product->set_name( 'NIW Icons Test Product' );
		$product->set_regular_price( '5.00' );
		$product->save();
		$this->product_id = $product->get_id();

		global $post;
		$post = get_post( $this->product_id );
		setup_postdata( $post );

		$this->hooks = new Hooks();
	}

	public function tear_down() {
		wp_reset_postdata();
		parent::tear_down();
	}

	private function allowed_tags_svg(): array {
		$refl = new ReflectionClass( $this->hooks );
		$prop = $refl->getProperty( 'allowed_tags_svg' );
		$prop->setAccessible( true );
		return $prop->getValue( $this->hooks );
	}

	public function test_single_product_renders_no_icons_when_no_allergens_are_flagged() {
		ob_start();
		$this->hooks->add_allergens_icon_single_product();
		$html = ob_get_clean();

		$this->assertStringNotContainsString( '<svg', $html );
	}

	public function test_single_product_renders_flagged_allergens_only() {
		update_post_meta( $this->product_id, 'niw_all_gluten', 'yes' );
		update_post_meta( $this->product_id, 'niw_all_milk', 'yes' );

		ob_start();
		$this->hooks->add_allergens_icon_single_product();
		$html = ob_get_clean();

		$this->assertSame( 2, substr_count( $html, '<svg' ) );
		$this->assertStringContainsString( 'Gluten', $html );
		$this->assertStringContainsString( 'Milk', $html );
		$this->assertStringNotContainsString( 'Fish', $html );
	}

	public function test_vegan_icon_renders_only_when_flagged() {
		ob_start();
		$this->hooks->add_special_allergens_icon_single_product();
		$this->assertStringNotContainsString( '<svg', ob_get_clean() );

		update_post_meta( $this->product_id, 'niw_all_vegan', 'yes' );

		ob_start();
		$this->hooks->add_special_allergens_icon_single_product();
		$html = ob_get_clean();

		$this->assertStringContainsString( '<svg', $html );
		$this->assertStringContainsString( 'Vegan', $html );
	}

	public function test_shop_loop_icon_matches_single_product_output() {
		update_post_meta( $this->product_id, 'niw_all_fish', 'yes' );

		ob_start();
		$this->hooks->add_allergens_icon();
		$html = ob_get_clean();

		$this->assertStringContainsString( 'Fish', $html );
		$this->assertSame( 1, substr_count( $html, '<svg' ) );
	}

	/**
	 * Regression guard: if a new allergen SVG ever used markup outside the
	 * frontend's wp_kses() allowlist (e.g. gradients), the icon would render
	 * broken in production even though the raw file is a valid SVG.
	 */
	public function test_every_allergen_svg_survives_wp_kses_without_losing_tags() {
		$allergens = new Allergens();
		$allowed   = $this->allowed_tags_svg();

		foreach ( array_keys( $allergens->show_allergens_name() ) as $key ) {
			$raw      = $allergens->show_allergen_svg( $key );
			$filtered = wp_kses( $raw, $allowed );

			preg_match_all( '/<([a-zA-Z][a-zA-Z0-9]*)/', $raw, $raw_tags );
			preg_match_all( '/<([a-zA-Z][a-zA-Z0-9]*)/', $filtered, $filtered_tags );

			$this->assertSame( $raw_tags[1], $filtered_tags[1], "wp_kses stripped tags from the '$key' allergen SVG." );
		}

		$vegan_raw      = $allergens->show_allergen_svg_vegan();
		$vegan_filtered = wp_kses( $vegan_raw, $allowed );
		preg_match_all( '/<([a-zA-Z][a-zA-Z0-9]*)/', $vegan_raw, $raw_tags );
		preg_match_all( '/<([a-zA-Z][a-zA-Z0-9]*)/', $vegan_filtered, $filtered_tags );
		$this->assertSame( $raw_tags[1], $filtered_tags[1], 'wp_kses stripped tags from the vegan SVG.' );
	}
}
