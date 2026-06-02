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
	var InspectorControls = blockEditor.InspectorControls;
	var useBlockProps     = blockEditor.useBlockProps;

	var products = ( window.niwNutrientsData && window.niwNutrientsData.products ) ? window.niwNutrientsData.products : [];

	var FIELDS = [
		{ key: 'energy',             label: 'Energy (KJ/kcal)' },
		{ key: 'fat',                label: 'Fat (g)' },
		{ key: 'saturated_fat',      label: '- Saturated fatty acids (g)',  sub: true },
		{ key: 'monounsaturated_fat',label: '- Monounsaturated fat (g)',    sub: true },
		{ key: 'polyunsaturated_fat',label: '- Polyunsaturated fat (g)',    sub: true },
		{ key: 'carb',               label: 'Carbohydrate (g)' },
		{ key: 'sugar',              label: '- Of which sugars (g)',        sub: true },
		{ key: 'polyol',             label: '- Of which Polyols (g)',       sub: true },
		{ key: 'starch',             label: '- Of which Starch (g)',        sub: true },
		{ key: 'fiber',              label: 'Dietary Fiber (g)' },
		{ key: 'protein',            label: 'Protein (g)' },
		{ key: 'salt',               label: 'Salt (g)' },
		{ key: 'vitamin_mineral',    label: 'Vitamins and minerals' },
	];

	function buildSkeletonTable( attrs ) {
		var headerBg   = ( attrs && attrs.headerBgColor )   || '#1e1e1e';
		var headerText = ( attrs && attrs.headerTextColor ) || '#ffffff';
		var rowBg      = ( attrs && attrs.rowBgColor )      || '#ffffff';
		var rowText    = ( attrs && attrs.rowTextColor )    || '#1e1e1e';
		var subText    = ( attrs && attrs.subTextColor )    || '#666666';
		var border     = ( attrs && attrs.borderColor )     || '#e0e0e0';
		var fs         = ( attrs && attrs.fontSize )        || 13;

		var sTable  = { borderCollapse: 'collapse', width: '100%', fontSize: fs + 'px' };
		var sHeader = { background: headerBg, color: headerText, padding: '10px 12px', textAlign: 'left', fontWeight: '700' };
		var sCell   = { padding: '5px 12px',          borderBottom: '1px solid ' + border, color: rowText,  background: rowBg };
		var sSub    = { padding: '5px 12px 5px 24px', borderBottom: '1px solid ' + border, color: subText,  background: rowBg };
		var sRight  = { padding: '5px 12px',          borderBottom: '1px solid ' + border, textAlign: 'right', color: '#888', whiteSpace: 'nowrap', background: rowBg };

		var tbodyRows = FIELDS.map( function ( f, i ) {
			return el( 'tr', { key: i },
				el( 'td', { style: f.sub ? sSub : sCell }, f.label ),
				el( 'td', { style: sRight }, '—' )
			);
		} );

		return el( 'table', { style: sTable },
			el( 'thead', null, el( 'tr', null, el( 'th', { style: sHeader, colSpan: 2 },
				__( 'Nutritional Information', 'nutrition-info-woocommerce' ) + ' — ' +
				__( 'per 100 g', 'nutrition-info-woocommerce' )
			) ) ),
			el( 'tbody', null, tbodyRows )
		);
	}

	function NutrientsPreview( props ) {
		var productId   = props.productId;
		var headerBg    = props.headerBgColor   || '#1e1e1e';
		var headerText  = props.headerTextColor || '#ffffff';
		var rowBg       = props.rowBgColor      || '#ffffff';
		var rowText     = props.rowTextColor    || '#1e1e1e';
		var subText     = props.subTextColor    || '#666666';
		var border      = props.borderColor     || '#e0e0e0';
		var fs          = props.fontSize        || 13;

		var htmlState    = useState( '' );
		var loadingState = useState( false );
		var htmlValue    = htmlState[0];
		var setHtml      = htmlState[1];
		var loadingValue = loadingState[0];
		var setLoading   = loadingState[1];

		useEffect( function () {
			if ( ! productId || productId <= 0 ) { setHtml( '' ); return; }
			setLoading( true );
			apiFetch( { path: '/niw/v1/render/nutrients'
				+ '?product_id='   + productId
				+ '&header_bg='    + encodeURIComponent( headerBg )
				+ '&header_text='  + encodeURIComponent( headerText )
				+ '&row_bg='       + encodeURIComponent( rowBg )
				+ '&row_text='     + encodeURIComponent( rowText )
				+ '&sub_text='     + encodeURIComponent( subText )
				+ '&border_color=' + encodeURIComponent( border )
				+ '&font_size='    + fs
			} ).then( function ( d ) { setHtml( d.html || '' ); setLoading( false ); } )
			  .catch( function () { setHtml( '' ); setLoading( false ); } );
		}, [ productId, headerBg, headerText, rowBg, rowText, subText, border, fs ] );

		if ( ! productId || productId <= 0 ) {
			return el( 'div', { style: { opacity: 0.55 } }, buildSkeletonTable( props ) );
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
					el( BaseControl, { label: __( 'Header background', 'nutrition-info-woocommerce' ), id: 'niw-n-hbg' },
						el( ColorPalette, { value: attrs.headerBgColor,   onChange: function ( v ) { set( { headerBgColor:   v || '#1e1e1e' } ); } } )
					),
					el( BaseControl, { label: __( 'Header text', 'nutrition-info-woocommerce' ), id: 'niw-n-htxt' },
						el( ColorPalette, { value: attrs.headerTextColor, onChange: function ( v ) { set( { headerTextColor: v || '#ffffff' } ); } } )
					)
				),

				el( PanelBody, { title: __( 'Rows', 'nutrition-info-woocommerce' ), initialOpen: false },
					el( BaseControl, { label: __( 'Row background', 'nutrition-info-woocommerce' ), id: 'niw-n-rbg' },
						el( ColorPalette, { value: attrs.rowBgColor,   onChange: function ( v ) { set( { rowBgColor:   v || '#ffffff' } ); } } )
					),
					el( BaseControl, { label: __( 'Row text', 'nutrition-info-woocommerce' ), id: 'niw-n-rtxt' },
						el( ColorPalette, { value: attrs.rowTextColor, onChange: function ( v ) { set( { rowTextColor: v || '#1e1e1e' } ); } } )
					),
					el( BaseControl, { label: __( 'Sub-nutrient text', 'nutrition-info-woocommerce' ), id: 'niw-n-stxt' },
						el( ColorPalette, { value: attrs.subTextColor, onChange: function ( v ) { set( { subTextColor: v || '#666666' } ); } } )
					),
					el( BaseControl, { label: __( 'Row separator color', 'nutrition-info-woocommerce' ), id: 'niw-n-bdr' },
						el( ColorPalette, { value: attrs.borderColor,  onChange: function ( v ) { set( { borderColor:  v || '#e0e0e0' } ); } } )
					)
				),

				el( PanelBody, { title: __( 'Typography', 'nutrition-info-woocommerce' ), initialOpen: false },
					el( RangeControl, {
						label: __( 'Font size (px)', 'nutrition-info-woocommerce' ), value: attrs.fontSize,
						min: 10, max: 20, step: 1,
						__next40pxDefaultSize: true, __nextHasNoMarginBottom: true,
						onChange: function ( v ) { set( { fontSize: v || 13 } ); },
					} )
				)
			),

			el( 'div', Object.assign( { key: 'preview' }, blockProps ),
				el( NutrientsPreview, {
					productId:       productId,
					headerBgColor:   attrs.headerBgColor,
					headerTextColor: attrs.headerTextColor,
					rowBgColor:      attrs.rowBgColor,
					rowTextColor:    attrs.rowTextColor,
					subTextColor:    attrs.subTextColor,
					borderColor:     attrs.borderColor,
					fontSize:        attrs.fontSize,
				} )
			),
		];
	}

	blocks.registerBlockType( 'niw/nutrients', {
		apiVersion:  3,
		title:       __( 'Nutritional Info', 'nutrition-info-woocommerce' ),
		description: __( 'Displays nutritional information for a WooCommerce product.', 'nutrition-info-woocommerce' ),
		icon:        'list-view',
		category:    'niw-nutrition',
		keywords:    [ 'nutrition', 'nutrients', 'product' ],
		supports: {
			color:   { background: true, text: true },
			spacing: { padding: true, margin: true },
			border:  { radius: true, color: true, width: true },
			align:   [ 'wide', 'full', 'left', 'center', 'right' ],
		},
		attributes: {
			productId:       { type: 'number', default: 0 },
			headerBgColor:   { type: 'string', default: '#1e1e1e' },
			headerTextColor: { type: 'string', default: '#ffffff' },
			rowBgColor:      { type: 'string', default: '#ffffff' },
			rowTextColor:    { type: 'string', default: '#1e1e1e' },
			subTextColor:    { type: 'string', default: '#666666' },
			borderColor:     { type: 'string', default: '#e0e0e0' },
			fontSize:        { type: 'number', default: 13 },
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
