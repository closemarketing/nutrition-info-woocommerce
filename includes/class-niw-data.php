<?php
/**
 * NIW Data
 *
 * Allergen list and nutrient definitions — self-contained, no external dependencies.
 *
 * @package nutrition-info-woocommerce
 */

defined( 'ABSPATH' ) || exit;

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
			'gluten'     => __( 'Gluten',      'nutrition-info-woocommerce' ),
			'crustacean' => __( 'Crustacean',  'nutrition-info-woocommerce' ),
			'egg'        => __( 'Egg',         'nutrition-info-woocommerce' ),
			'fish'       => __( 'Fish',        'nutrition-info-woocommerce' ),
			'peanut'     => __( 'Peanut',      'nutrition-info-woocommerce' ),
			'soy'        => __( 'Soy',         'nutrition-info-woocommerce' ),
			'dairy'      => __( 'Dairy',       'nutrition-info-woocommerce' ),
			'shell'      => __( 'Shell',       'nutrition-info-woocommerce' ),
			'celery'     => __( 'Celery',      'nutrition-info-woocommerce' ),
			'mustard'    => __( 'Mustard',     'nutrition-info-woocommerce' ),
			'sesame'     => __( 'Sesame',      'nutrition-info-woocommerce' ),
			'sulfite'    => __( 'Sulfite',     'nutrition-info-woocommerce' ),
			'mollusk'    => __( 'Mollusk',     'nutrition-info-woocommerce' ),
			'lupins'     => __( 'Lupins',      'nutrition-info-woocommerce' ),
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
			// Energía
			'energy_kcal'       => array( 'label' => __( 'Valor energético',        'nutrition-info-woocommerce' ), 'unit' => 'kcal', 'group' => 'energy' ),
			'energy_kj'         => array( 'label' => __( 'Valor energético',        'nutrition-info-woocommerce' ), 'unit' => 'kJ',   'group' => 'energy' ),
			// Grasas
			'fat'               => array( 'label' => __( 'Grasas',                  'nutrition-info-woocommerce' ), 'unit' => 'g',  'group' => 'fat' ),
			'fat_saturated'     => array( 'label' => __( 'Ácidos grasos saturados', 'nutrition-info-woocommerce' ), 'unit' => 'g',  'group' => 'fat', 'sub' => true ),
			'fat_monounsat'     => array( 'label' => __( 'Monoinsaturados',         'nutrition-info-woocommerce' ), 'unit' => 'g',  'group' => 'fat', 'sub' => true ),
			'fat_polyunsat'     => array( 'label' => __( 'Poliinsaturados',         'nutrition-info-woocommerce' ), 'unit' => 'g',  'group' => 'fat', 'sub' => true ),
			'fat_trans'         => array( 'label' => __( 'Ácidos grasos trans',     'nutrition-info-woocommerce' ), 'unit' => 'g',  'group' => 'fat', 'sub' => true ),
			'cholesterol'       => array( 'label' => __( 'Colesterol',              'nutrition-info-woocommerce' ), 'unit' => 'mg', 'group' => 'fat' ),
			// Hidratos
			'carbs'             => array( 'label' => __( 'Hidratos de carbono',     'nutrition-info-woocommerce' ), 'unit' => 'g', 'group' => 'carbs' ),
			'carbs_sugar'       => array( 'label' => __( 'Azúcares',               'nutrition-info-woocommerce' ), 'unit' => 'g', 'group' => 'carbs', 'sub' => true ),
			'carbs_sugar_added' => array( 'label' => __( 'Azúcares añadidos',      'nutrition-info-woocommerce' ), 'unit' => 'g', 'group' => 'carbs', 'sub' => true ),
			'carbs_polyols'     => array( 'label' => __( 'Polialcoholes',          'nutrition-info-woocommerce' ), 'unit' => 'g', 'group' => 'carbs', 'sub' => true ),
			'carbs_starch'      => array( 'label' => __( 'Almidón',                'nutrition-info-woocommerce' ), 'unit' => 'g', 'group' => 'carbs', 'sub' => true ),
			'fiber'             => array( 'label' => __( 'Fibra alimentaria',      'nutrition-info-woocommerce' ), 'unit' => 'g', 'group' => 'carbs' ),
			'fiber_soluble'     => array( 'label' => __( 'Fibra soluble',          'nutrition-info-woocommerce' ), 'unit' => 'g', 'group' => 'carbs', 'sub' => true ),
			'fiber_insoluble'   => array( 'label' => __( 'Fibra insoluble',        'nutrition-info-woocommerce' ), 'unit' => 'g', 'group' => 'carbs', 'sub' => true ),
			// Proteínas y sal
			'protein'           => array( 'label' => __( 'Proteínas',              'nutrition-info-woocommerce' ), 'unit' => 'g',  'group' => 'protein' ),
			'salt'              => array( 'label' => __( 'Sal',                    'nutrition-info-woocommerce' ), 'unit' => 'g',  'group' => 'protein' ),
			'sodium'            => array( 'label' => __( 'Sodio',                  'nutrition-info-woocommerce' ), 'unit' => 'mg', 'group' => 'protein', 'sub' => true ),
			// Vitaminas
			'vit_a'             => array( 'label' => __( 'Vitamina A',             'nutrition-info-woocommerce' ), 'unit' => 'μg', 'group' => 'vitamins' ),
			'vit_b1'            => array( 'label' => __( 'Tiamina (B1)',           'nutrition-info-woocommerce' ), 'unit' => 'mg', 'group' => 'vitamins' ),
			'vit_b2'            => array( 'label' => __( 'Riboflavina (B2)',       'nutrition-info-woocommerce' ), 'unit' => 'mg', 'group' => 'vitamins' ),
			'vit_b3'            => array( 'label' => __( 'Niacina (B3)',           'nutrition-info-woocommerce' ), 'unit' => 'mg', 'group' => 'vitamins' ),
			'vit_b5'            => array( 'label' => __( 'Ácido pantoténico (B5)', 'nutrition-info-woocommerce' ), 'unit' => 'mg', 'group' => 'vitamins' ),
			'vit_b6'            => array( 'label' => __( 'Vitamina B6',           'nutrition-info-woocommerce' ), 'unit' => 'mg', 'group' => 'vitamins' ),
			'vit_b7'            => array( 'label' => __( 'Biotina (B7)',           'nutrition-info-woocommerce' ), 'unit' => 'μg', 'group' => 'vitamins' ),
			'vit_b9'            => array( 'label' => __( 'Ácido fólico (B9)',      'nutrition-info-woocommerce' ), 'unit' => 'μg', 'group' => 'vitamins' ),
			'vit_b12'           => array( 'label' => __( 'Vitamina B12',           'nutrition-info-woocommerce' ), 'unit' => 'μg', 'group' => 'vitamins' ),
			'vit_c'             => array( 'label' => __( 'Vitamina C',             'nutrition-info-woocommerce' ), 'unit' => 'mg', 'group' => 'vitamins' ),
			'vit_d'             => array( 'label' => __( 'Vitamina D',             'nutrition-info-woocommerce' ), 'unit' => 'μg', 'group' => 'vitamins' ),
			'vit_e'             => array( 'label' => __( 'Vitamina E',             'nutrition-info-woocommerce' ), 'unit' => 'mg', 'group' => 'vitamins' ),
			'vit_k'             => array( 'label' => __( 'Vitamina K',             'nutrition-info-woocommerce' ), 'unit' => 'μg', 'group' => 'vitamins' ),
			// Minerales
			'min_calcium'       => array( 'label' => __( 'Calcio',     'nutrition-info-woocommerce' ), 'unit' => 'mg', 'group' => 'minerals' ),
			'min_phosphorus'    => array( 'label' => __( 'Fósforo',   'nutrition-info-woocommerce' ), 'unit' => 'mg', 'group' => 'minerals' ),
			'min_iron'          => array( 'label' => __( 'Hierro',    'nutrition-info-woocommerce' ), 'unit' => 'mg', 'group' => 'minerals' ),
			'min_magnesium'     => array( 'label' => __( 'Magnesio',  'nutrition-info-woocommerce' ), 'unit' => 'mg', 'group' => 'minerals' ),
			'min_zinc'          => array( 'label' => __( 'Zinc',      'nutrition-info-woocommerce' ), 'unit' => 'mg', 'group' => 'minerals' ),
			'min_iodine'        => array( 'label' => __( 'Yodo',      'nutrition-info-woocommerce' ), 'unit' => 'μg', 'group' => 'minerals' ),
			'min_selenium'      => array( 'label' => __( 'Selenio',   'nutrition-info-woocommerce' ), 'unit' => 'μg', 'group' => 'minerals' ),
			'min_copper'        => array( 'label' => __( 'Cobre',     'nutrition-info-woocommerce' ), 'unit' => 'mg', 'group' => 'minerals' ),
			'min_manganese'     => array( 'label' => __( 'Manganeso', 'nutrition-info-woocommerce' ), 'unit' => 'mg', 'group' => 'minerals' ),
			'min_chromium'      => array( 'label' => __( 'Cromo',     'nutrition-info-woocommerce' ), 'unit' => 'μg', 'group' => 'minerals' ),
			'min_molybdenum'    => array( 'label' => __( 'Molibdeno', 'nutrition-info-woocommerce' ), 'unit' => 'μg', 'group' => 'minerals' ),
			'min_potassium'     => array( 'label' => __( 'Potasio',   'nutrition-info-woocommerce' ), 'unit' => 'mg', 'group' => 'minerals' ),
			'min_fluoride'      => array( 'label' => __( 'Flúor',     'nutrition-info-woocommerce' ), 'unit' => 'mg', 'group' => 'minerals' ),
			'min_chloride'      => array( 'label' => __( 'Cloruro',   'nutrition-info-woocommerce' ), 'unit' => 'mg', 'group' => 'minerals' ),
		);
	}

	/** Group labels for the nutrient table. */
	public static function get_group_labels() {
		return array(
			'energy'   => __( 'Energía',             'nutrition-info-woocommerce' ),
			'fat'      => __( 'Grasas',               'nutrition-info-woocommerce' ),
			'carbs'    => __( 'Hidratos de carbono',  'nutrition-info-woocommerce' ),
			'protein'  => __( 'Proteínas y sal',      'nutrition-info-woocommerce' ),
			'vitamins' => __( 'Vitaminas',            'nutrition-info-woocommerce' ),
			'minerals' => __( 'Minerales',            'nutrition-info-woocommerce' ),
		);
	}
}
