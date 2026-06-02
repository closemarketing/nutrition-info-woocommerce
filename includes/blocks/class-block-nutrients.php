<?php
/**
 * Block: Nutritional Info
 *
 * Registers and renders the niw/nutrients Gutenberg block.
 *
 * @package nutrition-info-woocommerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gutenberg block that displays nutritional info for a WooCommerce product.
 */
class NIW_Block_Nutrients {

	/** Nutrient fields matching meta keys niw_{key}. */
	const NUTRIENT_FIELDS = array(
		array( 'key' => 'energy',            'label' => 'Energy (KJ/kcal)' ),
		array( 'key' => 'fat',               'label' => 'Fat (g)' ),
		array( 'key' => 'saturated_fat',     'label' => '- Saturated fatty acids (g)',   'sub' => true ),
		array( 'key' => 'monounsaturated_fat','label' => '- Monounsaturated fat (g)',     'sub' => true ),
		array( 'key' => 'polyunsaturated_fat','label' => '- Polyunsaturated fat (g)',     'sub' => true ),
		array( 'key' => 'carb',              'label' => 'Carbohydrate (g)' ),
		array( 'key' => 'sugar',             'label' => '- Of which sugars (g)',          'sub' => true ),
		array( 'key' => 'polyol',            'label' => '- Of which Polyols (g)',         'sub' => true ),
		array( 'key' => 'starch',            'label' => '- Of which Starch (g)',          'sub' => true ),
		array( 'key' => 'fiber',             'label' => 'Dietary Fiber (g)' ),
		array( 'key' => 'protein',           'label' => 'Protein (g)' ),
		array( 'key' => 'salt',              'label' => 'Salt (g)' ),
		array( 'key' => 'vitamin_mineral',   'label' => 'Vitamins and minerals' ),
	);

	public function __construct() {
		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_route' ) );
	}

	public function register_block() {
		wp_register_style( 'niw-blocks', NIW_PLUGIN_URL . 'assets/css/blocks.css', array(), NIW_BUNDLE_VERSION );

		register_block_type(
			'niw/nutrients',
			array(
				'api_version'     => 3,
				'attributes'      => array(
					'productId'       => array( 'type' => 'number', 'default' => 0 ),
					'headerBgColor'   => array( 'type' => 'string', 'default' => '#1e1e1e' ),
					'headerTextColor' => array( 'type' => 'string', 'default' => '#ffffff' ),
					'rowBgColor'      => array( 'type' => 'string', 'default' => '#ffffff' ),
					'rowTextColor'    => array( 'type' => 'string', 'default' => '#1e1e1e' ),
					'subTextColor'    => array( 'type' => 'string', 'default' => '#666666' ),
					'borderColor'     => array( 'type' => 'string', 'default' => '#e0e0e0' ),
					'fontSize'        => array( 'type' => 'number', 'default' => 13 ),
				),
				'render_callback' => array( $this, 'render' ),
				'editor_script'   => 'niw-block-nutrients',
				'style'           => 'niw-blocks',
			)
		);
	}

