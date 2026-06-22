<?php
/**
 * NIW Data
 *
 * Allergen list and nutrient definitions — self-contained, no external dependencies.
 *
 * @package nutrition-info-woocommerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * Allergen and nutrient data definitions.
 */
class NIW_Data {

	/**
	 * Returns the 14 EU allergens with their translation keys.
	 *
	 * Meta keys: food_allegerns_{key}  (value 1 = present, 0 = absent)
	 *
	 * @return array<string,string>
	 */
	public static function get_allergens() {
		return array(
			'gluten'     => __( 'Gluten', 'nutrition-info-woocommerce' ),
			'crustacean' => __( 'Crustacean', 'nutrition-info-woocommerce' ),
			'egg'        => __( 'Egg', 'nutrition-info-woocommerce' ),
			'fish'       => __( 'Fish', 'nutrition-info-woocommerce' ),
			'peanut'     => __( 'Peanut', 'nutrition-info-woocommerce' ),
			'soy'        => __( 'Soy', 'nutrition-info-woocommerce' ),
			'dairy'      => __( 'Dairy', 'nutrition-info-woocommerce' ),
			'shell'      => __( 'Shell', 'nutrition-info-woocommerce' ),
			'celery'     => __( 'Celery', 'nutrition-info-woocommerce' ),
			'mustard'    => __( 'Mustard', 'nutrition-info-woocommerce' ),
			'sesame'     => __( 'Sesame', 'nutrition-info-woocommerce' ),
			'sulfite'    => __( 'Sulfite', 'nutrition-info-woocommerce' ),
			'mollusk'    => __( 'Mollusk', 'nutrition-info-woocommerce' ),
			'lupins'     => __( 'Lupins', 'nutrition-info-woocommerce' ),
		);
	}

