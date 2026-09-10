<?php

use Yoast\WPTestUtils\WPIntegration\TestCase;
use CLOSE\NutritionInfo\Allergens;

class AllergensTest extends TestCase {

	private Allergens $allergens;

	public function set_up() {
		parent::set_up();
		$this->allergens = new Allergens();
	}

	public function test_show_allergens_name_returns_24_keyed_labels() {
		$names = $this->allergens->show_allergens_name();

		$this->assertCount( 24, $names );
		$this->assertArrayHasKey( 'gluten', $names );
		$this->assertArrayHasKey( 'legumes', $names );
		$this->assertArrayNotHasKey( 'vegan', $names, 'Vegan is handled separately, not part of the regular allergen list.' );
	}

	/**
	 * @dataProvider provide_allergen_keys
	 */
	public function test_show_allergen_svg_loads_a_real_svg_for_every_key( $key ) {
		$svg = $this->allergens->show_allergen_svg( $key );

		$this->assertStringStartsWith( '<svg', trim( $svg ), "Allergen '$key' did not load a valid SVG." );
	}

	/**
	 * Regression guard: the frontend CSS rule that sizes these icons
	 * (`.niw_svg_allergen { width: 32px; height: 32px; }` in
	 * includes/assets/css/styles.css) targets a class baked into each SVG's
	 * own root element — it isn't added by PHP. A replacement icon file that
	 * drops this class renders at its raw intrinsic size instead (which has
	 * shown up as icons 100+ px tall on the product page).
	 *
	 * @dataProvider provide_allergen_keys
	 */
	public function test_every_allergen_svg_carries_the_sizing_css_class( $key ) {
		$svg = $this->allergens->show_allergen_svg( $key );

		$this->assertStringContainsString( 'class="niw_svg_allergen"', $svg, "Allergen '$key' SVG is missing the niw_svg_allergen sizing class." );
	}

	public function test_vegan_svg_carries_the_sizing_css_class() {
		$this->assertStringContainsString( 'class="niw_svg_allergen"', $this->allergens->show_allergen_svg_vegan() );
	}

	public function provide_allergen_keys() {
		$allergens = new Allergens();
		$cases     = array();
		foreach ( array_keys( $allergens->show_allergens_name() ) as $key ) {
			$cases[ $key ] = array( $key );
		}
		return $cases;
	}

	public function test_show_allergen_svg_returns_empty_string_for_unknown_key() {
		$this->assertSame( '', $this->allergens->show_allergen_svg( 'not_a_real_allergen' ) );
	}

	public function test_show_allergen_svg_is_safe_against_path_traversal() {
		// load_svg() runs the key through sanitize_file_name() before building the
		// path — confirm a traversal attempt can't escape includes/assets/allergens/.
		$svg = $this->allergens->show_allergen_svg( '../../../../etc/passwd' );

		$this->assertSame( '', $svg );
	}

	public function test_show_allergen_svg_vegan_loads_a_real_svg() {
		$svg = $this->allergens->show_allergen_svg_vegan();

		$this->assertStringStartsWith( '<svg', trim( $svg ) );
	}

	public function test_show_allergen_name_vegan_returns_vegan_key() {
		$this->assertSame( 'vegan', $this->allergens->show_allergen_name_vegan() );
	}

	public function test_show_allergens_svg_returns_one_entry_per_allergen() {
		$svgs = $this->allergens->show_allergens_svg();

		$this->assertCount( 24, $svgs );
		foreach ( $svgs as $svg ) {
			$this->assertStringStartsWith( '<svg', trim( $svg ) );
		}
	}
}
