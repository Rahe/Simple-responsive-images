<?php
class SRI_Client {
	function __construct() {
		add_filter( 'wp_get_attachment_image_attributes', array( &$this, 'addAttrs' ), 1 ,2 );
		add_filter( 'wp_enqueue_scripts', array( &$this, 'addRessources' ) );
	}
	
	function addAttrs( $attrs = array(), $att = 0 ) {
		
		$other_sizes = sri_get_the_image_sizes( $att->ID );
		if( !empty( $other_sizes ) && is_array( $other_sizes ) )
			$attrs = array_merge( $attrs, $other_sizes );
		
		return $attrs;
	}
	
	function addRessources() {
		
		$add_script = apply_filters( 'sri_add_script', true );
		
		if( !$add_script )
			return false;
		
		$options = get_option( SRI_OPTION );
		
		if( empty( $options ) || !isset( $options['breakpoints'] ) || empty( $options['breakpoints'] ) )
			return false;
		
		$breakpoints = array_keys( $options['breakpoints'] );
		$sizes = array_values( $options['breakpoints'] );
		
		if( empty( $breakpoints ) || empty( $sizes ) )
			return false;
		
		wp_enqueue_script( 'dbl-take', SRI_URL.'/ressources/js/responsive.js', array( 'jquery' ), SRI_VERSION, true );
		wp_localize_script( 'dbl-take', 'sri_vars', array(
			'breakpoints' => $breakpoints,
			'sizes'		  => $sizes,
			'html_selector' => $options['html_selector']
		) );
	}
}
?>