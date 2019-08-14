<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of arfaly-caldera
 *
 * @author aelbuni
 */

class NF_Filetrip_Field extends NF_Abstracts_Field
{
    protected $_name = 'filetrip';

    protected $_section = 'common';

    protected $_icon = 'upload';

    protected $_aliases = array();

    protected $_type = 'filetrip';

    protected $_templates = 'filetrip';

    protected $_test_value = '0';

    protected $processing_fields = array('quantity', 'modifier', 'shipping', 'tax', 'total');

    protected $_settings_only = array('label', 'label_pos', 'filtrip_shortcode', 'required');

    //protected $_settings_exclude = array('input_limit_set', 'disable_input');

    public function __construct()
    {
        parent::__construct();

        $settings['filtrip_shortcode'] = array(
            'name' => 'filtrip_shortcode',
            'type' => 'select',
            'label' => __('Display This filetrip shortcode', 'ninja-forms'),
            'width' => 'full',
            'group' => 'primary',
            'options' => $this->option_return(),
            'value' => '',

        );

        add_filter( 'ninja_forms_custom_columns', array( $this, 'filetrip_custom_columns' ), 10, 2 );

        $this->_settings = array_merge($this->_settings, $settings);

        $this->_nicename = __('Filetrip', 'ninja-forms');


    }

    /**Get all filtrip post array element
     * @return array
     */

    function option_return()
    {

        $options_array = array();
        $args = array(
            'posts_per_page' => -1,
            'post_type' => \Filetrip_Constants::POST_TYPE,
            'post_status' => 'publish',
        );
        $posts_array = get_posts($args);

	    $options_array[] = ['label'=>esc_html('Select A Filetrip Shortcode','filetrip-plugin'),'value' => ''];


        if (count($posts_array) > 0) {
            foreach ($posts_array as $value) {

                array_push($options_array, array(
                    'label' => $value->post_title,
                    'value' => $value->ID
                ));
            }

        }

        return $options_array;
    }

    /**
     * Admin Form Element
     *
     * Returns the output for editing fields in a submission.
     *
     * @param $id
     * @param $value
     * @return string
     */
    public function admin_form_element( $id, $value )
    {
        if( ! is_array( $value ) ) $value = array( $value );

        $list = "";

        foreach($value as $upload)
        {
            $list = $list . '<li><a target="_blank" href="'.$upload.'">'.basename($upload).'</a></li>';            
        }
        return '<ol class="widefat" name="fields[' . $id . ']">'.$list.'</ol>';
    }

    /**
     * Process Data before submit
     * @param $field
     * @param $data
     * @return mixed
     */
    public function process($field, $data)
    {

      $field_id = $field['id'];
      $fields = $data['fields'];
      $filetrip_id = $field['settings']['filtrip_shortcode'];

      foreach ($fields as $field){
          if($field['id']==(int)$field_id){
              $uploads = array();
              $get_image_id = $field['value'];
              $get_image_id = explode(',',$get_image_id);

              foreach( $get_image_id as $att ){
                  // Notify channels to get the shared link URL for 
                  $shared_url_links = [];
                  $shared_url_links = apply_filters('itf/filetrip/filter/channels/get/shared/link', $shared_url_links, $att);

                  if(!empty($shared_url_links)){
                    $uploads[] = '<a href="' . $shared_url_links[0] . '" >' . basename($shared_url_links[0]) . '</a> ';
                  }
              }

              $data['fields'][(int)$field_id]['value']=$uploads;
          }

      }

        // On submit processing code goes here
        return $data;
    }

    /**
     * Custom representation for Filetrip column
     * @param $value
     * @param $field
     * @return mixed
     */
    public function filetrip_custom_columns( $value, $field )
    {
        // Exit if the field type is not Filetrip
        if( $this->_name != $field->get_setting( 'type' ) ) return $value;

        if( ! is_array( $value ) ) $value = array( $value );

        $list = '';

        foreach($value as $upload)
        {
            $list = $list . '<li><a target="_blank" href="'.$upload.'">'.basename($upload).'</a></li>';
        }

        return '<ol class="widefat">'.$list.'</ol>';

    }

}





