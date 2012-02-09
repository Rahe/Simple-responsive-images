jQuery( function(){
	jQuery( '.to-copy' ).hide();
	
	// Add a element
	jQuery( '.sri_add_breakpoint' ).on( 'click', function( e ) {
		e.preventDefault();
		jQuery( '.to-copy' ).clone().removeClass( 'to-copy' ).appendTo( '.widefat tbody' ).show();
	} );
	// Remove an element
	jQuery( '.sri_remove_breakpoint' ).live( 'click', function( e ) {
		e.preventDefault();
		jQuery( this ).closest( 'tr' ).remove();
	} );
	jQuery( 'input[type="number"]' ).live( 'click change', function() {
		var el = jQuery( this );
		
		el.closest( 'tr' ).find( 'input,select' ).each( function( i, input ) {
			var r = new RegExp( '(-?[0-9]+)' );
			var val = el.val();
			if( val == '' )
				val = 0;
			input.name = input.name.replace( r, val );
		});
	} );
} );