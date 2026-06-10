<?php
/**
 * Class Settings
 *
 * @package    CLOSE\NutritionInfo
 * @author     David Pérez <david@closemarketing.es>
 * @copyright  2021 Closemarketing
 * @version    1.0
 */

namespace CLOSE\NutritionInfo;

defined( 'ABSPATH' ) || exit;

/**
 * Class Settings
 */
class WooSettings {

	/**
	 * Bootstraps the class and hooks required actions & filters.
	 **/
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_submenu' ) );
		add_action( 'admin_post_niw_save_settings', array( __CLASS__, 'handle_save' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_tailwind' ) );
	}

	/**
	 * Enqueue Tailwind CDN only on this plugin page.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public static function enqueue_tailwind( string $hook ): void {
		if ( 'woocommerce_page_niw-settings' !== $hook ) {
			return;
		}
		wp_enqueue_script(
			'tailwind-cdn',
			'https://cdn.tailwindcss.com',
			array(),
			null,
			false
		);
	}

	/**
	 * Register submenu page under WooCommerce.
	 */
	public static function add_submenu(): void {
		add_submenu_page(
			'woocommerce',
			__( 'Nutrition Info', 'nutrition-info-woocommerce' ),
			__( 'Nutrition Info', 'nutrition-info-woocommerce' ),
			'manage_woocommerce',
			'niw-settings',
			array( __CLASS__, 'render_page' )
		);
	}

	/**
	 * Handle form save via admin-post.php.
	 */
	public static function handle_save(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'No tienes permiso para hacer esto.', 'nutrition-info-woocommerce' ) );
		}

		check_admin_referer( 'niw_save_settings' );

		$text_fields = array(
			'wc_nutrients_settings_tab_title',
			'wc_nutrients_settings_tab_per_volume_text',
			'wc_nutrients_settings_tab_position',
		);

		foreach ( $text_fields as $key ) {
			$value = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
			update_option( $key, $value );
		}

		$checkboxes = array(
			'wc_nutrients_settings_tab_styling',
			'wc_nutrients_registration_popup',
		);

		foreach ( $checkboxes as $key ) {
			update_option( $key, isset( $_POST[ $key ] ) ? 'yes' : 'no' );
		}

		wp_safe_redirect( admin_url( 'admin.php?page=niw-settings&saved=1' ) );
		exit;
	}

	/**
	 * Render the full settings page with Tailwind UI.
	 */
	public static function render_page(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$saved          = isset( $_GET['saved'] ) && '1' === $_GET['saved'];
		$title          = get_option( 'wc_nutrients_settings_tab_title', __( 'Nutritional Information', 'nutrition-info-woocommerce' ) );
		$per_volume     = get_option( 'wc_nutrients_settings_tab_per_volume_text', 'Per 100 g' );
		$position       = get_option( 'wc_nutrients_settings_tab_position', 'tab' );
		$styling        = 'yes' === get_option( 'wc_nutrients_settings_tab_styling', 'yes' );
		$popup_enabled  = 'yes' === get_option( 'wc_nutrients_registration_popup', 'no' );

		$positions = array(
			'tab'                => __( 'Standalone tab', 'nutrition-info-woocommerce' ),
			'in_description_tab' => __( 'Inside the description tab', 'nutrition-info-woocommerce' ),
			'after_price'        => __( 'After the price', 'nutrition-info-woocommerce' ),
			'after_excerpt'      => __( 'After the excerpt', 'nutrition-info-woocommerce' ),
			'after_add_to_cart'  => __( 'After the "Add to cart" button', 'nutrition-info-woocommerce' ),
			'after_meta'         => __( 'After product meta', 'nutrition-info-woocommerce' ),
			'hidden'             => __( 'Hidden — manual placement via shortcode', 'nutrition-info-woocommerce' ),
		);
		?>
		<div class="wrap" style="font-family:inherit">
		<div class="max-w-4xl mx-auto py-8 px-4">

			<!-- ── Header ── -->
			<div class="flex items-center gap-4 mb-8">
				<div class="w-12 h-12 rounded-xl bg-green-600 flex items-center justify-center text-2xl shadow">🥗</div>
				<div>
					<h1 class="text-2xl font-bold text-gray-900 m-0 leading-tight">Nutrition Info</h1>
					<p class="text-sm text-gray-500 m-0"><?php esc_html_e( 'Manage nutritional information and customer profiles', 'nutrition-info-woocommerce' ); ?></p>
				</div>
			</div>

			<?php if ( $saved ) : ?>
			<div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-6 text-sm font-medium">
				<svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
				<?php esc_html_e( 'Settings saved successfully.', 'nutrition-info-woocommerce' ); ?>
			</div>
			<?php endif; ?>

			<!-- ── Feature cards ── -->
			<div class="grid grid-cols-3 gap-4 mb-8">
				<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
					<div class="text-2xl mb-3">📊</div>
					<h3 class="font-semibold text-gray-800 text-sm m-0 mb-1"><?php esc_html_e( 'Nutrition Table', 'nutrition-info-woocommerce' ); ?></h3>
					<p class="text-xs text-gray-500 m-0 leading-relaxed"><?php esc_html_e( 'Calories, protein, carbs and fat on the product page.', 'nutrition-info-woocommerce' ); ?></p>
				</div>
				<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
					<div class="text-2xl mb-3">⚠️</div>
					<h3 class="font-semibold text-gray-800 text-sm m-0 mb-1"><?php esc_html_e( 'Allergen Icons', 'nutrition-info-woocommerce' ); ?></h3>
					<p class="text-xs text-gray-500 m-0 leading-relaxed"><?php esc_html_e( '14 allergens with visual icons in the shop and product pages.', 'nutrition-info-woocommerce' ); ?></p>
				</div>
				<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
					<div class="text-2xl mb-3">👤</div>
					<h3 class="font-semibold text-gray-800 text-sm m-0 mb-1"><?php esc_html_e( 'Nutritional Profile', 'nutrition-info-woocommerce' ); ?></h3>
					<p class="text-xs text-gray-500 m-0 leading-relaxed"><?php esc_html_e( 'Collects customer data on registration and calculates their TDEE.', 'nutrition-info-woocommerce' ); ?></p>
				</div>
			</div>

			<!-- ── Form ── -->
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="niw_save_settings" />
				<?php wp_nonce_field( 'niw_save_settings' ); ?>

				<!-- Tabla Nutricional -->
				<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
					<div class="flex items-center gap-2 mb-5 pb-4 border-b border-gray-100">
						<span class="text-lg">📊</span>
						<h2 class="text-base font-semibold text-gray-800 m-0"><?php esc_html_e( 'Nutrition Table', 'nutrition-info-woocommerce' ); ?></h2>
					</div>
					<p class="text-sm text-gray-500 mb-5 m-0"><?php esc_html_e( 'Configure how and where the nutrition table is displayed on product pages. Per-product values are edited from the product editor.', 'nutrition-info-woocommerce' ); ?></p>

					<div class="grid grid-cols-2 gap-5">
						<div>
							<label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5" for="niw_title">
								<?php esc_html_e( 'Section title', 'nutrition-info-woocommerce' ); ?>
							</label>
							<input
								type="text"
								id="niw_title"
								name="wc_nutrients_settings_tab_title"
								value="<?php echo esc_attr( $title ); ?>"
								class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
							/>
							<p class="text-xs text-gray-400 mt-1"><?php esc_html_e( 'Title text for the nutritional block or tab.', 'nutrition-info-woocommerce' ); ?></p>
						</div>
						<div>
							<label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5" for="niw_per_volume">
								<?php esc_html_e( 'Reference text', 'nutrition-info-woocommerce' ); ?>
							</label>
							<input
								type="text"
								id="niw_per_volume"
								name="wc_nutrients_settings_tab_per_volume_text"
								value="<?php echo esc_attr( $per_volume ); ?>"
								class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
							/>
							<p class="text-xs text-gray-400 mt-1"><?php esc_html_e( 'E.g. "Per 100 g" or "Per serving".', 'nutrition-info-woocommerce' ); ?></p>
						</div>
					</div>

					<div class="mt-5">
						<label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5" for="niw_position">
							<?php esc_html_e( 'Position on the product page', 'nutrition-info-woocommerce' ); ?>
						</label>
						<select
							id="niw_position"
							name="wc_nutrients_settings_tab_position"
							class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 bg-white"
						>
							<?php foreach ( $positions as $val => $label ) : ?>
							<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $position, $val ); ?>>
								<?php echo esc_html( $label ); ?>
							</option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="mt-5 flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
						<div>
							<p class="text-sm font-medium text-gray-700 m-0"><?php esc_html_e( 'Load stylesheet', 'nutrition-info-woocommerce' ); ?></p>
							<p class="text-xs text-gray-400 m-0"><?php esc_html_e( 'Disable if you use your own CSS styles.', 'nutrition-info-woocommerce' ); ?></p>
						</div>
						<label class="relative inline-flex items-center cursor-pointer ml-4">
							<input type="checkbox" name="wc_nutrients_settings_tab_styling" value="yes" class="sr-only peer" <?php checked( $styling ); ?> />
							<div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-green-400 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
						</label>
					</div>
				</div>

				<!-- Popup Nutricional -->
				<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
					<div class="flex items-center gap-2 mb-5 pb-4 border-b border-gray-100">
						<span class="text-lg">👤</span>
						<h2 class="text-base font-semibold text-gray-800 m-0"><?php esc_html_e( 'Nutritional Profile Popup', 'nutrition-info-woocommerce' ); ?></h2>
					</div>

					<p class="text-sm text-gray-500 mb-5 m-0"><?php esc_html_e( 'When active, a popup appears after registration collecting the new user\'s nutritional profile: age, height, weight, activity level, daily meals and goal. Data is saved to their profile and shown in "My Account".', 'nutrition-info-woocommerce' ); ?></p>

					<div class="grid grid-cols-3 gap-3 mb-5 text-xs text-gray-500">
						<div class="flex items-start gap-2 bg-gray-50 rounded-xl p-3">
							<span class="text-base">📅</span>
							<span><?php esc_html_e( 'Age, height and weight', 'nutrition-info-woocommerce' ); ?></span>
						</div>
						<div class="flex items-start gap-2 bg-gray-50 rounded-xl p-3">
							<span class="text-base">🏃</span>
							<span><?php esc_html_e( 'Physical activity level', 'nutrition-info-woocommerce' ); ?></span>
						</div>
						<div class="flex items-start gap-2 bg-gray-50 rounded-xl p-3">
							<span class="text-base">🎯</span>
							<span><?php esc_html_e( 'Goal: muscle gain, maintenance or fat loss', 'nutrition-info-woocommerce' ); ?></span>
						</div>
					</div>

					<div class="flex items-center justify-between bg-green-50 border border-green-100 rounded-xl px-4 py-3">
						<div>
							<p class="text-sm font-medium text-gray-700 m-0"><?php esc_html_e( 'Enable welcome popup', 'nutrition-info-woocommerce' ); ?></p>
							<p class="text-xs text-gray-400 m-0"><?php esc_html_e( 'Shown once after the user\'s first registration.', 'nutrition-info-woocommerce' ); ?></p>
						</div>
						<label class="relative inline-flex items-center cursor-pointer ml-4">
							<input type="checkbox" id="niw_popup_toggle" name="wc_nutrients_registration_popup" value="yes" class="sr-only peer" <?php checked( $popup_enabled ); ?> />
							<div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-green-400 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
						</label>
					</div>

					<!-- Preview del popup -->
					<div id="niw-popup-preview" class="mt-4 <?php echo $popup_enabled ? '' : 'hidden'; ?>">
						<p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3"><?php esc_html_e( 'Popup preview', 'nutrition-info-woocommerce' ); ?></p>
						<div class="bg-gray-100 rounded-2xl p-6 flex items-center justify-center" style="min-height:340px">
							<div class="bg-white rounded-2xl shadow-xl p-7 w-full max-w-sm">
								<h3 class="text-lg font-bold text-gray-900 m-0 mb-1">Welcome 👋</h3>
								<p class="text-xs text-gray-400 mb-5 m-0"><?php esc_html_e( 'Complete your profile to get personalised recommendations.', 'nutrition-info-woocommerce' ); ?></p>
								<div class="grid grid-cols-2 gap-3 mb-3">
									<div>
										<p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1 m-0">Age</p>
										<div class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-400 bg-gray-50">Years</div>
									</div>
									<div>
										<p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1 m-0">Height</p>
										<div class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-400 bg-gray-50">cm</div>
									</div>
									<div>
										<p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1 m-0">Weight</p>
										<div class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-400 bg-gray-50">kg</div>
									</div>
									<div>
										<p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1 m-0">Meals/day</p>
										<div class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-400 bg-gray-50">No. of meals</div>
									</div>
								</div>
								<div class="mb-3">
									<p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1 m-0">Activity Level</p>
									<div class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-400 bg-gray-50 flex justify-between items-center">
										<span>— Select —</span>
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
									</div>
								</div>
								<div class="mb-5">
									<p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1 m-0">Goal</p>
									<div class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-400 bg-gray-50 flex justify-between items-center">
										<span>— Select —</span>
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
									</div>
								</div>
								<div class="bg-green-600 text-white text-center text-sm font-semibold py-2.5 rounded-full cursor-default"><?php esc_html_e( 'Save and continue', 'nutrition-info-woocommerce' ); ?></div>
								<p class="text-center text-xs text-gray-400 mt-3 mb-0"><?php esc_html_e( 'Skip for now', 'nutrition-info-woocommerce' ); ?></p>
							</div>
						</div>
					</div>

				</div>

				<!-- Shortcode -->
				<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
					<div class="flex items-center gap-2 mb-4 pb-4 border-b border-gray-100">
						<span class="text-lg">🔧</span>
						<h2 class="text-base font-semibold text-gray-800 m-0"><?php esc_html_e( 'Shortcode', 'nutrition-info-woocommerce' ); ?></h2>
					</div>
					<p class="text-sm text-gray-500 m-0">
						<?php esc_html_e( 'Use the shortcode to place the nutrition table manually when position is set to "Hidden":', 'nutrition-info-woocommerce' ); ?>
					</p>
					<div class="mt-3 flex items-center gap-3">
						<code class="bg-gray-900 text-green-400 px-4 py-2 rounded-lg text-sm font-mono select-all">[nutritiontable]</code>
						<span class="text-xs text-gray-400"><?php esc_html_e( 'Copy and paste into any page or post.', 'nutrition-info-woocommerce' ); ?></span>
					</div>
				</div>

				<!-- Save button -->
				<div class="flex justify-end">
					<button
						type="submit"
						class="bg-green-600 hover:bg-green-700 text-white font-semibold px-8 py-2.5 rounded-full text-sm transition-colors cursor-pointer border-0"
					>
						<?php esc_html_e( 'Save settings', 'nutrition-info-woocommerce' ); ?>
					</button>
				</div>

			</form>
		</div>
		</div>

		<script>
		( function() {
			const toggle  = document.getElementById( 'niw_popup_toggle' );
			const preview = document.getElementById( 'niw-popup-preview' );
			if ( ! toggle || ! preview ) return;

			toggle.addEventListener( 'change', function() {
				if ( toggle.checked ) {
					preview.classList.remove( 'hidden' );
				} else {
					preview.classList.add( 'hidden' );
				}
			} );
		} )();
		</script>
		<?php
	}

	/**
	 * Get settings definitions (kept for filter compatibility).
	 *
	 * @return array
	 */
	public static function get_settings(): array {
		return apply_filters( 'wc_nutrients_settings_tab_settings', array() );
	}
}