	/**
	 * Returns the full 48-field nutrient table definition.
	 *
	 * Meta keys: food_nutrient_{key}  /  food_nutrient_serving
	 *
	 * @return array
	 */
	public static function get_nutrients() {
		return array(
			// Energy.
			'energy_kcal'       => array(
				'label' => __( 'Energy', 'nutrition-info-woocommerce' ),
				'unit'  => 'kcal',
				'group' => 'energy',
			),
			'energy_kj'         => array(
				'label' => __( 'Energy', 'nutrition-info-woocommerce' ),
				'unit'  => 'kJ',
				'group' => 'energy',
			),
			// Fat.
			'fat'               => array(
				'label' => __( 'Fat', 'nutrition-info-woocommerce' ),
				'unit'  => 'g',
				'group' => 'fat',
			),
			'fat_saturated'     => array(
				'label' => __( 'Saturated fatty acids', 'nutrition-info-woocommerce' ),
				'unit'  => 'g',
				'group' => 'fat',
				'sub'   => true,
			),
			'fat_monounsat'     => array(
				'label' => __( 'Monounsaturated fatty acids', 'nutrition-info-woocommerce' ),
				'unit'  => 'g',
				'group' => 'fat',
				'sub'   => true,
			),
			'fat_polyunsat'     => array(
				'label' => __( 'Polyunsaturated fatty acids', 'nutrition-info-woocommerce' ),
				'unit'  => 'g',
				'group' => 'fat',
				'sub'   => true,
			),
			'fat_trans'         => array(
				'label' => __( 'Trans fatty acids', 'nutrition-info-woocommerce' ),
				'unit'  => 'g',
				'group' => 'fat',
				'sub'   => true,
			),
			'cholesterol'       => array(
				'label' => __( 'Cholesterol', 'nutrition-info-woocommerce' ),
				'unit'  => 'mg',
				'group' => 'fat',
			),
			// Carbohydrates.
			'carbs'             => array(
				'label' => __( 'Carbohydrate', 'nutrition-info-woocommerce' ),
				'unit'  => 'g',
				'group' => 'carbs',
			),
			'carbs_sugar'       => array(
				'label' => __( 'Sugars', 'nutrition-info-woocommerce' ),
				'unit'  => 'g',
				'group' => 'carbs',
				'sub'   => true,
			),
			'carbs_sugar_added' => array(
				'label' => __( 'Added sugars', 'nutrition-info-woocommerce' ),
				'unit'  => 'g',
				'group' => 'carbs',
				'sub'   => true,
			),
			'carbs_polyols'     => array(
				'label' => __( 'Polyols', 'nutrition-info-woocommerce' ),
				'unit'  => 'g',
				'group' => 'carbs',
				'sub'   => true,
			),
			'carbs_starch'      => array(
				'label' => __( 'Starch', 'nutrition-info-woocommerce' ),
				'unit'  => 'g',
				'group' => 'carbs',
				'sub'   => true,
			),
			'fiber'             => array(
				'label' => __( 'Dietary fiber', 'nutrition-info-woocommerce' ),
				'unit'  => 'g',
				'group' => 'carbs',
			),
			'fiber_soluble'     => array(
				'label' => __( 'Soluble fiber', 'nutrition-info-woocommerce' ),
				'unit'  => 'g',
				'group' => 'carbs',
				'sub'   => true,
			),
			'fiber_insoluble'   => array(
				'label' => __( 'Insoluble fiber', 'nutrition-info-woocommerce' ),
				'unit'  => 'g',
				'group' => 'carbs',
				'sub'   => true,
			),
			// Protein and salt.
			'protein'           => array(
				'label' => __( 'Protein', 'nutrition-info-woocommerce' ),
				'unit'  => 'g',
				'group' => 'protein',
			),
			'salt'              => array(
				'label' => __( 'Salt', 'nutrition-info-woocommerce' ),
				'unit'  => 'g',
				'group' => 'protein',
			),
			'sodium'            => array(
				'label' => __( 'Sodium', 'nutrition-info-woocommerce' ),
				'unit'  => 'mg',
				'group' => 'protein',
				'sub'   => true,
			),
			// Vitamins.
			'vit_a'             => array(
				'label' => __( 'Vitamin A', 'nutrition-info-woocommerce' ),
				'unit'  => 'μg',
				'group' => 'vitamins',
			),
			'vit_b1'            => array(
				'label' => __( 'Thiamine (B1)', 'nutrition-info-woocommerce' ),
				'unit'  => 'mg',
				'group' => 'vitamins',
			),
			'vit_b2'            => array(
				'label' => __( 'Riboflavin (B2)', 'nutrition-info-woocommerce' ),
				'unit'  => 'mg',
				'group' => 'vitamins',
			),
			'vit_b3'            => array(
				'label' => __( 'Niacin (B3)', 'nutrition-info-woocommerce' ),
				'unit'  => 'mg',
				'group' => 'vitamins',
			),
			'vit_b5'            => array(
				'label' => __( 'Pantothenic acid (B5)', 'nutrition-info-woocommerce' ),
				'unit'  => 'mg',
				'group' => 'vitamins',
			),
			'vit_b6'            => array(
				'label' => __( 'Vitamin B6', 'nutrition-info-woocommerce' ),
				'unit'  => 'mg',
				'group' => 'vitamins',
			),
			'vit_b7'            => array(
				'label' => __( 'Biotin (B7)', 'nutrition-info-woocommerce' ),
				'unit'  => 'μg',
				'group' => 'vitamins',
			),
			'vit_b9'            => array(
				'label' => __( 'Folic acid (B9)', 'nutrition-info-woocommerce' ),
				'unit'  => 'μg',
				'group' => 'vitamins',
			),
			'vit_b12'           => array(
				'label' => __( 'Vitamin B12', 'nutrition-info-woocommerce' ),
				'unit'  => 'μg',
				'group' => 'vitamins',
			),
			'vit_c'             => array(
				'label' => __( 'Vitamin C', 'nutrition-info-woocommerce' ),
				'unit'  => 'mg',
				'group' => 'vitamins',
			),
			'vit_d'             => array(
				'label' => __( 'Vitamin D', 'nutrition-info-woocommerce' ),
				'unit'  => 'μg',
				'group' => 'vitamins',
			),
			'vit_e'             => array(
				'label' => __( 'Vitamin E', 'nutrition-info-woocommerce' ),
				'unit'  => 'mg',
				'group' => 'vitamins',
			),
			'vit_k'             => array(
				'label' => __( 'Vitamin K', 'nutrition-info-woocommerce' ),
				'unit'  => 'μg',
				'group' => 'vitamins',
			),
			// Minerals.
			'min_calcium'       => array(
				'label' => __( 'Calcium', 'nutrition-info-woocommerce' ),
				'unit'  => 'mg',
				'group' => 'minerals',
			),
			'min_phosphorus'    => array(
				'label' => __( 'Phosphorus', 'nutrition-info-woocommerce' ),
				'unit'  => 'mg',
				'group' => 'minerals',
			),
			'min_iron'          => array(
				'label' => __( 'Iron', 'nutrition-info-woocommerce' ),
				'unit'  => 'mg',
				'group' => 'minerals',
			),
			'min_magnesium'     => array(
				'label' => __( 'Magnesium', 'nutrition-info-woocommerce' ),
				'unit'  => 'mg',
				'group' => 'minerals',
			),
			'min_zinc'          => array(
				'label' => __( 'Zinc', 'nutrition-info-woocommerce' ),
				'unit'  => 'mg',
				'group' => 'minerals',
			),
			'min_iodine'        => array(
				'label' => __( 'Iodine', 'nutrition-info-woocommerce' ),
				'unit'  => 'μg',
				'group' => 'minerals',
			),
			'min_selenium'      => array(
				'label' => __( 'Selenium', 'nutrition-info-woocommerce' ),
				'unit'  => 'μg',
				'group' => 'minerals',
			),
			'min_copper'        => array(
				'label' => __( 'Copper', 'nutrition-info-woocommerce' ),
				'unit'  => 'mg',
				'group' => 'minerals',
			),
			'min_manganese'     => array(
				'label' => __( 'Manganese', 'nutrition-info-woocommerce' ),
				'unit'  => 'mg',
				'group' => 'minerals',
			),
			'min_chromium'      => array(
				'label' => __( 'Chromium', 'nutrition-info-woocommerce' ),
				'unit'  => 'μg',
				'group' => 'minerals',
			),
			'min_molybdenum'    => array(
				'label' => __( 'Molybdenum', 'nutrition-info-woocommerce' ),
				'unit'  => 'μg',
				'group' => 'minerals',
			),
			'min_potassium'     => array(
				'label' => __( 'Potassium', 'nutrition-info-woocommerce' ),
				'unit'  => 'mg',
				'group' => 'minerals',
			),
			'min_fluoride'      => array(
				'label' => __( 'Fluoride', 'nutrition-info-woocommerce' ),
				'unit'  => 'mg',
				'group' => 'minerals',
			),
			'min_chloride'      => array(
				'label' => __( 'Chloride', 'nutrition-info-woocommerce' ),
				'unit'  => 'mg',
				'group' => 'minerals',
			),
		);
	}

	/** Group labels for the nutrient table. */
	public static function get_group_labels() {
		return array(
			'energy'   => __( 'Energy', 'nutrition-info-woocommerce' ),
			'fat'      => __( 'Fat', 'nutrition-info-woocommerce' ),
			'carbs'    => __( 'Carbohydrate', 'nutrition-info-woocommerce' ),
			'protein'  => __( 'Protein and salt', 'nutrition-info-woocommerce' ),
			'vitamins' => __( 'Vitamins', 'nutrition-info-woocommerce' ),
			'minerals' => __( 'Minerals', 'nutrition-info-woocommerce' ),
		);
	}
}
