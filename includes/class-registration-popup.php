<?php
/**
 * Class RegistrationPopup
 *
 * Shows a nutritional profile onboarding modal after user registration.
 *
 * @package    CLOSE\NutritionInfo
 * @author     David Pérez <david@closemarketing.es>
 * @copyright  2021 Closemarketing
 * @version    1.0
 */

namespace CLOSE\NutritionInfo;

defined( 'ABSPATH' ) || exit;

/**
 * Class RegistrationPopup
 */
class RegistrationPopup {

	/**
	 * Constructor. Registers all required hooks.
	 */
	public function __construct() {
		add_action( 'user_register', array( $this, 'set_onboarding_flag' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_footer', array( $this, 'render_modal' ) );
		add_action( 'wp_ajax_niw_save_onboarding', array( $this, 'ajax_save' ) );
		add_action( 'wp_ajax_niw_dismiss_onboarding', array( $this, 'ajax_dismiss' ) );
	}

	/**
	 * Determines whether the modal should be shown for the current user.
	 *
	 * @return bool
	 */
	private function should_show(): bool {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		$user_id = get_current_user_id();
		$flag    = get_user_meta( $user_id, 'niw_show_onboarding_popup', true );

		if ( '1' !== $flag ) {
			return false;
		}

		$setting = get_option( 'wc_nutrients_registration_popup', 'no' );

		return 'yes' === $setting;
	}

	/**
	 * Sets the onboarding flag when a new user registers.
	 *
	 * @param int $user_id Newly registered user ID.
	 */
	public function set_onboarding_flag( int $user_id ): void {
		update_user_meta( $user_id, 'niw_show_onboarding_popup', '1' );
	}

	/**
	 * Enqueues the popup CSS and JS when the modal should be shown.
	 */
	public function enqueue_assets(): void {
		if ( ! $this->should_show() ) {
			return;
		}

		wp_enqueue_style(
			'niw-registration-popup',
			NIW_PLUGIN_URL . 'assets/css/registration-popup.css',
			array(),
			NIW_BUNDLE_VERSION
		);

		wp_enqueue_script(
			'niw-registration-popup',
			NIW_PLUGIN_URL . 'assets/js/registration-popup.js',
			array(),
			NIW_BUNDLE_VERSION,
			true
		);

		wp_localize_script(
			'niw-registration-popup',
			'niwOnboarding',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'niw_onboarding' ),
			)
		);
	}

