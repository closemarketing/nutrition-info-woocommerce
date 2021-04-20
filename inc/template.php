<?php

/*function nutritionInfo() {
	$energy              = get_post_meta(get_the_ID(), NIW_PLUGIN_PREFIX . 'energy', true  );
	$fat                 = get_post_meta(get_the_ID(), NIW_PLUGIN_PREFIX . 'fat', true  );
	$saturated_fat       = get_post_meta(get_the_ID(), NIW_PLUGIN_PREFIX . 'saturated_fat', true  );
	$monounsaturated_fat = get_post_meta(get_the_ID(), NIW_PLUGIN_PREFIX . 'monounsaturated_fat', true  );
	$polyunsaturated_fat = get_post_meta(get_the_ID(), NIW_PLUGIN_PREFIX . 'polyunsaturated_fat', true  );
	$carb                = get_post_meta(get_the_ID(), NIW_PLUGIN_PREFIX . 'carb', true  );
	$sugar               = get_post_meta(get_the_ID(), NIW_PLUGIN_PREFIX . 'sugar', true  );
	$polyol              = get_post_meta(get_the_ID(), NIW_PLUGIN_PREFIX . 'polyol', true  );
	$starch              = get_post_meta(get_the_ID(), NIW_PLUGIN_PREFIX . 'starch', true  );
	$fiber               = get_post_meta(get_the_ID(), NIW_PLUGIN_PREFIX . 'fiber', true  );
	$protein             = get_post_meta(get_the_ID(), NIW_PLUGIN_PREFIX . 'protein', true  );
	$salt                = get_post_meta(get_the_ID(), NIW_PLUGIN_PREFIX . 'salt', true  );
	$vitamin_mineral     = get_post_meta(get_the_ID(), NIW_PLUGIN_PREFIX . 'vitamin_mineral', true  );
<?php
*/
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

