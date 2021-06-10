<?php


function nutritionInfo() {

	$attr_products_nutrition = array(
		array(
			'key'   => 'energy',
			'label' => __( 'Energi (KJ/kcal)', 'nutrition-info-woocommerce' ),
		),
		array(
			'key'   => 'fat',
			'label' => __( 'Fat (g)', 'nutrition-info-woocommerce' ),
		),
		array(
			'key'   => 'saturated_fat',
			'label' => __( '- Of which Saturated fatty acids (g)', 'nutrition-info-woocommerce' ),
		),
		array(
			'key'   => 'monounsaturated_fat',
			'label' => __( '- Of which monounsaturated fatty acids (g)', 'nutrition-info-woocommerce' ),
		),
		array(
			'key'   => 'polyunsaturated_fat',
			'label' => __( '- Of which polyunsaturated fatty acids (g)', 'nutrition-info-woocommerce' ),
		),
		array(
			'key'   => 'carb',
			'label' => __( 'Carbohydrate (g)', 'nutrition-info-woocommerce' ),
		),
		array(
			'key'   => 'sugar',
			'label' => __( '- Of which sugars (g)', 'nutrition-info-woocommerce' ),
		),
		array(
			'key'   => 'polyol',
			'label' => __( '- Of which Polyols (g)', 'nutrition-info-woocommerce' ),
		),
		array(
			'key'   => 'starch',
			'label' => __( '- Of which Starch (g)', 'nutrition-info-woocommerce' ),
		),
		array(
			'key'   => 'fiber',
			'label' => __( 'Dietary Fiber (g)', 'nutrition-info-woocommerce' ),
		),
		array(
			'key'   => 'protein',
			'label' => __( 'Protein (g)', 'nutrition-info-woocommerce' ),
		),
		array(
			'key'   => 'salt',
			'label' => __( 'Salt (g)', 'nutrition-info-woocommerce' ),
		),
		array(
			'key'   => 'vitamin_mineral',
			'label' => __( 'Vitamins and minerals', 'nutrition-info-woocommerce' ),
		),

	);
	?>
	<details class="niw_nutritional_information">
		<summary class="niw_tittle_nutritional_information"><?php esc_html_e( 'Nutritional Information', 'nutrition-info-woocommerce' ); ?></summary>
		<table id="nutrition-table">
			<thead>
				<tr>
					<th class="nutrition-table nutrition-table_nutrient-name"><?php esc_html_e( 'Nutritional Information', 'nutrition-info-woocommerce' ); ?></th>
					<th class="nutrition-table nutrition-table_nutrient-amount"><?php esc_html_e( 'pr. 100 g', 'nutrition-info-woocommerce' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$post_id = get_the_ID();
				foreach ( $attr_products_nutrition as $attr_nutrition ) {
					$value_meta = get_post_meta( $post_id, NIW_PLUGIN_PREFIX . $attr_nutrition['key'], true );
					if ( $value_meta ) {
						echo '<tr>';
						echo '<td class="nutrition-table nutrition-table_nutrient-name">' . esc_html( $attr_nutrition['label'] ) . '</td>';
						echo '<td class="nutrition-table nutrition-table_nutrient-amount">' . esc_html( $value_meta ) . '</td>';
						echo '</tr>';
					}
				} ?>
			</tbody>
		</table>
	</details>
<?php }


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
		)
	);
	?>
	<details class="niw_additional_information">
		<summary class="niw_tittle_additional_information"><?php esc_html_e( 'Ingredients', 'nutrition-info-woocommerce' ); ?></summary>
		<tbody>
			<?php 
			$post_id = get_the_ID();
			foreach ( $attr_products_nutrition as $attr_nutrition ) {
				$value_meta = get_post_meta( $post_id, NIW_PLUGIN_PREFIX . $attr_nutrition['key'], true );
				if ( $value_meta ) {
					echo '<br>';
					echo '<p class="nutrition-table nutrition-table_nutrient-name">' . esc_html( $attr_nutrition['label'] ) . '</p>';
					if( $attr_nutrition['key'] == 'allergens' )
					{
					}
					else {
						echo '<p class="nutrition-table nutrition-table_nutrient-amount">' . esc_html( $value_meta ) . '</p>';
					}
					echo '</br>';
				}
			} ?>
		</tbody>
	</details>
<?php } ?>
