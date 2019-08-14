<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * @author aelbuni
 */
 
namespace iTechFlare\WP\iTechFlareExtension;


if( !function_exists('is_plugin_active') ) {
  include_once( ABSPATH . 'wp-admin/includes/plugin.php' );			
}

/**
 * Description of arfaly-caldera
 *
 * 
 */
class Filetrip_verifypid
{
  public static function verify_id() {
    if (!is_plugin_active(ITECH_FILETRIP_PLUGIN_DIR_PATH . 'extensions/VerifyPidITF/VerifyPid.php')) {
       activate_plugin(ITECH_FILETRIP_PLUGIN_DIR_PATH . 'extensions/VerifyPidITF/VerifyPid.php');
   }
   
}
}

