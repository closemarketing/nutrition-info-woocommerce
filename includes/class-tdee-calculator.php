<?php
/**
 * Class TDEECalculator
 *
 * Calculates Basal Metabolic Rate (BMR) and Total Daily Energy Expenditure
 * (TDEE) from a user's nutritional profile meta.
 *
 * Formula: Mifflin-St Jeor.
 *
 * @package CLOSE\NutritionInfo
 */

namespace CLOSE\NutritionInfo;

defined( 'ABSPATH' ) || exit;

/**
 * Class TDEECalculator
 */
class TDEECalculator {

	const ACTIVITY_MULTIPLIERS = array(
		'sedentary' => 1.2,
		'light'     => 1.375,
		'moderate'  => 1.55,
		'very'      => 1.725,
		'extra'     => 1.9,
	);

	const GOAL_ADJUSTMENTS = array(
		'fat_loss'    => -300,
		'maintenance' => 0,
		'muscle'      => 300,
	);

	/**
	 * Calculates TDEE for a given user.
	 *
	 * Returns an empty array if required data (age, height, weight, gender) is missing.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return array{bmr:int,tdee:int,goal_kcal:int,per_meal:int}|array{}
	 */
	public static function for_user( int $user_id ): array {
		$age    = (int) get_user_meta( $user_id, 'niw_user_age', true );
		$height = (int) get_user_meta( $user_id, 'niw_user_height', true );
		$weight = (int) get_user_meta( $user_id, 'niw_user_weight', true );
		$gender = get_user_meta( $user_id, 'niw_user_gender', true );

		if ( ! $age || ! $height || ! $weight || ! in_array( $gender, array( 'male', 'female' ), true ) ) {
			return array();
		}

		$activity = get_user_meta( $user_id, 'niw_user_activity', true );
		$meals    = (int) get_user_meta( $user_id, 'niw_user_meals', true );
		$goal     = get_user_meta( $user_id, 'niw_user_goal', true );

		// Mifflin-St Jeor BMR.
		$bmr  = 10 * $weight + 6.25 * $height - 5 * $age;
		$bmr += ( 'female' === $gender ) ? -161 : 5;

		$multiplier = self::ACTIVITY_MULTIPLIERS[ $activity ] ?? 1.2;
		$tdee       = (int) round( $bmr * $multiplier );

		$adjustment = self::GOAL_ADJUSTMENTS[ $goal ] ?? 0;
		$goal_kcal  = $tdee + $adjustment;

		$per_meal = $meals > 0 ? (int) round( $goal_kcal / $meals ) : 0;

		return array(
			'bmr'       => (int) round( $bmr ),
			'tdee'      => $tdee,
			'goal_kcal' => $goal_kcal,
			'per_meal'  => $per_meal,
		);
	}
}
