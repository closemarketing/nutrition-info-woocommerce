<?php

use Yoast\WPTestUtils\WPIntegration\TestCase;
use function CLOSE\NutritionInfo\niw_nutrition_info;
use function CLOSE\NutritionInfo\niw_composition_info;

class TemplateFunctionsTest extends TestCase {

	private $product_id;

	public function set_up() {
		parent::set_up();
		$product = new WC_Product_Simple();
		$product->set_name( 'NIW Template Test Product' );
		$product->set_regular_price( '5.00' );
		$product->save();
		$this->product_id = $product->get_id();

		global $post;
		$post = get_post( $this->product_id );
		setup_postdata( $post );
	}

	public function tear_down() {
		wp_reset_postdata();
		parent::tear_down();
	}

	private function tbody_content( string $html ): string {
		preg_match( '/<tbody>(.*?)<\/tbody>/s', $html, $matches );
		return $matches[1] ?? '';
	}

	public function test_nutrition_info_has_an_empty_body_when_no_field_is_filled() {
		ob_start();
		niw_nutrition_info();
		$html = ob_get_clean();

		$this->assertStringContainsString( '<table id="nutrition-table">', $html );
		$this->assertStringNotContainsString( '<tr>', $this->tbody_content( $html ), 'No nutrient rows should render when every value is empty.' );
	}

	public function test_nutrition_info_shows_only_the_filled_fields_with_their_unit() {
		update_post_meta( $this->product_id, 'niw_energy', '245.5' );
		update_post_meta( $this->product_id, 'niw_fiber', '3' );

		ob_start();
		niw_nutrition_info();
		$html = ob_get_clean();

		$this->assertSame( 2, substr_count( $this->tbody_content( $html ), '<tr>' ) );
		$this->assertStringContainsString( '245.5 kcal', $html );
		$this->assertStringContainsString( '3 g', $html );
		$this->assertStringNotContainsString( 'Protein', $html );
	}

	public function test_vitamin_mineral_is_free_text_with_no_unit_appended() {
		update_post_meta( $this->product_id, 'niw_vitamin_mineral', 'Vit C: 12mg' );

		ob_start();
		niw_nutrition_info();
		$html = ob_get_clean();

		$this->assertStringContainsString( '<td class="nutrition-table nutrition-table_nutrient-amount">Vit C: 12mg</td>', $html );
	}

	public function test_sub_nutrient_label_is_prefixed_with_a_dash() {
		update_post_meta( $this->product_id, 'niw_saturated_fat', '1.2' );

		ob_start();
		niw_nutrition_info();
		$html = ob_get_clean();

		$this->assertStringContainsString( '- Saturated fat', $html );
	}

	public function test_composition_info_wrapper_always_renders_even_when_empty() {
		ob_start();
		niw_composition_info();
		$html = ob_get_clean();

		$this->assertStringContainsString( 'niw_additional_information', $html );
		$this->assertStringNotContainsString( 'nutrition-table_nutrient-name">Ingredients<', $html );
	}

	public function test_composition_info_shows_ingredients_when_present() {
		update_post_meta( $this->product_id, 'niw_ingredients', 'Harina, huevo, leche' );

		ob_start();
		niw_composition_info();
		$html = ob_get_clean();

		$this->assertStringContainsString( 'Harina, huevo, leche', $html );
	}
}
