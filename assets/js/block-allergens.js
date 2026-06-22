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

	var products  = ( window.niwBlockData && window.niwBlockData.products )  ? window.niwBlockData.products  : [];
	var iconsUrl  = ( window.niwBlockData && window.niwBlockData.iconsUrl )   ? window.niwBlockData.iconsUrl  : '';
	var allergens = ( window.niwBlockData && window.niwBlockData.allergens )  ? window.niwBlockData.allergens : {};

	/* ---- Skeleton preview (same appearance as the real block) ---- */
	function buildSkeletonPreview( attrs ) {
		var headerBg   = ( attrs && attrs.headerBgColor )   || '#1e1e1e';
		var headerText = ( attrs && attrs.headerTextColor ) || '#ffffff';
		var rowBg      = ( attrs && attrs.rowBgColor )      || '#ffffff';
		var rowText    = ( attrs && attrs.rowTextColor )    || '#1e1e1e';
		var iconSize   = ( attrs && attrs.iconSize )        || 32;
		var showIcons  = attrs ? ( attrs.showIcons !== false ) : true;

		var headerStyle = { background: headerBg, color: headerText, padding: '10px 12px', textAlign: 'left', fontWeight: '700' };
		var rowStyle    = { background: rowBg, color: rowText, padding: '8px 12px', borderBottom: '1px solid #e8e8e8', verticalAlign: 'middle' };

		var keys  = Object.keys( allergens );
		var rows  = keys.slice( 0, 5 ).map( function ( key ) {
			return el( 'tr', { key: key },
				el( 'td', { style: rowStyle },
					showIcons && iconsUrl
						? el( 'img', { src: iconsUrl + 'icon-' + key + '.png', alt: '', style: { width: iconSize + 'px', height: iconSize + 'px', verticalAlign: 'middle', marginRight: '8px' } } )
						: null,
					allergens[ key ]
				)
			);
		} );

		return el( 'table', { style: { borderCollapse: 'collapse', width: '100%', opacity: 0.55 } },
			el( 'thead', null, el( 'tr', null, el( 'th', { colSpan: 2, style: headerStyle }, __( 'Allergens', 'nutrition-info-woocommerce' ) ) ) ),
			el( 'tbody', null, rows )
		);
	}

	/* ---- Live preview component ---- */
	function AllergensPreview( props ) {
		var productId   = props.productId;
		var headerBg    = props.headerBgColor   || '#1e1e1e';
		var headerText  = props.headerTextColor || '#ffffff';
		var rowBg       = props.rowBgColor      || '#ffffff';
		var rowText     = props.rowTextColor    || '#1e1e1e';
		var iconSize    = props.iconSize        || 32;
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
			return el( 'div', null, buildSkeletonPreview( props ) );
		}
		if ( loadingValue ) {
			return el( 'div', { style: { padding: '12px' } }, __( 'Loading…', 'nutrition-info-woocommerce' ) );
		}
		return el( 'div', { dangerouslySetInnerHTML: { __html: htmlValue } } );
	}

	/* ---- Edit function ---- */
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
						min: 16, max: 64, step: 4,
						__next40pxDefaultSize: true, __nextHasNoMarginBottom: true,
						onChange: function ( v ) { set( { iconSize: v || 32 } ); },
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
		title:       __( 'Product allergens', 'nutrition-info-woocommerce' ),
		description: __( 'Displays the declared allergens for a WooCommerce product.', 'nutrition-info-woocommerce' ),
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
			iconSize:        { type: 'number',  default: 32 },
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
