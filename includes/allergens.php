<?php
/**
 * Class for Allergens
 *
 * @package    CLOSE\NutritionInfo
 * @author     David Perez <david@closemarketing.es>
 * @copyright  2021 Closemarketing
 * @version    1.0
 */

namespace CLOSE\NutritionInfo;

defined( 'ABSPATH' ) || exit;

/**
 * Allergens class.
 */
class Allergens {

	/**
	 * Path to the directory containing allergen SVG files.
	 *
	 * @var string
	 */
	private string $assets_dir;

	/**
	 * Allergen definitions: key and translatable label.
	 *
	 * @var array<int, array{key: string, label: string}>
	 */
	private array $allergens;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->assets_dir = NIW_PLUGIN_PATH . 'includes/assets/allergens/';

		$this->allergens = array(
			array(
				'key'   => 'alcohol',
				'label' => __( 'Alcohol', 'nutrition-info-woocommerce' ),
			),
			array(
				'key'   => 'almonds',
				'label' => __( 'Almonds', 'nutrition-info-woocommerce' ),
			),
			array(
				'key'   => 'lupins',
				'label' => __( 'Lupins', 'nutrition-info-woocommerce' ),
			),
			array(
				'key'   => 'celery',
				'label' => __( 'Celery', 'nutrition-info-woocommerce' ),
			),
			array(
				'key'   => 'sugar',
				'label' => __( 'Sugar', 'nutrition-info-woocommerce' ),
			),
			array(
				'key'   => 'peanuts',
				'label' => __( 'Peanuts', 'nutrition-info-woocommerce' ),
			),
			array(
				'key'   => 'spices',
				'label' => __( 'Spices', 'nutrition-info-woocommerce' ),
			),
			array(
				'key'   => 'gluten',
				'label' => __( 'Gluten', 'nutrition-info-woocommerce' ),
			),
			array(
				'key'   => 'egg',
				'label' => __( 'Egg', 'nutrition-info-woocommerce' ),
			),
			array(
				'key'   => 'milk',
				'label' => __( 'Milk', 'nutrition-info-woocommerce' ),
			),
			array(
				'key'   => 'corn',
				'label' => __( 'Corn', 'nutrition-info-woocommerce' ),
			),
			array(
				'key'   => 'crustaceans',
				'label' => __( 'Crustaceans', 'nutrition-info-woocommerce' ),
			),
			array(
				'key'   => 'honey',
				'label' => __( 'Honey', 'nutrition-info-woocommerce' ),
			),
			array(
				'key'   => 'mollusks',
				'label' => __( 'Mollusks', 'nutrition-info-woocommerce' ),
			),
			array(
				'key'   => 'mustard',
				'label' => __( 'Mustard', 'nutrition-info-woocommerce' ),
			),
			array(
				'key'   => 'organic',
				'label' => __( 'Organic', 'nutrition-info-woocommerce' ),
			),
			array(
				'key'   => 'fish',
				'label' => __( 'Fish', 'nutrition-info-woocommerce' ),
			),
			array(
				'key'   => 'sesame',
				'label' => __( 'Sesame', 'nutrition-info-woocommerce' ),
			),
			array(
				'key'   => 'mushrooms',
				'label' => __( 'Mushrooms', 'nutrition-info-woocommerce' ),
			),
			array(
				'key'   => 'soy',
				'label' => __( 'Soy', 'nutrition-info-woocommerce' ),
			),
			array(
				'key'   => 'sulfates',
				'label' => __( 'Sulfates', 'nutrition-info-woocommerce' ),
			),
			array(
				'key'   => 'vegetables',
				'label' => __( 'Vegetables', 'nutrition-info-woocommerce' ),
			),
			array(
				'key'   => 'nuts',
				'label' => __( 'Nuts', 'nutrition-info-woocommerce' ),
			),
		);
	}

	/**
	 * Returns allergen keys mapped to their translatable labels.
	 *
	 * @return array<string, string>
	 */
	public function show_allergens_name(): array {
		$allergens_name = array();

		foreach ( $this->allergens as $allergen ) {
			$allergens_name[ $allergen['key'] ] = $allergen['label'];
		}

		return $allergens_name;
	}

	/**
	 * Returns all allergen SVGs as a flat list.
	 *
	 * @return array<int, string>
	 */
	public function show_allergens_svg(): array {
		$allergens_svg = array();

		foreach ( $this->allergens as $allergen ) {
			$allergens_svg[] = $this->load_svg( $allergen['key'] );
		}

		return $allergens_svg;
	}

	/**
	 * Returns the SVG markup for a single allergen by key.
	 *
	 * @param string $name Allergen key.
	 * @return string
	 */
	public function show_allergen_svg( string $name ): string {
		return $this->load_svg( $name );
	}

	/**
	 * Returns the vegan SVG markup.
	 *
	 * @return string
	 */
	public function show_allergen_svg_vegan(): string {
		return $this->load_svg( 'vegan' );
	}

	/**
	 * Returns the vegan allergen key.
	 *
	 * @return string
	 */
	public function show_allergen_name_vegan(): string {
		return 'vegan';
	}

	/**
	 * Reads an SVG file from the assets directory.
	 *
	 * @param string $key Allergen key (filename without extension).
	 * @return string SVG markup or empty string if file not found.
	 */
	private function load_svg( string $key ): string {
		$path = $this->assets_dir . sanitize_file_name( $key ) . '.svg';

		if ( ! file_exists( $path ) ) {
			return '';
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		return (string) file_get_contents( $path );
	}
}
