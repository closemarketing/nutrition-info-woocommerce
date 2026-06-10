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
		$title          = get_option( 'wc_nutrients_settings_tab_title', __( 'Información Nutricional', 'nutrition-info-woocommerce' ) );
		$per_volume     = get_option( 'wc_nutrients_settings_tab_per_volume_text', 'Por 100 g' );
		$position       = get_option( 'wc_nutrients_settings_tab_position', 'tab' );
		$styling        = 'yes' === get_option( 'wc_nutrients_settings_tab_styling', 'yes' );
		$popup_enabled  = 'yes' === get_option( 'wc_nutrients_registration_popup', 'no' );

		$positions = array(
			'tab'                => __( 'Pestaña independiente', 'nutrition-info-woocommerce' ),
			'in_description_tab' => __( 'Dentro de la pestaña de descripción', 'nutrition-info-woocommerce' ),
			'after_price'        => __( 'Tras el precio', 'nutrition-info-woocommerce' ),
			'after_excerpt'      => __( 'Tras el resumen', 'nutrition-info-woocommerce' ),
			'after_add_to_cart'  => __( 'Tras el botón "Añadir al carrito"', 'nutrition-info-woocommerce' ),
			'after_meta'         => __( 'Tras los metadatos del producto', 'nutrition-info-woocommerce' ),
			'hidden'             => __( 'Oculta — colocación manual con shortcode', 'nutrition-info-woocommerce' ),
		);
		?>
		<div class="wrap" style="font-family:inherit">
		<div class="max-w-4xl mx-auto py-8 px-4">

			<!-- ── Header ── -->
			<div class="flex items-center gap-4 mb-8">
				<div class="w-12 h-12 rounded-xl bg-green-600 flex items-center justify-center text-2xl shadow">🥗</div>
				<div>
					<h1 class="text-2xl font-bold text-gray-900 m-0 leading-tight">Nutrition Info</h1>
					<p class="text-sm text-gray-500 m-0"><?php esc_html_e( 'Gestiona la información nutricional y el perfil de tus clientes', 'nutrition-info-woocommerce' ); ?></p>
				</div>
			</div>

			<?php if ( $saved ) : ?>
			<div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-6 text-sm font-medium">
				<svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
				<?php esc_html_e( 'Ajustes guardados correctamente.', 'nutrition-info-woocommerce' ); ?>
			</div>
			<?php endif; ?>

			<!-- ── Feature cards ── -->
			<div class="grid grid-cols-3 gap-4 mb-8">
				<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
					<div class="text-2xl mb-3">📊</div>
					<h3 class="font-semibold text-gray-800 text-sm m-0 mb-1"><?php esc_html_e( 'Tabla Nutricional', 'nutrition-info-woocommerce' ); ?></h3>
					<p class="text-xs text-gray-500 m-0 leading-relaxed"><?php esc_html_e( 'Calorías, proteínas, hidratos y grasas en la ficha de producto.', 'nutrition-info-woocommerce' ); ?></p>
				</div>
				<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
					<div class="text-2xl mb-3">⚠️</div>
					<h3 class="font-semibold text-gray-800 text-sm m-0 mb-1"><?php esc_html_e( 'Iconos de Alérgenos', 'nutrition-info-woocommerce' ); ?></h3>
					<p class="text-xs text-gray-500 m-0 leading-relaxed"><?php esc_html_e( '14 alérgenos con iconos visuales en tienda y ficha de producto.', 'nutrition-info-woocommerce' ); ?></p>
				</div>
				<div class="bg-white rounded-2xl border border-blue-100 shadow-sm p-5 relative overflow-hidden">
					<div class="absolute top-3 right-3">
						<span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Nuevo</span>
					</div>
					<div class="text-2xl mb-3">👤</div>
					<h3 class="font-semibold text-gray-800 text-sm m-0 mb-1"><?php esc_html_e( 'Perfil Nutricional', 'nutrition-info-woocommerce' ); ?></h3>
					<p class="text-xs text-gray-500 m-0 leading-relaxed"><?php esc_html_e( 'Recoge datos del cliente al registrarse y calcula su TDEE.', 'nutrition-info-woocommerce' ); ?></p>
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
						<h2 class="text-base font-semibold text-gray-800 m-0"><?php esc_html_e( 'Tabla Nutricional', 'nutrition-info-woocommerce' ); ?></h2>
					</div>
					<p class="text-sm text-gray-500 mb-5 m-0"><?php esc_html_e( 'Configura cómo y dónde se muestra la tabla nutricional en las páginas de producto. Los valores por producto se editan desde el editor de producto.', 'nutrition-info-woocommerce' ); ?></p>

					<div class="grid grid-cols-2 gap-5">
						<div>
							<label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5" for="niw_title">
								<?php esc_html_e( 'Título de la sección', 'nutrition-info-woocommerce' ); ?>
							</label>
							<input
								type="text"
								id="niw_title"
								name="wc_nutrients_settings_tab_title"
								value="<?php echo esc_attr( $title ); ?>"
								class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
							/>
							<p class="text-xs text-gray-400 mt-1"><?php esc_html_e( 'Texto del título del bloque o pestaña nutricional.', 'nutrition-info-woocommerce' ); ?></p>
						</div>
						<div>
							<label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5" for="niw_per_volume">
								<?php esc_html_e( 'Texto de referencia', 'nutrition-info-woocommerce' ); ?>
							</label>
							<input
								type="text"
								id="niw_per_volume"
								name="wc_nutrients_settings_tab_per_volume_text"
								value="<?php echo esc_attr( $per_volume ); ?>"
								class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
							/>
							<p class="text-xs text-gray-400 mt-1"><?php esc_html_e( 'Ej: "Por 100 g" o "Por ración".', 'nutrition-info-woocommerce' ); ?></p>
						</div>
					</div>

					<div class="mt-5">
						<label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5" for="niw_position">
							<?php esc_html_e( 'Posición en la ficha de producto', 'nutrition-info-woocommerce' ); ?>
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
							<p class="text-sm font-medium text-gray-700 m-0"><?php esc_html_e( 'Cargar hoja de estilos', 'nutrition-info-woocommerce' ); ?></p>
							<p class="text-xs text-gray-400 m-0"><?php esc_html_e( 'Desactívalo si usas tus propios estilos CSS.', 'nutrition-info-woocommerce' ); ?></p>
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
						<h2 class="text-base font-semibold text-gray-800 m-0"><?php esc_html_e( 'Popup de Perfil Nutricional', 'nutrition-info-woocommerce' ); ?></h2>
						<span class="ml-2 text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full"><?php esc_html_e( 'Nuevo', 'nutrition-info-woocommerce' ); ?></span>
					</div>

					<p class="text-sm text-gray-500 mb-5 m-0"><?php esc_html_e( 'Cuando está activo, tras el registro aparece un popup que recoge el perfil nutricional del nuevo usuario: edad, altura, peso, nivel de actividad, nº de comidas diarias y objetivo. Los datos se guardan en su perfil y se muestran en "Mi Cuenta".', 'nutrition-info-woocommerce' ); ?></p>

					<div class="grid grid-cols-3 gap-3 mb-5 text-xs text-gray-500">
						<div class="flex items-start gap-2 bg-gray-50 rounded-xl p-3">
							<span class="text-base">📅</span>
							<span><?php esc_html_e( 'Edad, altura y peso', 'nutrition-info-woocommerce' ); ?></span>
						</div>
						<div class="flex items-start gap-2 bg-gray-50 rounded-xl p-3">
							<span class="text-base">🏃</span>
							<span><?php esc_html_e( 'Nivel de actividad física', 'nutrition-info-woocommerce' ); ?></span>
						</div>
						<div class="flex items-start gap-2 bg-gray-50 rounded-xl p-3">
							<span class="text-base">🎯</span>
							<span><?php esc_html_e( 'Objetivo: masa, mantenimiento o pérdida de grasa', 'nutrition-info-woocommerce' ); ?></span>
						</div>
					</div>

					<div class="flex items-center justify-between bg-green-50 border border-green-100 rounded-xl px-4 py-3">
						<div>
							<p class="text-sm font-medium text-gray-700 m-0"><?php esc_html_e( 'Activar popup de bienvenida', 'nutrition-info-woocommerce' ); ?></p>
							<p class="text-xs text-gray-400 m-0"><?php esc_html_e( 'Se muestra una sola vez tras el primer registro del usuario.', 'nutrition-info-woocommerce' ); ?></p>
						</div>
						<label class="relative inline-flex items-center cursor-pointer ml-4">
							<input type="checkbox" name="wc_nutrients_registration_popup" value="yes" class="sr-only peer" <?php checked( $popup_enabled ); ?> />
							<div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-green-400 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
						</label>
					</div>
				</div>

				<!-- Shortcode -->
				<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
					<div class="flex items-center gap-2 mb-4 pb-4 border-b border-gray-100">
						<span class="text-lg">🔧</span>
						<h2 class="text-base font-semibold text-gray-800 m-0"><?php esc_html_e( 'Shortcode', 'nutrition-info-woocommerce' ); ?></h2>
					</div>
					<p class="text-sm text-gray-500 m-0">
						<?php esc_html_e( 'Usa el shortcode para colocar la tabla nutricional manualmente cuando la posición está en "Oculta":', 'nutrition-info-woocommerce' ); ?>
					</p>
					<div class="mt-3 flex items-center gap-3">
						<code class="bg-gray-900 text-green-400 px-4 py-2 rounded-lg text-sm font-mono select-all">[nutritiontable]</code>
						<span class="text-xs text-gray-400"><?php esc_html_e( 'Copiar y pegar en cualquier página o entrada.', 'nutrition-info-woocommerce' ); ?></span>
					</div>
				</div>

				<!-- Save button -->
				<div class="flex justify-end">
					<button
						type="submit"
						class="bg-green-600 hover:bg-green-700 text-white font-semibold px-8 py-2.5 rounded-full text-sm transition-colors cursor-pointer border-0"
					>
						<?php esc_html_e( 'Guardar ajustes', 'nutrition-info-woocommerce' ); ?>
					</button>
				</div>

			</form>
		</div>
		</div>
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
