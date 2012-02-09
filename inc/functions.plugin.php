<?php
function sri_plugin_install() {
	$opts = get_option( SRI_OPTION );
	
	if( !empty( $opts ) )
		return false;

	$default = array(
		'breakpoints' => array(
			320 => 'thumbnail',
			640 => 'medium',
			920 => 'large',
			1024 => 'full',
		),
		'html_selector' => 'body'
	);
	
	update_option( SRI_OPTION, $default );
}
function sri_get_the_image_sizes( $id = 0 ) {
	$options = get_option( SRI_OPTION );
	
	if( empty( $options ) )
		return array();
	
	$sizes = array_values($options['breakpoints']);
	
	foreach ( $sizes as $s ) {
		$data = wp_get_attachment_image_src( $id, $s );
		$other_sizes['src-'.$s] = $data[0];
	}
	
	return $other_sizes;
}

function sri_get_image_sizes( $prefix = 'src-' ) {
	global $_wp_additional_image_sizes;
	
	$other_sizes = array();
	foreach ( get_intermediate_image_sizes() as $s ) {
		if ( isset( $_wp_additional_image_sizes[$s]['width'] ) ) // For theme-added sizes
			$width = intval( $_wp_additional_image_sizes[$s]['width'] );
		else                                                     // For default sizes set in options
			$width = get_option( "{$s}_size_w" );
		
		// Set height
		if ( isset( $_wp_additional_image_sizes[$s]['height'] ) ) // For theme-added sizes
			$height = intval( $_wp_additional_image_sizes[$s]['height'] );
		else                                                      // For default sizes set in options
			$height = get_option( "{$s}_size_h" );
			
		$other_sizes[] = array( $prefix.$s, $width, $height );
	}
	$other_sizes[] = array( $prefix."full", 'max', 'max' );
	return $other_sizes;
}

function sri_get_breakpoint_image_sizes(){
	$options = get_option( SRI_OPTION );
	
	if( empty( $options ) )
		return array();
	
	$sizes = array_values($options['breakpoints']);
	
	return $sizes;
}
?>