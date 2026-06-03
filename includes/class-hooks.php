<?php
/**
 * Plugin hooks registration.
 *
 * @package CLOSE\NutritionInfo
 */

namespace CLOSE\NutritionInfo;

defined( 'ABSPATH' ) || exit;

/**
 * Registers all frontend hooks for the plugin.
 */
class Hooks {

	/**
	 * Allowed HTML tags for SVG output.
	 *
	 * @var array<string, array<string, bool>>
	 */
	private array $allowed_tags_svg;

	/**
	 * Bootstrap hooks.
	 */
	public function __construct() {
		$kses_defaults = wp_kses_allowed_html( 'post' );
		$svg_args      = array(
			'svg'   => array(
				'class'           => true,
				'aria-hidden'     => true,
				'aria-labelledby' => true,
				'role'            => true,
				'xmlns'           => true,
				'width'           => true,
				'height'          => true,
				'viewbox'         => true,
			),
			'g'     => array( 'fill' => true ),
			'title' => array( 'title' => true ),
			'path'  => array(
				'd'    => true,
				'fill' => true,
			),
		);

		$this->allowed_tags_svg = array_merge( $kses_defaults, $svg_args );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'add_allergens_icon' ), 5 );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'template_loop_product_replaced_thumb' ), 10 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'add_special_allergens_icon_single_product' ), 6 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'add_allergens_icon_single_product' ), 10 );

		$this->register_position_hooks();
	}

	/**
	 * Enqueue frontend stylesheet.
	 */
	public function enqueue_styles(): void {
		wp_enqueue_style( 'niw-styles', NIW_PLUGIN_URL . 'includes/css/styles.css', array(), NIW_BUNDLE_VERSION, 'all' );
	}

	/**
	 * Register product summary hooks based on the configured position setting.
	 */
	private function register_position_hooks(): void {
		$position_priorities = array(
			'after_product_summary' => 45,
			'after_add_to_cart'     => 35,
			'after_excerpt'         => 25,
			'after_price'           => 15,
		);

		$position = get_option( 'wc_nutrients_settings_tab_position' );

		if ( isset( $position_priorities[ $position ] ) ) {
			$priority = $position_priorities[ $position ];
			add_action( 'woocommerce_single_product_summary', __NAMESPACE__ . '\\niw_nutrition_info', $priority );
			add_action( 'woocommerce_single_product_summary', __NAMESPACE__ . '\\niw_composition_info', $priority );
		}

		if ( 'in_description_tab' === $position ) {
			add_filter( 'woocommerce_product_tabs', array( $this, 'override_description_tab' ), 98 );
		}
	}

	/**
	 * Override description tab callback to include nutrition info.
	 *
	 * @param array<string, mixed> $tabs Product tabs.
	 * @return array<string, mixed>
	 */
	public function override_description_tab( array $tabs ): array {
		$tabs['description']['callback'] = array( $this, 'render_description_tab_content' );
		return $tabs;
	}

	/**
	 * Render custom description tab with nutrition and composition info appended.
	 */
	public function render_description_tab_content(): void {
		$heading = esc_html( apply_filters( 'niwcommerce_product_description_heading', __( 'Description', 'woocommerce' ) ) );
		if ( $heading ) {
			echo '<h2>' . esc_html( $heading ) . '</h2>';
		}
		the_content();
		niw_nutrition_info();
		niw_composition_info();
	}

	/**
	 * Show allergen icons in the product loop (after title).
	 */
	public function add_allergens_icon(): void {
		$all_allergens = new Allergens();
		echo "<div class='niw_icon_allergen_product'>";
		foreach ( $all_allergens->show_allergens_name() as $key => $value ) {
			$allergens_active = get_post_meta( get_the_ID(), 'niw_all_' . $key, true );
			if ( 'yes' === $allergens_active ) {
				echo '<div class="niw_svg_container"><div class="niw_svg_container_span">' . esc_html( $value ) . '</div>';
				echo wp_kses( $all_allergens->show_allergen_svg( $key ), $this->allowed_tags_svg );
				echo '</div>';
			}
		}
		echo '</div>';
	}

	/**
	 * Show vegan icon on single product page (priority 6).
	 */
	public function add_special_allergens_icon_single_product(): void {
		$all_allergens  = new Allergens();
		$allergen_vegan = get_post_meta( get_the_ID(), 'niw_all_vegan', true );
		echo "<div class='niw_icon_allergen_product'>";
		if ( 'yes' === $allergen_vegan ) {
			echo '<div class="niw_svg_container"><div class="niw_svg_container_span">' . esc_html__( 'Vegan', 'nutrition-info-woocommerce' ) . '</div>';
			echo wp_kses( $all_allergens->show_allergen_svg_vegan(), $this->allowed_tags_svg );
			echo '</div>';
		}
		echo '</div>';
	}

	/**
	 * Show all active allergen icons on single product page (priority 10).
	 */
	public function add_allergens_icon_single_product(): void {
		$all_allergens = new Allergens();
		echo "<div class='niw_icon_allergen_product'>";
		foreach ( $all_allergens->show_allergens_name() as $key => $value ) {
			$allergens_active = get_post_meta( get_the_ID(), 'niw_all_' . $key, true );
			if ( 'yes' === $allergens_active ) {
				echo '<div class="niw_svg_container"><div class="niw_svg_container_span">' . esc_html( $value ) . '</div>';
				echo wp_kses( $all_allergens->show_allergen_svg( $key ), $this->allowed_tags_svg );
				echo '</div>';
			}
		}
		echo '</div>';
	}

	/**
	 * Show vegan icon before product thumbnail in shop loop.
	 */
	public function template_loop_product_replaced_thumb(): void {
		$all_allergens  = new Allergens();
		$allergen_vegan = get_post_meta( get_the_ID(), 'niw_all_vegan', true );
		echo '<div class="niw_icons_product">';
		if ( 'yes' === $allergen_vegan ) {
			echo '<div class="niw_svg_container_span">' . esc_html__( 'Vegan', 'nutrition-info-woocommerce' ) . '</div>';
			echo wp_kses( $all_allergens->show_allergen_svg_vegan(), $this->allowed_tags_svg );
		}
		echo '</div>';
	}
}
