( function ( blocks, element, components, blockEditor, apiFetch, i18n ) {
	var el             = element.createElement;
	var __             = i18n.__;
	var useState       = element.useState;
	var useEffect      = element.useEffect;

	var SelectControl     = components.SelectControl;
	var PanelBody         = components.PanelBody;
	var RangeControl      = components.RangeControl;
	var ColorPalette      = components.ColorPalette;
	var BaseControl       = components.BaseControl;
	var ToggleControl     = components.ToggleControl;
	var InspectorControls = blockEditor.InspectorControls;
	var useBlockProps     = blockEditor.useBlockProps;

	var products = ( window.niwBlockData && window.niwBlockData.products ) ? window.niwBlockData.products : [];

	function AllergensPreview( props ) {
		var productId   = props.productId;
		var headerBg    = props.headerBgColor   || '#1e1e1e';
		var headerText  = props.headerTextColor || '#ffffff';
		var rowBg       = props.rowBgColor      || '#ffffff';
		var rowText     = props.rowTextColor    || '#1e1e1e';
		var iconSize    = props.iconSize        || 40;
		var showIcons   = props.showIcons !== false;

		var htmlState    = useState( '' );
		var loadingState = useState( false );
		var htmlValue    = htmlState[0];
		var setHtml      = htmlState[1];
		var loadingValue = loadingState[0];
		var setLoading   = loadingState[1];

		useEffect( function () {
			if ( ! productId || productId <= 0 ) { setHtml( '' ); return; }
			setLoading( true );
			apiFetch( { path: '/niw/v1/render/allergens'
				+ '?product_id='  + productId
				+ '&header_bg='   + encodeURIComponent( headerBg )
				+ '&header_text=' + encodeURIComponent( headerText )
				+ '&row_bg='      + encodeURIComponent( rowBg )
				+ '&row_text='    + encodeURIComponent( rowText )
				+ '&icon_size='   + iconSize
				+ '&show_icons='  + ( showIcons ? '1' : '0' )
			} ).then( function ( data ) { setHtml( data.html || '' ); setLoading( false ); } )
			  .catch( function () { setHtml( '' ); setLoading( false ); } );
		}, [ productId, headerBg, headerText, rowBg, rowText, iconSize, showIcons ] );

		if ( ! productId || productId <= 0 ) {
			return el( 'div', { style: { padding: '16px', background: '#f0f0f0', textAlign: 'center' } },
				el( 'p', null, __( 'Select a product in the sidebar to see its allergens.', 'nutrition-info-woocommerce' ) )
			);
		}
		if ( loadingValue ) { return el( 'div', { style: { padding: '12px' } }, __( 'Loading…', 'nutrition-info-woocommerce' ) ); }
		return el( 'div', { dangerouslySetInnerHTML: { __html: htmlValue } } );
	}

	function edit( props ) {
		var attrs      = props.attributes;
		var set        = props.setAttributes;
		var productId  = attrs.productId;
		var blockProps = useBlockProps();

		return [
			el( InspectorControls, { key: 'inspector' },

				el( PanelBody, { title: __( 'Product', 'nutrition-info-woocommerce' ), initialOpen: true },
					el( SelectControl, {
						label: __( 'Product', 'nutrition-info-woocommerce' ), value: productId, options: products,
						__next40pxDefaultSize: true, __nextHasNoMarginBottom: true,
						onChange: function ( v ) { set( { productId: parseInt( v, 10 ) || 0 } ); },
					} )
				),

				el( PanelBody, { title: __( 'Header', 'nutrition-info-woocommerce' ), initialOpen: false },
					el( BaseControl, { label: __( 'Header background', 'nutrition-info-woocommerce' ), id: 'niw-al-hbg' },
						el( ColorPalette, { value: attrs.headerBgColor,   onChange: function ( v ) { set( { headerBgColor:   v || '#1e1e1e' } ); } } )
					),
					el( BaseControl, { label: __( 'Header text', 'nutrition-info-woocommerce' ), id: 'niw-al-htxt' },
						el( ColorPalette, { value: attrs.headerTextColor, onChange: function ( v ) { set( { headerTextColor: v || '#ffffff' } ); } } )
					)
				),

				el( PanelBody, { title: __( 'Rows', 'nutrition-info-woocommerce' ), initialOpen: false },
					el( BaseControl, { label: __( 'Row background', 'nutrition-info-woocommerce' ), id: 'niw-al-rbg' },
						el( ColorPalette, { value: attrs.rowBgColor,   onChange: function ( v ) { set( { rowBgColor:   v || '#ffffff' } ); } } )
					),
					el( BaseControl, { label: __( 'Row text', 'nutrition-info-woocommerce' ), id: 'niw-al-rtxt' },
						el( ColorPalette, { value: attrs.rowTextColor, onChange: function ( v ) { set( { rowTextColor: v || '#1e1e1e' } ); } } )
					)
				),

				el( PanelBody, { title: __( 'Icons', 'nutrition-info-woocommerce' ), initialOpen: false },
					el( ToggleControl, {
						label: __( 'Show icons', 'nutrition-info-woocommerce' ), checked: attrs.showIcons,
						__nextHasNoMarginBottom: true,
						onChange: function ( v ) { set( { showIcons: v } ); },
					} ),
					attrs.showIcons && el( RangeControl, {
						label: __( 'Icon size (px)', 'nutrition-info-woocommerce' ), value: attrs.iconSize,
						min: 20, max: 80, step: 4,
						__next40pxDefaultSize: true, __nextHasNoMarginBottom: true,
						onChange: function ( v ) { set( { iconSize: v || 40 } ); },
					} )
				)
			),

			el( 'div', Object.assign( { key: 'preview' }, blockProps ),
				el( AllergensPreview, {
					productId:       productId,
					headerBgColor:   attrs.headerBgColor,
					headerTextColor: attrs.headerTextColor,
					rowBgColor:      attrs.rowBgColor,
					rowTextColor:    attrs.rowTextColor,
					iconSize:        attrs.iconSize,
					showIcons:       attrs.showIcons,
				} )
			),
		];
	}

	blocks.registerBlockType( 'niw/allergens', {
		apiVersion:  3,
		title:       __( 'Allergens', 'nutrition-info-woocommerce' ),
		description: __( 'Displays allergens for a WooCommerce product.', 'nutrition-info-woocommerce' ),
		icon:        'shield-alt',
		category:    'niw-nutrition',
		keywords:    [ 'allergens', 'nutrition', 'product' ],
		supports: {
			color:   { background: true, text: true },
			spacing: { padding: true, margin: true },
			border:  { radius: true, color: true, width: true },
			align:   [ 'wide', 'full', 'left', 'center', 'right' ],
		},
		attributes: {
			productId:       { type: 'number',  default: 0 },
			headerBgColor:   { type: 'string',  default: '#1e1e1e' },
			headerTextColor: { type: 'string',  default: '#ffffff' },
			rowBgColor:      { type: 'string',  default: '#ffffff' },
			rowTextColor:    { type: 'string',  default: '#1e1e1e' },
			iconSize:        { type: 'number',  default: 40 },
			showIcons:       { type: 'boolean', default: true },
		},
		edit: edit,
		save: function () { return null; },
	} );

} (
	window.wp.blocks,
	window.wp.element,
	window.wp.components,
	window.wp.blockEditor,
	window.wp.apiFetch,
	window.wp.i18n
) );