/*


		<?php if ($energy) { ?>
		<tr>
			<td class="nutrition-table nutrition-table__nutrient-name"><?= __('Energi (KJ/kcal)', 'nutritional_info_domain'); ?></td>
			<td class="nutrition-table nutrition-table__nutrient-amount"><?= $energy; ?></td>
		</tr>
		<?php } if ($fat) { ?>
		<tr>
			<td class="nutrition-table nutrition-table__nutrient-name"><?= __('Fat (g)', 'nutritional_info_domain'); ?></td>
			<td class="nutrition-table nutrition-table__nutrient-amount"><?= $fat; ?></td>
		</tr>
		<?php } if ($saturated_fat) { ?>
		<tr>
			<td class="nutrition-table nutrition-table__nutrient-name nutrition-table__nutrient-name_subnutrient"><?= __('- of which Saturated fatty acids (g)', 'nutritional_info_domain'); ?></td>
			<td class="nutrition-table nutrition-table__nutrient-amount"><?= $saturated_fat; ?></td>
		</tr>
		<?php } if ($monounsaturated_fat) { ?>
		<tr>
			<td class="nutrition-table nutrition-table__nutrient-name nutrition-table__nutrient-name_subnutrient"><?= __('- of which monounsaturated fatty acids (g)', 'nutritional_info_domain'); ?></td>
			<td class="nutrition-table nutrition-table__nutrient-amount"><?= $monounsaturated_fat; ?></td>
		</tr>
		<?php } if ($polyunsaturated_fat) { ?>
		<tr>
			<td class="nutrition-table nutrition-table__nutrient-name nutrition-table__nutrient-name_subnutrient"><?= __('- of which polyunsaturated fatty acids (g)', 'nutritional_info_domain'); ?></td>
			<td class="nutrition-table nutrition-table__nutrient-amount"><?= $polyunsaturated_fat; ?></td>
		</tr>
		<?php } if ($carb) { ?>
		<tr>
			<td class="nutrition-table nutrition-table__nutrient-name"><?= __('Carbohydrate (g)', 'nutritional_info_domain'); ?></td>
			<td class="nutrition-table nutrition-table__nutrient-amount"><?= $carb; ?></td>
		</tr>
		<?php } if ($sugar) { ?>
		<tr>
			<td class="nutrition-table nutrition-table__nutrient-name nutrition-table__nutrient-name_subnutrient"><?= __('- of which sugars (g)', 'nutritional_info_domain'); ?></td>
			<td class="nutrition-table nutrition-table__nutrient-amount"><?= $sugar; ?></td>
		</tr>
		<?php } if ($polyol) { ?>
		<tr>
			<td class="nutrition-table nutrition-table__nutrient-name nutrition-table__nutrient-name_subnutrient"><?= __('- of which Polyols (g)', 'nutritional_info_domain'); ?></td>
			<td class="nutrition-table nutrition-table__nutrient-amount"><?= $polyol; ?></td>
		</tr>
		<?php } if ($starch) { ?>
		<tr>
			<td class="nutrition-table nutrition-table__nutrient-name nutrition-table__nutrient-name_subnutrient"><?= __('- of which Starch (g)', 'nutritional_info_domain'); ?></td>
			<td class="nutrition-table nutrition-table__nutrient-amount"><?= $starch; ?></td>
		</tr>
		<?php } if ($fiber) { ?>
		<tr>
			<td class="nutrition-table nutrition-table__nutrient-name"><?= __('Dietary Fiber (g)', 'nutritional_info_domain'); ?></td>
			<td class="nutrition-table nutrition-table__nutrient-amount"><?= $fiber; ?></td>
		</tr>
		<?php } if ($protein) { ?>
		<tr>
			<td class="nutrition-table nutrition-table__nutrient-name"><?= __('Protein (g)', 'nutritional_info_domain'); ?></td>
			<td class="nutrition-table nutrition-table__nutrient-amount"><?= $protein; ?></td>
		</tr>
		<?php } if ($salt) { ?>
		<tr>
			<td class="nutrition-table nutrition-table__nutrient-name"><?= __('Salt (g)', 'nutritional_info_domain'); ?></td>
			<td class="nutrition-table nutrition-table__nutrient-amount"><?= $salt; ?></td>
		</tr>
		<?php } if ($vitamin_mineral) { ?>
		<tr>
			<td class="nutrition-table nutrition-table__nutrient-name"><?= __('Vitamins and minerals', 'nutritional_info_domain'); ?></td>
			<td class="nutrition-table nutrition-table__nutrient-amount"><?= $vitamin_mineral; ?></td>
		</tr>
		<?php }; */


function compositionInfo() {
	$ingredients              = get_post_meta(get_the_ID(), NIW_PLUGIN_PREFIX . 'ingredients', true  );
	$allergens              = get_post_meta(get_the_ID(), NIW_PLUGIN_PREFIX . 'allergens', true  );

	?>

	<table id="nutrition-table">
	<thead>
		<tr>
		<th class="nutrition-table nutrition-table__nutrient-name"><?= __('Composition Information', 'nutritional_info_domain'); ?></th>
		<th class="nutrition-table nutrition-table__nutrient-amount"><?= __('Values', 'nutritional_info_domain'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if ($ingredients) { ?>
		<tr>
			<td class="nutrition-table nutrition-table__nutrient-name"><?= __('Ingredients', 'nutritional_info_domain'); ?></td>
			<td class="nutrition-table nutrition-table__nutrient-amount"><?= $ingredients; ?></td>
		</tr>
		<?php } if ($allergens) { ?>
		<tr>
			<td class="nutrition-table nutrition-table__nutrient-name"><?= __('Allergens', 'nutritional_info_domain'); ?></td>
			<td class="nutrition-table nutrition-table__nutrient-amount"><?= $allergens; ?></td>
		</tr>
		<?php }; ?>
	</tbody>
</table>

<?php } ?>