	/**
	 * Outputs the onboarding modal HTML in the footer.
	 */
	public function render_modal(): void {
		if ( ! $this->should_show() ) {
			return;
		}
		?>
		<div id="niw-onboarding-overlay" class="niw-onboarding-overlay" role="dialog" aria-modal="true" aria-labelledby="niw-onboarding-title">
			<div class="niw-onboarding-card">
				<h2 id="niw-onboarding-title" class="niw-onboarding-title">
					<?php esc_html_e( 'Bienvenido/a', 'nutrition-info-woocommerce' ); ?>
				</h2>
				<p class="niw-onboarding-subtitle">
					<?php esc_html_e( 'Completa tu perfil nutricional para obtener recomendaciones personalizadas.', 'nutrition-info-woocommerce' ); ?>
				</p>

				<form id="niw-onboarding-form" novalidate>

					<div class="niw-onboarding-field">
						<label for="niw_user_age" class="niw-onboarding-label">
							<?php esc_html_e( 'Edad', 'nutrition-info-woocommerce' ); ?>
						</label>
						<input
							type="number"
							id="niw_user_age"
							name="niw_user_age"
							class="niw-onboarding-input"
							min="1"
							max="120"
							placeholder="<?php esc_attr_e( 'Años', 'nutrition-info-woocommerce' ); ?>"
						/>
					</div>

					<div class="niw-onboarding-field">
						<label for="niw_user_height" class="niw-onboarding-label">
							<?php esc_html_e( 'Altura (cm)', 'nutrition-info-woocommerce' ); ?>
						</label>
						<input
							type="number"
							id="niw_user_height"
							name="niw_user_height"
							class="niw-onboarding-input"
							min="50"
							max="300"
							placeholder="<?php esc_attr_e( 'cm', 'nutrition-info-woocommerce' ); ?>"
						/>
					</div>

					<div class="niw-onboarding-field">
						<label for="niw_user_weight" class="niw-onboarding-label">
							<?php esc_html_e( 'Peso (kg)', 'nutrition-info-woocommerce' ); ?>
						</label>
						<input
							type="number"
							id="niw_user_weight"
							name="niw_user_weight"
							class="niw-onboarding-input"
							min="20"
							max="500"
							placeholder="<?php esc_attr_e( 'kg', 'nutrition-info-woocommerce' ); ?>"
						/>
					</div>

					<div class="niw-onboarding-field">
						<label for="niw_user_activity" class="niw-onboarding-label">
							<?php esc_html_e( 'Nivel de Actividad', 'nutrition-info-woocommerce' ); ?>
						</label>
						<select id="niw_user_activity" name="niw_user_activity" class="niw-onboarding-input niw-onboarding-select">
							<option value=""><?php esc_html_e( '— Selecciona —', 'nutrition-info-woocommerce' ); ?></option>
							<option value="sedentary"><?php esc_html_e( 'Sedentario (sin ejercicio o muy poco)', 'nutrition-info-woocommerce' ); ?></option>
							<option value="light"><?php esc_html_e( 'Levemente activo (1–3 días/semana)', 'nutrition-info-woocommerce' ); ?></option>
							<option value="moderate"><?php esc_html_e( 'Moderadamente activo (3–5 días/semana)', 'nutrition-info-woocommerce' ); ?></option>
							<option value="very"><?php esc_html_e( 'Muy activo (6–7 días/semana)', 'nutrition-info-woocommerce' ); ?></option>
							<option value="extra"><?php esc_html_e( 'Extremadamente activo (trabajo físico intenso)', 'nutrition-info-woocommerce' ); ?></option>
						</select>
					</div>

					<div class="niw-onboarding-field">
						<label for="niw_user_meals" class="niw-onboarding-label">
							<?php esc_html_e( 'Nº de comidas diarias', 'nutrition-info-woocommerce' ); ?>
						</label>
						<input
							type="number"
							id="niw_user_meals"
							name="niw_user_meals"
							class="niw-onboarding-input"
							min="1"
							max="10"
							placeholder="<?php esc_attr_e( 'Comidas por día', 'nutrition-info-woocommerce' ); ?>"
						/>
					</div>

					<div class="niw-onboarding-field">
						<label for="niw_user_goal" class="niw-onboarding-label">
							<?php esc_html_e( 'Objetivo', 'nutrition-info-woocommerce' ); ?>
						</label>
						<select id="niw_user_goal" name="niw_user_goal" class="niw-onboarding-input niw-onboarding-select">
							<option value=""><?php esc_html_e( '— Selecciona —', 'nutrition-info-woocommerce' ); ?></option>
							<option value="muscle"><?php esc_html_e( 'Ganar masa muscular', 'nutrition-info-woocommerce' ); ?></option>
							<option value="maintenance"><?php esc_html_e( 'Mantenimiento', 'nutrition-info-woocommerce' ); ?></option>
							<option value="fat_loss"><?php esc_html_e( 'Perder grasa', 'nutrition-info-woocommerce' ); ?></option>
						</select>
					</div>

					<button type="submit" class="niw-onboarding-submit">
						<?php esc_html_e( 'Guardar y continuar', 'nutrition-info-woocommerce' ); ?>
					</button>

				</form>

				<p class="niw-onboarding-skip">
					<a href="#" id="niw-onboarding-skip" class="niw-onboarding-skip-link">
						<?php esc_html_e( 'Saltar por ahora', 'nutrition-info-woocommerce' ); ?>
					</a>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * AJAX handler: saves onboarding profile data and clears the flag.
	 */
	public function ajax_save(): void {
		check_ajax_referer( 'niw_onboarding', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
		}

		$user_id = get_current_user_id();

		$age      = isset( $_POST['niw_user_age'] ) ? absint( $_POST['niw_user_age'] ) : 0;
		$height   = isset( $_POST['niw_user_height'] ) ? absint( $_POST['niw_user_height'] ) : 0;
		$weight   = isset( $_POST['niw_user_weight'] ) ? absint( $_POST['niw_user_weight'] ) : 0;
		$meals    = isset( $_POST['niw_user_meals'] ) ? absint( $_POST['niw_user_meals'] ) : 0;

		$allowed_activity = array( 'sedentary', 'light', 'moderate', 'very', 'extra' );
		$activity = isset( $_POST['niw_user_activity'] ) ? sanitize_text_field( wp_unslash( $_POST['niw_user_activity'] ) ) : '';
		if ( ! in_array( $activity, $allowed_activity, true ) ) {
			$activity = '';
		}

		$allowed_goals = array( 'muscle', 'maintenance', 'fat_loss' );
		$goal = isset( $_POST['niw_user_goal'] ) ? sanitize_text_field( wp_unslash( $_POST['niw_user_goal'] ) ) : '';
		if ( ! in_array( $goal, $allowed_goals, true ) ) {
			$goal = '';
		}

		if ( $age ) {
			update_user_meta( $user_id, 'niw_user_age', $age );
		}
		if ( $height ) {
			update_user_meta( $user_id, 'niw_user_height', $height );
		}
		if ( $weight ) {
			update_user_meta( $user_id, 'niw_user_weight', $weight );
		}
		if ( $meals ) {
			update_user_meta( $user_id, 'niw_user_meals', $meals );
		}
		if ( $activity ) {
			update_user_meta( $user_id, 'niw_user_activity', $activity );
		}
		if ( $goal ) {
			update_user_meta( $user_id, 'niw_user_goal', $goal );
		}

		// Clear the flag so the popup does not appear again.
		delete_user_meta( $user_id, 'niw_show_onboarding_popup' );

		wp_send_json_success( array( 'message' => 'Profile saved.' ) );
	}

	/**
	 * AJAX handler: dismisses the onboarding popup without saving data.
	 */
	public function ajax_dismiss(): void {
		check_ajax_referer( 'niw_onboarding', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
		}

		$user_id = get_current_user_id();
		delete_user_meta( $user_id, 'niw_show_onboarding_popup' );

		wp_send_json_success( array( 'message' => 'Dismissed.' ) );
	}
}
