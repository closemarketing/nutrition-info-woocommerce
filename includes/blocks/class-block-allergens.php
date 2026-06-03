<?php
/**
 * Block: Allergens
 *
 * Registers and renders the niw/allergens Gutenberg block.
 * Uses the same allergen list, PNG icons and meta keys as manage-menus.
 *
 * @package nutrition-info-woocommerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gutenberg block that displays allergens for a WooCommerce product.
 */
class NIW_Block_Allergens {

	public function __construct() {
		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_route' ) );
		add_filter( 'block_categories_all', array( $this, 'register_block_category' ), 10, 2 );
	}

	public function register_block_category( $categories ) {
		foreach ( $categories as $cat ) {
			if ( 'niw-nutrition' === $cat['slug'] ) {
				return $categories;
			}
		}
		return array_merge(
			array( array( 'slug' => 'niw-nutrition', 'title' => __( 'Nutrition Info', 'nutrition-info-woocommerce' ), 'icon' => 'carrot' ) ),
			$categories
		);
	}

	public function register_block() {
		wp_register_style( 'niw-blocks', NIW_PLUGIN_URL . 'assets/css/blocks.css', array(), NIW_BUNDLE_VERSION );

		register_block_type(
			'niw/allergens',
			array(
				'api_version'     => 3,
				'attributes'      => array(
					'productId'       => array( 'type' => 'number',  'default' => 0 ),
					'headerBgColor'   => array( 'type' => 'string',  'default' => '#1e1e1e' ),
					'headerTextColor' => array( 'type' => 'string',  'default' => '#ffffff' ),
					'rowBgColor'      => array( 'type' => 'string',  'default' => '#ffffff' ),
					'rowTextColor'    => array( 'type' => 'string',  'default' => '#1e1e1e' ),
					'iconSize'        => array( 'type' => 'number',  'default' => 32 ),
					'showIcons'       => array( 'type' => 'boolean', 'default' => true ),
				),
				'render_callback' => array( $this, 'render' ),
				'editor_script'   => 'niw-block-allergens',
				'style'           => 'niw-blocks',
			)
		);
	}

