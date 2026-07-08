<?php
/**
 * Templates for nutrition and composition info.
 *
 * @package CLOSE\NutritionInfo
 */

namespace CLOSE\NutritionInfo;

/**
 * Shared nutrition field definitions: key => label + display unit.
 *
 * Values are always entered and stored as plain numbers, per 100 g of product.
 * The unit is never part of the stored value — it is appended wherever the
 * value is displayed (admin panel and frontend table).
 *
 * @return array
 */
function niw_get_nutrition_fields() {
	return array(
		'energy'              => array(
			'label' => __( 'Energy', 'nutrition-info-woocommerce' ),
			'unit'  => __( 'kcal', 'nutrition-info-woocommerce' ),
			'sub'   => false,
		),
		'fat'                 => array(
			'label' => __( 'Fat', 'nutrition-info-woocommerce' ),
			'unit'  => __( 'g', 'nutrition-info-woocommerce' ),
			'sub'   => false,
		),
		'saturated_fat'       => array(
			'label' => __( 'Saturated fatty acids', 'nutrition-info-woocommerce' ),
			'unit'  => __( 'g', 'nutrition-info-woocommerce' ),
			'sub'   => true,
		),
		'monounsaturated_fat' => array(
			'label' => __( 'Monounsaturated fatty acids', 'nutrition-info-woocommerce' ),
			'unit'  => __( 'g', 'nutrition-info-woocommerce' ),
			'sub'   => true,
		),
		'polyunsaturated_fat' => array(
			'label' => __( 'Polyunsaturated fatty acids', 'nutrition-info-woocommerce' ),
			'unit'  => __( 'g', 'nutrition-info-woocommerce' ),
			'sub'   => true,
		),
		'carb'                => array(
			'label' => __( 'Carbohydrate', 'nutrition-info-woocommerce' ),
			'unit'  => __( 'g', 'nutrition-info-woocommerce' ),
			'sub'   => false,
		),
		'sugar'               => array(
			'label' => __( 'Sugar', 'nutrition-info-woocommerce' ),
			'unit'  => __( 'g', 'nutrition-info-woocommerce' ),
			'sub'   => true,
		),
		'polyol'              => array(
			'label' => __( 'Polyols', 'nutrition-info-woocommerce' ),
			'unit'  => __( 'g', 'nutrition-info-woocommerce' ),
			'sub'   => true,
		),
		'starch'              => array(
			'label' => __( 'Starch', 'nutrition-info-woocommerce' ),
			'unit'  => __( 'g', 'nutrition-info-woocommerce' ),
			'sub'   => true,
		),
		'fiber'               => array(
			'label' => __( 'Dietary fiber', 'nutrition-info-woocommerce' ),
			'unit'  => __( 'g', 'nutrition-info-woocommerce' ),
			'sub'   => false,
		),
		'protein'             => array(
			'label' => __( 'Protein', 'nutrition-info-woocommerce' ),
			'unit'  => __( 'g', 'nutrition-info-woocommerce' ),
			'sub'   => false,
		),
		'salt'                => array(
			'label' => __( 'Salt', 'nutrition-info-woocommerce' ),
			'unit'  => __( 'g', 'nutrition-info-woocommerce' ),
			'sub'   => false,
		),
		'vitamin_mineral'     => array(
			'label' => __( 'Vitamins and minerals', 'nutrition-info-woocommerce' ),
			'unit'  => '',
			'sub'   => false,
		),
	);
}

/**
 * Render nutrition info table.
 */
function niw_nutrition_info() {
	?>
	<details class="niw_nutritional_information">
		<summary class="niw_tittle_nutritional_information"><?php esc_html_e( 'Nutritional Information', 'nutrition-info-woocommerce' ); ?></summary>
		<p class="niw_nutrition_note"><?php esc_html_e( 'Values shown are always per 100 g of product.', 'nutrition-info-woocommerce' ); ?></p>
		<table id="nutrition-table">
			<thead>
				<tr>
					<th class="nutrition-table nutrition-table_nutrient-name"><?php esc_html_e( 'Nutritional Information', 'nutrition-info-woocommerce' ); ?></th>
					<th class="nutrition-table nutrition-table_nutrient-amount"><?php esc_html_e( 'Per 100 g', 'nutrition-info-woocommerce' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$post_id = get_the_ID();
				foreach ( niw_get_nutrition_fields() as $key => $field ) {
					$value_meta = get_post_meta( $post_id, 'niw_' . $key, true );
					if ( '' === $value_meta ) {
						continue;
					}
					$display = $field['unit'] ? $value_meta . ' ' . $field['unit'] : $value_meta;
					$label   = $field['sub'] ? '- ' . $field['label'] : $field['label'];
					echo '<tr>';
					echo '<td class="nutrition-table nutrition-table_nutrient-name">' . esc_html( $label ) . '</td>';
					echo '<td class="nutrition-table nutrition-table_nutrient-amount">' . esc_html( $display ) . '</td>';
					echo '</tr>';
				}
				?>
			</tbody>
		</table>
	</details>
	<?php
}


/**
 * Show in tab the ingredients.
 *
 * @return void
 */
function niw_composition_info() {
	$attr_products_nutrition = array(
		array(
			'key'   => 'ingredients',
			'label' => __( 'Ingredients', 'nutrition-info-woocommerce' ),
		),
	);
	?>
	<details class="niw_additional_information">
		<summary class="niw_tittle_additional_information"><?php esc_html_e( 'Ingredients', 'nutrition-info-woocommerce' ); ?></summary>
		<tbody>
			<?php
			$post_id = get_the_ID();
			foreach ( $attr_products_nutrition as $attr_nutrition ) {
				$value_meta = get_post_meta( $post_id, 'niw_' . $attr_nutrition['key'], true );
				if ( $value_meta && 'allergens' !== $attr_nutrition['key'] ) {
					echo '<br>';
					echo '<p class="nutrition-table nutrition-table_nutrient-name">' . esc_html( $attr_nutrition['label'] ) . '</p>';
					echo '<p class="nutrition-table nutrition-table_nutrient-amount">' . esc_html( $value_meta ) . '</p>';
					echo '</br>';
				}
			}
			?>
		</tbody>
	</details>
	<?php
}
