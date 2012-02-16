<?php
/*
Plugin Name: Simple Responsive Images
Description: Add options in media setting page for images sizes
Plugin URI: https://github.com/Rahe/Simple-responsive-images
Version: 1.0.2
Author: Rahe
Author URI: http://nicolas-juen.fr
Text Domain: sri
Domain Path: /languages/

2012 Nicolas JUEN (njuen87@gmail.com))

Contribute on https://github.com/Rahe/Simple-responsive-images

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
define( 'SRI_URL', plugins_url('/', __FILE__) );
define( 'SRI_DIR', dirname(__FILE__) );
define( 'SRI_VERSION', '1.0.2' );
define( 'SRI_OPTION', 'sri_responsive_images' );


require_once( SRI_DIR . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'class.admin.php'  );
require_once( SRI_DIR . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'class.client.php'  );
require_once( SRI_DIR . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'functions.plugin.php'  );


register_activation_hook( __FILE__, 'sri_plugin_install' );

add_action ( 'plugins_loaded', 'initSRI' );
function initSRI() {
	global $sri;
	if( is_admin() )
		$sri['admin'] = new SRI_Admin();
	
	$sri['client'] = new SRI_Client();
	
	load_plugin_textdomain ( 'sri', false, basename( rtrim( SRI_DIR, '/' ) ) . '/languages' );
}
?>