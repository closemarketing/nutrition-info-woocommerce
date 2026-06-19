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

	/* Exact same groups + rows as HELPER::get_nutrients() in manage-menus */
	var GROUPS = [
		{ key: 'energy',   label: 'Energy',             rows: [
			{ label: 'Energy', unit: 'kcal' },
			{ label: 'Energy', unit: 'kJ' },
		]},
		{ key: 'fat',      label: 'Fat',               rows: [
			{ label: 'Fat',                          unit: 'g' },
			{ label: '— Saturated fatty acids',      unit: 'g',  sub: true },
			{ label: '— Monounsaturated fatty acids', unit: 'g',  sub: true },
			{ label: '— Polyunsaturated fatty acids', unit: 'g',  sub: true },
			{ label: '— Trans fatty acids',           unit: 'g',  sub: true },
			{ label: 'Cholesterol',                  unit: 'mg' },
		]},
		{ key: 'carbs',    label: 'Carbohydrate',  rows: [
			{ label: 'Carbohydrate',   unit: 'g' },
			{ label: '— Sugars',        unit: 'g', sub: true },
			{ label: '— Added sugars',  unit: 'g', sub: true },
			{ label: '— Polyols',       unit: 'g', sub: true },
			{ label: '— Starch',        unit: 'g', sub: true },
			{ label: 'Dietary fiber',  unit: 'g' },
			{ label: '— Soluble fiber',   unit: 'g', sub: true },
			{ label: '— Insoluble fiber', unit: 'g', sub: true },
		]},
		{ key: 'protein',  label: 'Protein and salt',      rows: [
			{ label: 'Protein', unit: 'g' },
			{ label: 'Salt',    unit: 'g' },
			{ label: '— Sodium', unit: 'mg', sub: true },
		]},
		{ key: 'vitamins', label: 'Vitamins',            rows: [
			{ label: 'Vitamin A',             unit: 'μg' },
			{ label: 'Thiamine (B1)',          unit: 'mg' },
			{ label: 'Riboflavin (B2)',        unit: 'mg' },
			{ label: 'Niacin (B3)',            unit: 'mg' },
			{ label: 'Pantothenic acid (B5)',  unit: 'mg' },
			{ label: 'Vitamin B6',             unit: 'mg' },
			{ label: 'Biotin (B7)',            unit: 'μg' },
			{ label: 'Folic acid (B9)',        unit: 'μg' },
			{ label: 'Vitamin B12',            unit: 'μg' },
			{ label: 'Vitamin C',              unit: 'mg' },
			{ label: 'Vitamin D',              unit: 'μg' },
			{ label: 'Vitamin E',              unit: 'mg' },
			{ label: 'Vitamin K',              unit: 'μg' },
		]},
		{ key: 'minerals', label: 'Minerals',            rows: [
			{ label: 'Calcium',    unit: 'mg' },
			{ label: 'Phosphorus', unit: 'mg' },
			{ label: 'Iron',       unit: 'mg' },
			{ label: 'Magnesium',  unit: 'mg' },
			{ label: 'Zinc',       unit: 'mg' },
			{ label: 'Iodine',     unit: 'μg' },
			{ label: 'Selenium',   unit: 'μg' },
			{ label: 'Copper',     unit: 'mg' },
			{ label: 'Manganese',  unit: 'mg' },
			{ label: 'Chromium',   unit: 'μg' },
			{ label: 'Molybdenum', unit: 'μg' },
			{ label: 'Potassium',  unit: 'mg' },
			{ label: 'Fluoride',   unit: 'mg' },
			{ label: 'Chloride',   unit: 'mg' },
		]},
	];

	function buildSkeletonTable( attrs ) {
		var headerBg   = ( attrs && attrs.headerBgColor )   || '#1e1e1e';
		var headerText = ( attrs && attrs.headerTextColor ) || '#ffffff';
		var groupBg    = ( attrs && attrs.groupBgColor )    || '#f0f0f0';
		var groupText  = ( attrs && attrs.groupTextColor )  || '#555555';
		var rowBg      = ( attrs && attrs.rowBgColor )      || '#ffffff';
		var rowText    = ( attrs && attrs.rowTextColor )    || '#1e1e1e';
		var subText    = ( attrs && attrs.subTextColor )    || '#666666';
		var border     = ( attrs && attrs.borderColor )     || '#e8e8e8';
		var fs         = ( attrs && attrs.fontSize )        || 13;

		var sTable  = { borderCollapse: 'collapse', width: '100%', fontSize: fs + 'px' };
		var sHeader = { background: headerBg, color: headerText, padding: '10px 12px', textAlign: 'left', fontWeight: '700', fontSize: fs + 'px' };
		var sGroup  = { background: groupBg,  color: groupText,  padding: '5px 12px', fontWeight: '600', fontSize: '12px', textTransform: 'uppercase', letterSpacing: '.05em' };
		var sCell   = { padding: '5px 12px',          borderBottom: '1px solid ' + border, color: rowText,  background: rowBg };
		var sSub    = { padding: '5px 12px 5px 24px', borderBottom: '1px solid ' + border, color: subText,  background: rowBg };
		var sRight  = { padding: '5px 12px',          borderBottom: '1px solid ' + border, textAlign: 'right', color: '#888', whiteSpace: 'nowrap', background: rowBg };

		var tbodyRows = [];
		GROUPS.forEach( function ( group ) {
			tbodyRows.push( el( 'tr', { key: 'g-' + group.key }, el( 'td', { colSpan: 2, style: sGroup }, group.label ) ) );
			group.rows.forEach( function ( row, idx ) {
				tbodyRows.push( el( 'tr', { key: group.key + idx },
					el( 'td', { style: row.sub ? sSub : sCell }, row.label ),
					el( 'td', { style: sRight }, '— ' + row.unit )
				) );
			} );
		} );

		return el( 'table', { style: sTable },
			el( 'thead', null, el( 'tr', null, el( 'th', { colSpan: 2, style: sHeader },
				__( 'Nutritional information', 'nutrition-info-woocommerce' ) + ' — ' + __( 'per 100 g', 'nutrition-info-woocommerce' )
			) ) ),
			el( 'tbody', null, tbodyRows )
		);
	}

	function NutrientsPreview( props ) {
		var productId   = props.productId;
		var headerBg    = props.headerBgColor   || '#1e1e1e';
		var headerText  = props.headerTextColor || '#ffffff';
		var groupBg     = props.groupBgColor    || '#f0f0f0';
		var groupText   = props.groupTextColor  || '#555555';
		var rowBg       = props.rowBgColor      || '#ffffff';
		var rowText     = props.rowTextColor    || '#1e1e1e';
		var subText     = props.subTextColor    || '#666666';
		var border      = props.borderColor     || '#e8e8e8';
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
				+ '&group_bg='     + encodeURIComponent( groupBg )
				+ '&group_text='   + encodeURIComponent( groupText )
				+ '&row_bg='       + encodeURIComponent( rowBg )
				+ '&row_text='     + encodeURIComponent( rowText )
				+ '&sub_text='     + encodeURIComponent( subText )
				+ '&border_color=' + encodeURIComponent( border )
				+ '&font_size='    + fs
			} ).then( function ( d ) { setHtml( d.html || '' ); setLoading( false ); } )
			  .catch( function () { setHtml( '' ); setLoading( false ); } );
		}, [ productId, headerBg, headerText, groupBg, groupText, rowBg, rowText, subText, border, fs ] );

		if ( ! productId || productId <= 0 ) {
			return el( 'div', { style: { opacity: 0.55 } }, buildSkeletonTable( props ) );
		}
		if ( loadingValue ) {
			return el( 'div', { style: { padding: '12px' } }, __( 'Loading…', 'nutrition-info-woocommerce' ) );
		}
		return el( 'div', { dangerouslySetInnerHTML: { __html: htmlValue } } );
	}

	function edit( props ) {
		var attrs      = props.attributes;
		var set        = props.setAttributes;
		var productId  = attrs.productId;
		var blockProps = useBlockProps();

		return [
			el( InspectorControls, { key: 'inspector' },

				el( PanelBody, { title: __( 'Product selection', 'nutrition-info-woocommerce' ), initialOpen: true },
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

				el( PanelBody, { title: __( 'Groups', 'nutrition-info-woocommerce' ), initialOpen: false },
					el( BaseControl, { label: __( 'Group background', 'nutrition-info-woocommerce' ), id: 'niw-n-gbg' },
						el( ColorPalette, { value: attrs.groupBgColor,   onChange: function ( v ) { set( { groupBgColor:   v || '#f0f0f0' } ); } } )
					),
					el( BaseControl, { label: __( 'Group text', 'nutrition-info-woocommerce' ), id: 'niw-n-gtxt' },
						el( ColorPalette, { value: attrs.groupTextColor, onChange: function ( v ) { set( { groupTextColor: v || '#555555' } ); } } )
					)
				),

				el( PanelBody, { title: __( 'Nutrient rows', 'nutrition-info-woocommerce' ), initialOpen: false },
					el( BaseControl, { label: __( 'Row background', 'nutrition-info-woocommerce' ), id: 'niw-n-rbg' },
						el( ColorPalette, { value: attrs.rowBgColor,   onChange: function ( v ) { set( { rowBgColor:   v || '#ffffff' } ); } } )
					),
					el( BaseControl, { label: __( 'Main text', 'nutrition-info-woocommerce' ), id: 'niw-n-rtxt' },
						el( ColorPalette, { value: attrs.rowTextColor, onChange: function ( v ) { set( { rowTextColor: v || '#1e1e1e' } ); } } )
					),
					el( BaseControl, { label: __( 'Sub-nutrient text', 'nutrition-info-woocommerce' ), id: 'niw-n-stxt' },
						el( ColorPalette, { value: attrs.subTextColor, onChange: function ( v ) { set( { subTextColor: v || '#666666' } ); } } )
					),
					el( BaseControl, { label: __( 'Divider colour', 'nutrition-info-woocommerce' ), id: 'niw-n-bdr' },
						el( ColorPalette, { value: attrs.borderColor,  onChange: function ( v ) { set( { borderColor:  v || '#e8e8e8' } ); } } )
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
					groupBgColor:    attrs.groupBgColor,
					groupTextColor:  attrs.groupTextColor,
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
		title:       __( 'Product nutritional information', 'nutrition-info-woocommerce' ),
		description: __( 'Displays the full nutrient table for a WooCommerce product.', 'nutrition-info-woocommerce' ),
		icon:        'list-view',
		category:    'niw-nutrition',
		keywords:    [ 'nutrients', 'nutrition', 'product', 'calories' ],
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
			groupBgColor:    { type: 'string', default: '#f0f0f0' },
			groupTextColor:  { type: 'string', default: '#555555' },
			rowBgColor:      { type: 'string', default: '#ffffff' },
			rowTextColor:    { type: 'string', default: '#1e1e1e' },
			subTextColor:    { type: 'string', default: '#666666' },
			borderColor:     { type: 'string', default: '#e8e8e8' },
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
