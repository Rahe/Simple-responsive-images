<?php
class SRI_Admin {
	
	function __construct() {
		add_action( 'admin_menu', array( &$this, 'addOptionPage' ) );
		add_action( 'admin_init', array( &$this,'saveSettings' ) );
		add_filter( 'image_send_to_editor', array( &$this,'imageSendToEditor' ), 9 , 8 );
		add_filter( 'tiny_mce_before_init', array( &$this,'addMceValidElements' ) );
		
		add_action( 'admin_enqueue_scripts', array( &$this, 'addRessources' ) );
	}
	
	/**
	 * Add the ressources on the right admin page
	 * 
	 * @param $hook : the hook of the page
	 * @return : void
	 * @author Nicolas Juen
	 */
	function addRessources( $hook = '' ) {
		if( $hook == 'settings_page_sri_optionPage' ) {
			wp_enqueue_script( 'sri_admin', SRI_URL.'ressources/js/admin-page.js', array( 'jquery' ), SRI_VERSION, true );
		}
	}
	
	/**
	 * Add to tinyMce src special attributes
	 * 
	 * @param $init : the init vars of tinyMce
	 * @return : $init
	 * @author Nicolas Juen
	 */
	function addMceValidElements( $init ) {
		// Get the sizes
		$sizes = sri_get_the_image_sizes();
		
		// If empty, stop now
		if( empty( $sizes ) )
			return $init;
		
		// Add the basic attributes of an img elements
		$basic_img = array( 'src', 'alt', 'title', 'height', 'width', 'class' ) ;
		
		// Add the custom sizes to the attributes
		$elements = array( 'img' => array_merge( $sizes, $basic_img ) );
		
		$eleList = array();
		foreach ( $elements as $element => $attributes ) {
			if ( count( $attributes ) > 0 ) {
				$eleList[] = $element . '[' . implode( '|', $attributes ) . ']';
			} else {
				$eleList[] = $element;
			}
		}
		
		// Extended string to add to tinyMCE.init();
		$extStr = implode(',', $eleList);
		// Only add ext valid ele's if a correct string was made
		if ( $extStr != null && $extStr != '' ) {
			// Add to extended_valid_elements if it alreay exists
			if ( isset( $init['extended_valid_elements'] ) && ! empty( $init['extended_valid_elements'] ) ) {
				$init['extended_valid_elements'] .= ',' . $extStr;
			} else {
				$init['extended_valid_elements'] = $extStr;
			}
		}
		
		// Super important: return $init!
		return $init;
	}
	
	function imageSendToEditor( $html, $id, $caption, $title, $align, $url, $size, $alt ) {
		return $this->getImageTag( $id, $alt , $title, $align, $size, sri_get_the_image_sizes( $id ) );
	}
	
	function getImageTag( $id, $alt, $title, $align, $size='medium', $attrs = array() ) {
	
		list( $img_src, $width, $height ) = image_downsize($id, $size);
		$hwstring = image_hwstring($width, $height);
	
		$class = 'align' . esc_attr($align) .' size-' . esc_attr($size) . ' wp-image-' . $id;
		$class = apply_filters('get_image_tag_class', $class, $id, $align, $size);
		
		$attrs_fields = '';
		foreach( $attrs as $name => $attr ) {
			$attrs_fields .= ' '.$name.'="'.esc_attr( $attr ).'" ';
		}
	
		$html = '<img src="' . esc_attr($img_src) . '" alt="' . esc_attr($alt) . '" title="' . esc_attr($title).'" '.$hwstring.'class="'.$class.'" '.$attrs_fields.' />';
		
		return $html;
	}

	function addOptionPage() {
		add_options_page( __( 'Reponsive Images', 'sri' ), __( 'Reponsive Images', 'sri' ), 'manage_options', 'sri_optionPage', array( &$this, 'sri_optionPage' ) );
	}
	
	function saveSettings() {
		if( isset( $_POST['save-sri'] ) ){
			check_admin_referer( 'sri_sizes' );
			
			$options = array( 'breakpoints', 'html_selector' => 'body' );
			
			if( isset( $_POST['breakpoints'] ) && !empty( $_POST['breakpoints'] ) ) {
				// make the breakpoints
				foreach( $_POST['breakpoints'] as $k => $bp ) {
					if( (int)$k < 0 )
						continue;
					
					$options['breakpoints'][(int)$bp['breakpoint']] = $bp['size'];
				}
				
				if( isset( $options['breakpoints'] ) && !empty( $options['breakpoints'] ) ) {
					// Sort by keys
					ksort ( $options['breakpoints'] );
				}
			}
			
			if( isset( $_POST['html_selector'] ) ){
				$options['html_selector'] = sanitize_text_field( $_POST['html_selector'] );
			}
			
			update_option( SRI_OPTION, $options );
		}
	}
	
