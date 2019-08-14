<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace iTechFlare\WP\iTechFlareExtension;

/**
 * Description of arfaly-caldera
 *
 * @author aelbuni
 */

class Filetrip_Ninja
{

    protected static $instance = null;
    private $filetrip_shorcode_id = array();

    function __construct()
    {


//         If form integration is enabled, ask the user to install Ninja Forms
        if (!function_exists('Ninja_Forms')) {
            add_action('admin_notices', array($this, 'my_admin_error_notice'));
        }else{
            require_once (plugin_dir_path(__FILE__) . '/filetrip-ninja-field.php');
            //Register Filetrip field
            add_filter('ninja_forms_register_fields', array($this,'register_filetrip_field_in_ninja_form') );
            //Register New file path for NInja form field template
            add_filter('ninja_forms_field_template_file_paths', array($this, 'ninja_filetrip_field_template_path'));
           //ninja Localize script
            add_filter( 'ninja_forms_localize_fields', array( $this, 'enqueue_scripts' ) );
            add_filter( 'ninja_forms_localize_fields_preview', array( $this, 'enqueue_scripts' ),0 );
        }
    }

/**
     * Register Filetrip field
     * @param $fields
     * @return mixed
     */
    function register_filetrip_field_in_ninja_form($fields) {
        $fields['filetrip'] = new \NF_Filetrip_Field;
        return $fields;
    }



    /**
     * Register New file path for NInja form field template
     * @param $paths
     * @return array
     */
    function ninja_filetrip_field_template_path($paths)
    {

        $paths[] = plugin_dir_path(__FILE__) . '../templates/';
        return $paths;
    }

    /**
     * send filetrip post meta value as json
     * @param $field
     * @return mixed
     */
    function enqueue_scripts( $field ) {
        $settings = is_object( $field ) ? $field->get_settings() : $field['settings'];

        if($settings['type']=='filetrip'){

            if(empty($settings['filtrip_shortcode'])){
                die(' Please Select A Valid Filetrip Shortcode');
            }

            wp_enqueue_script('ninja-filetrip.js', plugin_dir_url(__FILE__) . '../../assets/js/ninja.js', array(
                'jquery',
                'nf-front-end',
            ), \Filetrip_Constants::VERSION, true);


            $this->filetrip_shorcode_id[(int)$settings['filtrip_shortcode']]=\Filetrip_Uploader::building_arfaly_metadata_array((int)$settings['filtrip_shortcode']);

            // Make Ajax data dynamic to allow more than one uploader to be placed easily within a single form
            wp_localize_script('ninja-filetrip.js', 'filetrip_options', array_unique($this->filetrip_shorcode_id));

        }

        return $field;
    }


    function my_admin_error_notice()
    {
        $msg = 'The ' . \Filetrip_Constants::PLUGIN_NAME . ' plugin needs <a target="_blank" href="https://wordpress.org/plugins/ninja-forms/">Ninja Forms</a> so you can build forms';
        $class = "error";
        echo '<div class="' . $class . '"> <p>' . $msg . '</p></div>';
    }

    /**
     * Return an instance of this class.
     * @return    object    A single instance of this class.
     */
    public static function get_instance()
    {

        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }


}