	public function register_rest_route() {
		register_rest_route(
			'niw/v1',
			'/render/nutrients',
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
					'sub_text'     => array( 'required' => false, 'type' => 'string',  'sanitize_callback' => 'sanitize_text_field' ),
					'border_color' => array( 'required' => false, 'type' => 'string',  'sanitize_callback' => 'sanitize_text_field' ),
					'font_size'    => array( 'required' => false, 'type' => 'integer', 'sanitize_callback' => 'absint' ),
				),
			)
		);
	}

	public function rest_render( $request ) {
		$opts = array(
			'header_bg'    => $request->get_param( 'header_bg' )    ?: '#1e1e1e',
			'header_text'  => $request->get_param( 'header_text' )  ?: '#ffffff',
			'row_bg'       => $request->get_param( 'row_bg' )       ?: '#ffffff',
			'row_text'     => $request->get_param( 'row_text' )     ?: '#1e1e1e',
			'sub_text'     => $request->get_param( 'sub_text' )     ?: '#666666',
			'border_color' => $request->get_param( 'border_color' ) ?: '#e0e0e0',
			'font_size'    => absint( $request->get_param( 'font_size' ) ) ?: 13,
		);
		return new WP_REST_Response( array(
			'html' => $this->build_html( absint( $request->get_param( 'product_id' ) ), $opts ),
		) );
	}

	public function render( $attributes ) {
		$product_id = intval( $attributes['productId'] ?? 0 );
		if ( ! $product_id ) {
			return '<p>' . esc_html__( 'Select a product to show its nutritional info.', 'nutrition-info-woocommerce' ) . '</p>';
		}
		$opts = array(
			'header_bg'    => sanitize_text_field( $attributes['headerBgColor']   ?? '#1e1e1e' ),
			'header_text'  => sanitize_text_field( $attributes['headerTextColor'] ?? '#ffffff' ),
			'row_bg'       => sanitize_text_field( $attributes['rowBgColor']      ?? '#ffffff' ),
			'row_text'     => sanitize_text_field( $attributes['rowTextColor']    ?? '#1e1e1e' ),
			'sub_text'     => sanitize_text_field( $attributes['subTextColor']    ?? '#666666' ),
			'border_color' => sanitize_text_field( $attributes['borderColor']     ?? '#e0e0e0' ),
			'font_size'    => absint( $attributes['fontSize'] ?? 13 ) ?: 13,
		);
		return $this->build_html( $product_id, $opts );
	}

	private function build_html( $product_id, $opts ) {
		$fs     = absint( $opts['font_size'] );
		$border = esc_attr( $opts['border_color'] );

		$header_style = 'background:' . esc_attr( $opts['header_bg'] ) . ';color:' . esc_attr( $opts['header_text'] ) . ';padding:10px 12px;text-align:left;font-weight:700;font-size:' . $fs . 'px;';
		$cell_style   = 'padding:5px 12px;border-bottom:1px solid ' . $border . ';color:' . esc_attr( $opts['row_text'] ) . ';background:' . esc_attr( $opts['row_bg'] ) . ';font-size:' . $fs . 'px;';
		$sub_style    = 'padding:5px 12px 5px 24px;border-bottom:1px solid ' . $border . ';color:' . esc_attr( $opts['sub_text'] ) . ';background:' . esc_attr( $opts['row_bg'] ) . ';font-size:' . $fs . 'px;';
		$right_style  = 'padding:5px 12px;border-bottom:1px solid ' . $border . ';text-align:right;color:#888;white-space:nowrap;background:' . esc_attr( $opts['row_bg'] ) . ';font-size:' . $fs . 'px;';

		$rows = array();
		foreach ( self::NUTRIENT_FIELDS as $field ) {
			$value = get_post_meta( $product_id, 'niw_' . $field['key'], true );
			if ( $value ) {
				$rows[] = array_merge( $field, array( 'value' => $value ) );
			}
		}

		if ( empty( $rows ) ) {
			return '<p>' . esc_html__( 'This product has no nutritional data.', 'nutrition-info-woocommerce' ) . '</p>';
		}

		$html  = '<table style="border-collapse:collapse;width:100%;font-size:' . $fs . 'px;">';
		$html .= '<thead><tr><th colspan="2" style="' . $header_style . '">';
		$html .= esc_html__( 'Nutritional Information', 'nutrition-info-woocommerce' );
		$html .= ' <span style="font-weight:400;opacity:.75;">' . esc_html__( 'per 100 g', 'nutrition-info-woocommerce' ) . '</span>';
		$html .= '</th></tr></thead><tbody>';

		foreach ( $rows as $row ) {
			$is_sub = ! empty( $row['sub'] );
			$html  .= '<tr>';
			$html  .= '<td style="' . ( $is_sub ? $sub_style : $cell_style ) . '">' . esc_html( $row['label'] ) . '</td>';
			$html  .= '<td style="' . $right_style . '">' . esc_html( $row['value'] ) . '</td>';
			$html  .= '</tr>';
		}

		$html .= '</tbody></table>';
		return $html;
	}

	public function enqueue_editor_assets() {
		wp_enqueue_script(
			'niw-block-nutrients',
			NIW_PLUGIN_URL . 'assets/js/block-nutrients.js',
			array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-api-fetch', 'wp-block-editor', 'wp-i18n' ),
			NIW_BUNDLE_VERSION,
			true
		);
		wp_localize_script( 'niw-block-nutrients', 'niwNutrientsData', array(
			'products' => $this->get_product_options(),
		) );
	}

	private function get_product_options() {
		$posts   = get_posts( array( 'post_type' => 'product', 'posts_per_page' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC' ) );
		$options = array( array( 'value' => 0, 'label' => __( '— Select a product —', 'nutrition-info-woocommerce' ) ) );
		foreach ( $posts as $post ) {
			$options[] = array( 'value' => $post->ID, 'label' => $post->post_title );
		}
		return $options;
	}
}