	function sri_optionPage() {
		$options = get_option( SRI_OPTION );
		
		if( !isset( $options ) || empty( $options ) ) {
			$options = array(
				'breakpoints' => array(
					320 => 'thumbnail',
					640 => 'medium',
					920 => 'large',
					1024 => 'full',
				),
				'html_selector' => 'body'
			);
		}
		$html_selector = isset( $options['html_selector'] )? $options['html_selector'] : '' ;
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php esc_html_e( 'Reponsive Images', 'sri' ); ?> </h2>
		<form method="post">
			<h3> <?php esc_html_e( 'Breakpoints', 'sri'); ?> </h3>
			<p class="description">
				<?php esc_html_e( 'Define here the breakpoints. If you set a break point at 320px and a breakpoint at 920px, if you are below 320px or 960px, this 320px size will be used. When the windows size is higher than 960px, then the 960px size will be switched.' , 'sri' ) ?>
			</p>
			<p class="description">
				<b><?php esc_html_e( 'WARNING : The images inserted in the content before the plugin or between two different configurations will not have the right attributes and the script may not work if the size names have changed !', 'sri' ); ?></b>
			</p>
			<table class="widefat">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Breakpoint', 'sri' ); ?></th>
						<th><?php esc_html_e( 'Size associated', 'sri' ); ?></th>
						<th><?php esc_html_e( 'Action', 'sri' ); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th><?php esc_html_e( 'Breakpoint', 'sri' ); ?></th>
						<th><?php esc_html_e( 'Size associated', 'sri' ); ?></th>
						<th><?php esc_html_e( 'Action', 'sri' ); ?></th>
					</tr>
				</tfoot>
				<tbody>
				<tr class="to-copy">
					<td>
						<input type="number" name="breakpoints[-1][breakpoint]" min='0' max='' step="1" value="" />
					</td>
					<td>
						<select name="breakpoints[-1][size]">
							<?php foreach( sri_get_image_sizes( '' ) as $wp_size ): ?>
								<option value="<?php echo esc_attr( $wp_size[0] ); ?>" >  <?php echo esc_html( $wp_size[0].'-'.$wp_size[1].'x'.$wp_size[2] ); ?> </option>
							<?php endforeach; ?>
						</select>
					</td>
					<td>
						<input type='button' class='button sri_remove_breakpoint' value="<?php esc_attr_e( 'Remove breakpoint', 'sri' ); ?>" />
					</td>
				</tr>
				<?php if( isset( $options['breakpoints'] ) && !empty( $options['breakpoints'] ) ) : ?>
					<?php foreach( $options['breakpoints'] as $breakpoint => $size ) : ?>
					<tr>
						<td>
							<input type="number" name="breakpoints[<?php echo esc_attr( $breakpoint ); ?>][breakpoint]" min='0' max='' step="1" value="<?php echo esc_attr( $breakpoint ); ?>">
						</td>
						<td>
							<select name="breakpoints[<?php echo esc_attr( $breakpoint ); ?>][size]">
								<?php foreach( sri_get_image_sizes( '' ) as $wp_size ): ?>
									<option value="<?php echo esc_attr( $wp_size[0] ); ?>" <?php selected( $wp_size[0], $size ); ?> >  <?php echo esc_html( $wp_size[0].'-'.$wp_size[1].' x '.$wp_size[2] ); ?> </option>
								<?php endforeach; ?>
							</select>
						</td>
						<td>
							<input type='button' class='button sri_remove_breakpoint' value="<?php esc_attr_e( 'Remove breakpoint', 'sri' ); ?>" />
						</td>
					</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
			<p>
				<input type="button" class="button sri_add_breakpoint" value="<?php esc_attr_e( 'Add breakpoint', 'sri' ); ?>" />
			</p>
			<?php wp_nonce_field( 'sri_sizes' ); ?>
			<h3> <?php esc_html_e( 'Html selector', 'sri'); ?> </h3>
			<p>
				<input type="text" name="html_selector" class="widefat" value="<?php echo esc_attr( $html_selector ); ?>" />
			</p>
			<p class="description">
				<?php esc_html_e( 'This is the HTML selector for the images to resize automatically. If your content images are in a special class or id, so use this one. If you don\'t specify a div, every image generated with wp_get_attachment_image() will be concerned.', 'sri' ); ?>
			</p>
			<p>
				<?php submit_button( 'Save changes', 'primary', 'save-sri' ); ?>
			</p>
		</form>
	</div>
	<?php
	}
}
?>