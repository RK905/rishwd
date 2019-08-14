<?php

#define demo

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */



    class Filetrip_AmazonS3_Setting_Page
    {
        static public $amazon_s3_setting_page = 'filetrip_amazon_s3_setting';

        function __construct() {
            if(!Filetrip_AmazonS3_Utility::check_if_amazon_disabled()){
                add_filter( 'itf/filetrip/settings/add/section', array( $this, 'add_setting_section' ), 1);
                add_filter( 'itf/filetrip/settings/add/section_fields', array( $this, 'add_setting_fields' ), 1);
                add_action( 'itf/filetrip/settings/page/header' , array( $this, 'print_html_in_page_header' ), 1);
            }
        }

        function add_setting_section($sections)
        {
            $sections[] = array(
                'id' => Filetrip_Constants::POST_TYPE.'_amazon_s3_setting',
                'title' => __( 'Amazon S3 API settings', 'filetrip-plugin' ),
            );

            return $sections;
        }

        function add_setting_fields($fields)
        {
            $channelSettings = array(
                array(
                    'name' => 'amazon_key_ID',
                    'label' => __( 'Amazon Key ID', 'filetrip-plugin' ),
                    'desc' => __( 'You shall create your own app <a target="_blank" href="https://console.aws.amazon.com"><b>Create your Amazon application now</b></a>', 'filetrip-plugin' ),
                    'type' => 'text',
                    'default' => '',
                ),
                array(
                    'name' => 'amazon_secret_access',
                    'label' => __( 'Amazon Secret Access', 'filetrip-plugin' ),
                    'desc' => __( 'Amazon Secret Access', 'filetrip-plugin' ),
                    'type' => 'text',
                    'default' => ''
                ),
                array(
                    'name' => 'amazon_bucket_name',
                    'label' => __( 'Amazon Bucket Name', 'filetrip-plugin' ),
                    'desc' => __( 'Please Enter a Bucket name', 'filetrip-plugin' ),
                    'type' => 'text',
                    'default' => ''
                ),
                array(
                    'name'    => 'amazon_bucket_region_name',
                    'label'    => __( 'Select the region where your bucket is hosted.', 'filetrip-plugin' ),
                    'type'    => 'select',
                    'default' => 'ca-central-1',
                    'options' => array(
                        'us-east-2' => __( 'US East (Ohio)', 'filetrip-plugin' ),
                        'us-east-1' => __( 'US East (N. Virginia)', 'filetrip-plugin' ),
                        'us-west-1' => __( 'US West (N. California)', 'filetrip-plugin' ),
                        'us-west-2' => __( 'US West (Oregon)', 'filetrip-plugin' ),
                        'ca-central-1' => __( 'Canada (Central)', 'filetrip-plugin' ),
                        'ap-south-1' => __( 'Asia Pacific (Mumbai)', 'filetrip-plugin' ),
                        'ap-northeast-2' => __( 'Asia Pacific (Seoul)', 'filetrip-plugin' ),
                        'ap-southeast-1' => __( 'Asia Pacific (Singapore)', 'filetrip-plugin' ),
                        'ap-southeast-2' => __( 'Asia Pacific (Sydney)', 'filetrip-plugin' ),
                        'ap-northeast-1' => __( 'Asia Pacific (Tokyo)', 'filetrip-plugin' ),
                        'eu-central-1' => __( 'EU (Frankfurt)', 'filetrip-plugin' ),
                        'eu-west-1' => __( 'EU (Ireland)', 'filetrip-plugin' ),
                        'eu-west-2' => __( 'EU (London)', 'filetrip-plugin' ),
                        'sa-east-1' => __( 'South America (São Paulo)', 'filetrip-plugin' )
                    )
                )
            );

            $fields[Filetrip_AmazonS3_Setting_Page::$amazon_s3_setting_page] = $channelSettings;

            return $fields;
        }

        function print_html_in_page_header()
        {
            $drive_auth_state = false;
            // Check if Drive setting is complete
            $S3settings = (array)get_option( Filetrip_AmazonS3_Setting_Page::$amazon_s3_setting_page);
            if(isset($S3settings['amazon_key_ID']) && isset($S3settings['amazon_secret_access']) && isset($S3settings['amazon_bucket_name'])
                && !Filetrip_AmazonS3::is_amazon_s3_active())
            {
                $s3_auth_state = true;
            }else{
                $s3_auth_state = false;
            }

            if(!Filetrip_AmazonS3_Utility::check_if_amazon_disabled() && Filetrip_AmazonS3::is_amazon_s3_active())
            {
                ?>
                <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
                    <p><strong>Your Amazon S3 is active.</strong></p>
                </div>
                <?php
            }


            if(!Filetrip_AmazonS3_Utility::check_if_amazon_disabled() && $s3_auth_state)
            {
                ?>
                <div class="error settings-error notice is-dismissible">
                    <b>Make sure to fill the correct Amazon S3 credential. Then, please click over the 'Save Changes' button to get your plugin activated.</b>
                </div>
                <?php
            }
        }

    }

