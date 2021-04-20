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
			'label' => __( '- Of which Saturated fatty acids (g)' ),
		),
		array(
			'key'   => 'monounsaturated_fat',
			'label' => __( '- Of which monounsaturated fatty acids (g)' ),
		),
		array(
			'key'   => 'polyunsaturated_fat',
			'label' => __( '- Of which polyunsaturated fatty acids (g)' ),
		),
		array(
			'key'   => 'carb',
			'label' => __( 'Carbohydrate (g)' ),
		),
		array(
			'key'   => 'sugar',
			'label' => __( '- Of which sugars (g)' ),
		),
		array(
			'key'   => 'polyol',
			'label' => __( '- Of which Polyols (g)' ),
		),
		array(
			'key'   => 'starch',
			'label' => __( '- Of which Starch (g)' ),
		),
		array(
			'key'   => 'fiber',
			'label' => __( 'Dietary Fiber (g)' ),
		),
		array(
			'key'   => 'protein',
			'label' => __( 'Protein (g)' ),
		),
		array(
			'key'   => 'salt',
			'label' => __( 'Salt (g)' ),
		),
		array(
			'key'   => 'vitamin_mineral',
			'label' => __( 'Vitamins and minerals' ),
		),

	);
	?>
	<table id="nutrition-table">
		<thead>
			<tr>
				<th class="nutrition-table nutrition-table__nutrient-name"><?= __('Nutritional Information', 'nutritional_info_domain'); ?></th>
				<th class="nutrition-table nutrition-table__nutrient-amount"><?= __('pr. 100 g', 'nutritional_info_domain'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$post_id = get_the_ID();
			foreach ( $attr_products_nutrition as $attr_nutrition ) {
				$value_meta = get_post_meta( $post_id, NIW_PLUGIN_PREFIX . $attr_nutrition['key'], true  );
				if ( $value_meta ) {
					echo '<tr>';
					echo '<td class="nutrition-table nutrition-table__nutrient-name">' . esc_html( $attr_nutrition['label'] ) . '</td>';
					echo '<td class="nutrition-table nutrition-table__nutrient-amount">' . esc_html( $value_meta ) . '</td>';
					echo '</tr>';
				}
			} ?>
		</tbody>
	</table>
<?php }



function compositionInfo() {
	$attr_products_nutrition = array(
		array(
			'key'   => 'ingredients',
			'label' => __( 'Ingredients', 'nutrition-info-woocommerce' ),
		),
		array(
			'key'   => 'allergens',
			'label' => __( 'Allergens', 'nutrition-info-woocommerce' ),
		)
	);

	?>

	<table id="nutrition-table">
		<thead>
			<tr>
				<th class="nutrition-table nutrition-table__nutrient-name"><?= __('Nutritional Information', 'nutritional_info_domain'); ?></th>
				<th class="nutrition-table nutrition-table__nutrient-amount"><?= __('pr. 100 g', 'nutritional_info_domain'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$post_id = get_the_ID();
			foreach ( $attr_products_nutrition as $attr_nutrition ) {
				$value_meta = get_post_meta( $post_id, NIW_PLUGIN_PREFIX . $attr_nutrition['key'], true  );
				if ( $value_meta ) {
					echo '<tr>';
					echo '<td class="nutrition-table nutrition-table__nutrient-name">' . esc_html( $attr_nutrition['label'] ) . '</td>';
					if( $attr_nutrition['key'] == 'allergens' )
					{
						echo '<td class="nutrition-table nutrition-table__nutrient-amount">';
						foreach (explode(" ", $value_meta) as $key => $value) 
						{
							echo "<input type='checkbox' checked disabled>" . esc_html( $value ) . "<br>";
						}
						echo "</td>";
					}
					else {
						echo '<td class="nutrition-table nutrition-table__nutrient-amount">' . esc_html( $value_meta ) . '</td>';
					}
					echo '</tr>';
				}
			} ?>
		</tbody>
	</table>

<?php } ?>
