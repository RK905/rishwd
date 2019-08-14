<?php
/**
 * Filetrip Uploader preview
 *
 * @package   Filetrip_Post_Type
 * @author    Abdulrhman Elbuni
 * @license   GPL-2.0+
 * @copyright 2013-2014
 *
 **/
 
if(!class_exists('Filetrip_Preview')){
 
  	class Filetrip_Preview {

	   public function __construct() {
		   add_filter( 'single_template', array( $this, 'filetrip_template') );
    	}

		/*********** function for including uploader preview file ***************/
		public function filetrip_template($single) {
		        global $wp_query, $post;
				/* Checks for single template by post type */
				$classobj = new Filetrip_Preview();
				if ($post->post_type == \Filetrip_Constants::POST_TYPE ) {
					if (file_exists( ITECH_FILETRIP_LIB_EXTENSION_DIR . 'classes/templates/single-filetrip.php' ) ) {
							return ITECH_FILETRIP_LIB_EXTENSION_DIR . 'classes/templates/single-filetrip.php';
					}
				}
				return $single;
		}
		
 	}
}
$obj = new Filetrip_Preview();