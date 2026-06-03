( function ( $ ) {
	'use strict';

	var $body = $( '#niw-ingredients-body' );

	function reindex() {
		$body.find( '.niw-ingredient-row' ).each( function ( i ) {
			$( this ).find( 'input[name*="[name]"]' ).attr( 'name', 'food_ingredients[' + i + '][name]' );
			$( this ).find( 'input[name*="[quantity]"]' ).attr( 'name', 'food_ingredients[' + i + '][quantity]' );
		} );
	}

	$( document ).on( 'click', '#niw-add-ingredient', function () {
		var idx = $body.find( '.niw-ingredient-row' ).length;
		var row = '<tr class="niw-ingredient-row">'
			+ '<td style="padding:4px 8px;"><input type="text" name="food_ingredients[' + idx + '][name]" placeholder="e.g. Olive oil" style="width:100%;"></td>'
			+ '<td style="padding:4px 8px;"><input type="text" name="food_ingredients[' + idx + '][quantity]" placeholder="e.g. 50g" style="width:100%;"></td>'
			+ '<td style="padding:4px 8px;text-align:center;"><button type="button" class="niw-remove-ingredient button">✕</button></td>'
			+ '</tr>';
		$body.append( row );
		reindex();
	} );

	$( document ).on( 'click', '.niw-remove-ingredient', function () {
		$( this ).closest( '.niw-ingredient-row' ).remove();
		reindex();
	} );

} )( jQuery );