	public function register_rest_route() {
		register_rest_route(
			'niw/v1',
			'/render/allergens',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'rest_render' ),
				'permission_callback' => function () { return current_user_can( 'edit_posts' ); },
				'args'                => array(
					'product_id'   => array( 'required' => true,  'type' => 'integer', 'minimum' => 1, 'sanitize_callback' => 'absint' ),
					'header_bg'    => array( 'required' => false, 'type' => 'string',  'sanitize_callback' => 'sanitize_text_field' ),
					'header_text'  => array( 'required' => false, 'type' => 'string',  'sanitize_callback' => 'sanitize_text_field' ),
					'row_bg'       => array( 'required' => false, 'type' => 'string',  'sanitize_callback' => 'sanitize_text_field' ),
					'row_text'     => array( 'required' => false, 'type' => 'string',  'sanitize_callback' => 'sanitize_text_field' ),
					'icon_size'    => array( 'required' => false, 'type' => 'integer', 'sanitize_callback' => 'absint' ),
					'show_icons'   => array( 'required' => false, 'type' => 'string',  'sanitize_callback' => 'sanitize_text_field' ),
				),
			)
		);
	}

	public function rest_render( $request ) {
		$opts = array(
			'header_bg'   => $request->get_param( 'header_bg' )   ?: '#1e1e1e',
			'header_text' => $request->get_param( 'header_text' ) ?: '#ffffff',
			'row_bg'      => $request->get_param( 'row_bg' )      ?: '#ffffff',
			'row_text'    => $request->get_param( 'row_text' )    ?: '#1e1e1e',
			'icon_size'   => absint( $request->get_param( 'icon_size' ) ) ?: 32,
			'show_icons'  => '0' !== $request->get_param( 'show_icons' ),
		);
		return new WP_REST_Response( array(
			'html' => $this->build_html( absint( $request->get_param( 'product_id' ) ), $opts ),
		) );
	}

	public function render( $attributes ) {
		$product_id = intval( $attributes['productId'] ?? 0 );
		if ( ! $product_id ) {
			return '<p>' . esc_html__( 'Selecciona un producto para mostrar sus alérgenos.', 'nutrition-info-woocommerce' ) . '</p>';
		}
		$opts = array(
			'header_bg'   => sanitize_text_field( $attributes['headerBgColor']   ?? '#1e1e1e' ),
			'header_text' => sanitize_text_field( $attributes['headerTextColor'] ?? '#ffffff' ),
			'row_bg'      => sanitize_text_field( $attributes['rowBgColor']      ?? '#ffffff' ),
			'row_text'    => sanitize_text_field( $attributes['rowTextColor']    ?? '#1e1e1e' ),
			'icon_size'   => absint( $attributes['iconSize']  ?? 32 ) ?: 32,
			'show_icons'  => (bool) ( $attributes['showIcons'] ?? true ),
		);
		return $this->build_html( $product_id, $opts );
	}

	private function build_html( $product_id, $opts ) {
		$allergens  = NIW_Data::get_allergens();
		$icons_url  = NIW_PLUGIN_URL . 'assets/icons/';
		$active     = array();

		foreach ( $allergens as $key => $label ) {
			if ( get_post_meta( $product_id, 'food_allegerns_' . $key, true ) ) {
				$active[ $key ] = $label;
			}
		}

		if ( empty( $active ) ) {
			return '<p>' . esc_html__( 'Este producto no tiene alérgenos declarados.', 'nutrition-info-woocommerce' ) . '</p>';
		}

		$icon_size    = absint( $opts['icon_size'] );
		$header_style = 'background:' . esc_attr( $opts['header_bg'] ) . ';color:' . esc_attr( $opts['header_text'] ) . ';padding:10px 12px;text-align:left;font-weight:700;';
		$row_style    = 'background:' . esc_attr( $opts['row_bg'] ) . ';color:' . esc_attr( $opts['row_text'] ) . ';';

		$html  = '<table class="manmen-allergens-table">';
		$html .= '<thead><tr><th colspan="2" style="' . $header_style . '">' . esc_html__( 'Alérgenos', 'nutrition-info-woocommerce' ) . '</th></tr></thead>';
		$html .= '<tbody>';

		foreach ( $active as $key => $label ) {
			$html .= '<tr><td style="' . $row_style . 'padding:8px 12px;border-bottom:1px solid #e8e8e8;vertical-align:middle;">';
			if ( $opts['show_icons'] ) {
				$icon  = esc_url( $icons_url . 'icon-' . $key . '.png' );
				$html .= '<img src="' . $icon . '" alt="" style="width:' . $icon_size . 'px;height:' . $icon_size . 'px;vertical-align:middle;margin-right:8px;">';
			}
			$html .= esc_html( $label );
			$html .= '</td></tr>';
		}

		$html .= '</tbody></table>';
		return $html;
	}

	public function enqueue_editor_assets() {
		wp_enqueue_script(
			'niw-block-allergens',
			NIW_PLUGIN_URL . 'assets/js/block-allergens.js',
			array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-api-fetch', 'wp-block-editor', 'wp-i18n' ),
			NIW_BUNDLE_VERSION,
			true
		);
		wp_localize_script( 'niw-block-allergens', 'niwBlockData', array(
			'products'  => $this->get_product_options(),
			'iconsUrl'  => NIW_PLUGIN_URL . 'assets/icons/',
			'allergens' => NIW_Data::get_allergens(),
		) );
	}

	private function get_product_options() {
		$posts   = get_posts( array( 'post_type' => 'product', 'posts_per_page' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC' ) );
		$options = array( array( 'value' => 0, 'label' => __( '— Selecciona un producto —', 'nutrition-info-woocommerce' ) ) );
		foreach ( $posts as $post ) {
			$options[] = array( 'value' => $post->ID, 'label' => $post->post_title );
		}
		return $options;
	}
}
