/*
* Doubletake.js 1.1
* A jQuery plugin for responsive images
*
* Copyright 2011, Graham Bird http://www.grahambird.co.uk
* Released under the WTFPL license 
* http://sam.zoy.org/wtfpl/
*
* Date: 28 July 2011
* Modified by Rahe for Wordpress the 07/02/2012
*/
(function($){

	$.fn.doubletake = function(userconfig)
	{
		// DEFAULT CONFIG
		var config = {
		'breakpoints': [320,640,960,1000],
		'sizes'		: ['thumbnail','medium', 'large', 'full'],
		'watchresize': false
		};
		return this.each( function() {
		
			var $this = $( this ); 
		
			// GRAB USER CONFIG
			if ( userconfig ) { 
				$.extend( config, userconfig );
			}
			
			var go = function()
			{
				var newWidth;
				var newIndex;
				var elementWidth = $( this ).width();
				
				// MAKE SURE BREAKPOINTS ARE IN CORRECT ORDER
				config.breakpoints.sort( function( a, b ){return a-b} );
				
				// USE SMALLEST BREAKPOINT TO START WITH
				newWidth = config.breakpoints[0];
				newIndex = 0;
				
				// LOOP OVER BREAKPOINTS TO WORK OUT WHICH APPLIES BEST
				$.each(config.breakpoints, function( index, value ) {
					
					// IF THE ELEMENT WE'RE INSIDE IS BIGGER THAN THE CURRENT VALUE, INCREASE THE BEST WIDTH AND CONTINUE
					if ( elementWidth >= value ) {
						newIndex = index;
						newWidth = value;
					} else {
						// EXIT, WE HAVE THE BIGGEST WE NEED
						return false;
					}
				});
				// LOOP OVER IMAGES INSIDE THE SELECTED ELEMENT
				$this.find('img[class*="size-"]').each(function() {
					// Get the el
					var el = $( this );
					
					// regexp to get the size in the image
					var r = new RegExp( "-([0-9]+)x([0-9]+).[a-z]+" );
					var s_attr = el.attr( 'src-'+config.sizes[newIndex] );
					var parts = r.exec( s_attr );
					
					if( typeof s_attr != 'undefined' ) {
						
						if( parts ) {
							el.attr( 'width', parts[1] );
							el.attr( 'height',parts[2] );
						} else {
							el.removeAttr( 'width height' );
						}
						el.removeClass( 'size-' );
						this.src = el.attr( 'src-'+config.sizes[newIndex] );
					}
				});
			
			}
			
			// INITIAL PASS
			go();
			
			if (config.watchresize)
			{
				// UPDATE AFTER RESIZE
				$(window).resize(function()
				{
					if(this.resizeTO) clearTimeout(this.resizeTO);
					this.resizeTO = setTimeout(function() {
						$(this).trigger('resizeEnd');
					}, 500);
				});
				$(window).bind('resizeEnd', function()
				{
					go();
				});
			}
		});
	}
})(jQuery);
jQuery(sri_vars.html_selector).doubletake( {
	'watchresize': true,
	'breakpoints' : sri_vars.breakpoints,
	'sizes' : sri_vars.sizes,
} );