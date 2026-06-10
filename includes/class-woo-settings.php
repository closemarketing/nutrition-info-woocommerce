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
		add_action( 'admin_head', array( __CLASS__, 'inline_styles' ) );
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

		$fields = array(
			'wc_nutrients_settings_tab_title'        => 'sanitize_text_field',
			'wc_nutrients_settings_tab_per_volume_text' => 'sanitize_text_field',
			'wc_nutrients_settings_tab_position'     => 'sanitize_text_field',
		);

		foreach ( $fields as $key => $cb ) {
			$value = isset( $_POST[ $key ] ) ? $cb( wp_unslash( $_POST[ $key ] ) ) : '';
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
	 * Render the full settings page.
	 */
	public static function render_page(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$saved = isset( $_GET['saved'] ) && '1' === $_GET['saved'];

		self::render_feature_overview();

		if ( $saved ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Ajustes guardados.', 'nutrition-info-woocommerce' ) . '</p></div>';
		}
		?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="niw_save_settings" />
			<?php wp_nonce_field( 'niw_save_settings' ); ?>
			<?php woocommerce_admin_fields( self::get_settings() ); ?>
			<?php submit_button( __( 'Guardar ajustes', 'nutrition-info-woocommerce' ) ); ?>
		</form>
		<?php
	}

	/**
	 * Output inline styles for the settings page.
	 */
	public static function inline_styles(): void {
		$screen = get_current_screen();
		if ( ! $screen || 'woocommerce_page_niw-settings' !== $screen->id ) {
			return;
		}
		?>
		<style>
			.niw-feature-grid {
				display: grid;
				grid-template-columns: repeat(3, 1fr);
				gap: 16px;
				margin: 20px 0 30px;
				max-width: 900px;
			}
			.niw-feature-card {
				background: #fff;
				border: 1px solid #e0e0e0;
				border-radius: 8px;
				padding: 18px 20px;
			}
			.niw-feature-card h4 {
				margin: 0 0 6px;
				font-size: 13px;
				font-weight: 600;
				display: flex;
				align-items: center;
				gap: 6px;
			}
			.niw-feature-card p {
				margin: 0;
				font-size: 12px;
				color: #666;
				line-height: 1.5;
			}
			.niw-feature-icon {
				font-size: 16px;
			}
			.niw-badge {
				display: inline-block;
				background: #e8f5e9;
				color: #2e7d32;
				font-size: 10px;
				font-weight: 600;
				padding: 2px 7px;
				border-radius: 20px;
				text-transform: uppercase;
				letter-spacing: .04em;
				vertical-align: middle;
				margin-left: 4px;
			}
			.niw-badge--new {
				background: #e3f2fd;
				color: #1565c0;
			}
			.niw-popup-toggle-wrap {
				background: #f0fdf4;
				border: 1.5px solid #bbf7d0;
				border-radius: 8px;
				padding: 16px 20px;
				max-width: 700px;
				margin-top: 8px;
			}
			.niw-popup-toggle-wrap p {
				margin: 6px 0 0;
				font-size: 12.5px;
				color: #555;
				line-height: 1.6;
			}
		</style>
		<?php
	}

	/**
	 * Render the feature overview cards at the top of the tab.
	 */
	private static function render_feature_overview(): void {
		?>
		<h2 style="margin-top:24px;"><?php esc_html_e( 'Nutrition Info for WooCommerce', 'nutrition-info-woocommerce' ); ?></h2>
		<p style="color:#666;max-width:700px;margin-bottom:20px;">
			<?php esc_html_e( 'Plugin para mostrar información nutricional y alérgenos en tus productos WooCommerce, y recopilar el perfil nutricional de tus clientes.', 'nutrition-info-woocommerce' ); ?>
		</p>

		<div class="niw-feature-grid">
			<div class="niw-feature-card">
				<h4>
					<span class="niw-feature-icon">🥗</span>
					<?php esc_html_e( 'Tabla Nutricional', 'nutrition-info-woocommerce' ); ?>
				</h4>
				<p><?php esc_html_e( 'Muestra calorías, proteínas, hidratos, grasas y más en la ficha de cada producto. Configura los valores desde el editor de producto.', 'nutrition-info-woocommerce' ); ?></p>
			</div>

			<div class="niw-feature-card">
				<h4>
					<span class="niw-feature-icon">⚠️</span>
					<?php esc_html_e( 'Iconos de Alérgenos', 'nutrition-info-woocommerce' ); ?>
				</h4>
				<p><?php esc_html_e( 'Muestra iconos de los 14 alérgenos principales en la tienda y en la ficha de producto. Actívalos por producto desde el editor.', 'nutrition-info-woocommerce' ); ?></p>
			</div>

			<div class="niw-feature-card">
				<h4>
					<span class="niw-feature-icon">👤</span>
					<?php esc_html_e( 'Perfil Nutricional', 'nutrition-info-woocommerce' ); ?>
					<span class="niw-badge niw-badge--new"><?php esc_html_e( 'Nuevo', 'nutrition-info-woocommerce' ); ?></span>
				</h4>
				<p><?php esc_html_e( 'Recoge edad, altura, peso, nivel de actividad, comidas diarias y objetivo de cada cliente al registrarse. Datos disponibles en su perfil.', 'nutrition-info-woocommerce' ); ?></p>
			</div>
		</div>
		<?php
	}

	/**
	 * Get all settings definitions.
	 *
	 * @return array
	 */
	public static function get_settings(): array {
		$settings = array(

			// ── Tabla Nutricional ─────────────────────────────────────────────
			'nutrition_section_title' => array(
				'name' => __( 'Tabla Nutricional', 'nutrition-info-woocommerce' ),
				'type' => 'title',
				'desc' => __( 'Configura cómo y dónde se muestra la tabla de información nutricional en las páginas de producto. Los valores por producto se editan desde el panel de cada producto.', 'nutrition-info-woocommerce' ),
				'id'   => 'wc_nutrients_nutrition_section_title',
			),
			'title' => array(
				'name'    => __( 'Título de la sección', 'nutrition-info-woocommerce' ),
				'type'    => 'text',
				'desc'    => __( 'Texto que aparece como título del bloque o pestaña nutricional.', 'nutrition-info-woocommerce' ),
				'id'      => 'wc_nutrients_settings_tab_title',
				'default' => __( 'Información Nutricional', 'nutrition-info-woocommerce' ),
			),
			'per_volume_text' => array(
				'name'    => __( 'Texto de referencia', 'nutrition-info-woocommerce' ),
				'type'    => 'text',
				'desc'    => __( 'Ej: "Por 100 g" o "Por ración". Aparece junto a los valores nutricionales.', 'nutrition-info-woocommerce' ),
				'id'      => 'wc_nutrients_settings_tab_per_volume_text',
				'default' => __( 'Por 100 g', 'nutrition-info-woocommerce' ),
			),
			'position' => array(
				'name'    => __( 'Posición en la ficha', 'nutrition-info-woocommerce' ),
				'type'    => 'select',
				'desc'    => __( 'Dónde mostrar la tabla nutricional dentro de la página de producto.', 'nutrition-info-woocommerce' ),
				'id'      => 'wc_nutrients_settings_tab_position',
				'options' => array(
					'tab'                => __( 'Pestaña independiente', 'nutrition-info-woocommerce' ),
					'in_description_tab' => __( 'Dentro de la pestaña de descripción', 'nutrition-info-woocommerce' ),
					'after_price'        => __( 'Tras el precio', 'nutrition-info-woocommerce' ),
					'after_excerpt'      => __( 'Tras el resumen', 'nutrition-info-woocommerce' ),
					'after_add_to_cart'  => __( 'Tras el botón "Añadir al carrito"', 'nutrition-info-woocommerce' ),
					'after_meta'         => __( 'Tras los metadatos del producto', 'nutrition-info-woocommerce' ),
					'hidden'             => __( 'Oculta (colocación manual con shortcode)', 'nutrition-info-woocommerce' ),
				),
			),
			'styling' => array(
				'name'    => __( 'Estilos CSS', 'nutrition-info-woocommerce' ),
				'type'    => 'checkbox',
				'desc'    => __( 'Cargar la hoja de estilos del plugin. Desactívalo si usas tus propios estilos.', 'nutrition-info-woocommerce' ),
				'id'      => 'wc_nutrients_settings_tab_styling',
				'default' => 'yes',
			),
			'nutrition_section_end' => array(
				'type' => 'sectionend',
				'id'   => 'wc_nutrients_nutrition_section_end',
			),

			// ── Popup de Bienvenida ───────────────────────────────────────────
			'popup_section_title' => array(
				'name' => __( 'Popup de Perfil Nutricional', 'nutrition-info-woocommerce' ),
				'type' => 'title',
				'desc' => __( 'Cuando está activo, justo después de que un usuario se registre en tu web aparece un popup con un mini-formulario para recopilar su perfil nutricional: edad, altura, peso, nivel de actividad, nº de comidas diarias y objetivo (ganar masa, mantenimiento o perder grasa). Los datos se guardan en el perfil del usuario y están disponibles en "Mi Cuenta".', 'nutrition-info-woocommerce' ),
				'id'   => 'wc_nutrients_popup_section_title',
			),
			'registration_popup' => array(
				'name'              => __( 'Activar popup de bienvenida', 'nutrition-info-woocommerce' ),
				'type'              => 'checkbox',
				'desc'              => __( 'Mostrar el formulario nutricional tras el registro de nuevos usuarios.', 'nutrition-info-woocommerce' ),
				'id'                => 'wc_nutrients_registration_popup',
				'default'           => 'no',
				'checkboxgroup'     => '',
				'show_if_checked'   => 'option',
			),
			'popup_section_end' => array(
				'type' => 'sectionend',
				'id'   => 'wc_nutrients_popup_section_end',
			),

			// ── Shortcode ─────────────────────────────────────────────────────
			'shortcode_section_title' => array(
				'name' => __( 'Shortcode', 'nutrition-info-woocommerce' ),
				'type' => 'title',
				'desc' => sprintf(
					/* translators: %s: shortcode */
					__( 'Usa %s para mostrar la tabla nutricional manualmente en cualquier página o entrada cuando la posición está en "Oculta".', 'nutrition-info-woocommerce' ),
					'<code>[nutritiontable]</code>'
				),
				'id'   => 'wc_nutrients_shortcode_section_title',
			),
			'shortcode_section_end' => array(
				'type' => 'sectionend',
				'id'   => 'wc_nutrients_shortcode_section_end',
			),
		);

		return apply_filters( 'wc_nutrients_settings_tab_settings', $settings );
	}
}
