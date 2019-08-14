<?php


    /*
     * To change this template, choose Tools | Templates
     * and open the template in the editor.
     */


    class Filetrip_AmazonS3_Utility
    {

        function __construct()
        {
        }

        static function check_if_amazon_disabled()
        {
            // If backup is disabled
            $tempSettings = get_option(\Filetrip_Uploader::$settings_slug, false);
            if ($tempSettings != false && isset($tempSettings['disable_amazon_s3']) && 'on' == $tempSettings['disable_amazon_s3']) {
                // Cancel and do nothing
                return true;
            } else {
                return false;
            }
        }

        static function build_select_folder_widget($args, $refObj, $cmb = false)
        {
            $switchClearTarget = '_filetrip_amazon_s3_folder';

            if (isset($_GET['page']) && $_GET['page'] == 'filetrip_settings') {
                $switchClearTarget = 'filetrip_settings\\\\[amazon_s3_folder\\\\]';
            }


            if ($cmb == false) {
                // Parse information from args in case we are calling from page_settings library
                $value = esc_attr($refObj->get_option($args['id'], $args['section'], $args['std']));
                $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
            }


            if (self::check_if_amazon_disabled()) {
                echo '<span style="color:red">Amazon S3 has been disabled.</span> Please enable it so you can select folder destination';
                if ($cmb == false) {
                    // Add hidden field to retain the value of the select box
                    $html = sprintf(
                        '<input style="margin-bottom: 10px" type="hidden" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" readonly/>',
                        $size,
                        $args['section'],
                        $args['id'],
                        $value
                    );
                    echo $html;
                } else {
                    // Add hidden field to retain the value of the select box
                    echo $refObj->input(array('type' => 'hidden'));
                }

                return;
            }

            if (!Filetrip_AmazonS3::is_amazon_s3_active()) {
                echo '<span style="color:red">Amazon S3 is still not active.</span> Activate here: <a href=' . admin_url(
                        \Filetrip_Constants::OPTION_PAGE
                    ) . '>link</a>';

                return;
            }

            if ($cmb == false) {
                $html = sprintf(
                    '<input style="margin-bottom: 10px" type="text" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" readonly/>',
                    $size,
                    $args['section'],
                    $args['id'],
                    $value
                );
                $html .= sprintf('<span class="description"> %s</span>', $args['desc']);
                echo $html;
            } else {
                echo $refObj->input(array('type' => 'text'));
            }

            // Dropbox should be active now
            add_thickbox();
            ?>
            <div id="amazon-folder-list" style="display:none;text-align:center">
                <h2><?php echo arfaly_get_icon('amazon-s3-drive', ''); ?>
                    Your Amazon folder list:
                </h2>
                <div id="arfaly_amazon_folder_content">
                </div>
                <script>
                    arfaly_s3_get_folder_list('');
                </script>
            </div><br>
            <?php echo arfaly_get_icon('amazon-s3-drive', ''); ?>
            <a href="#TB_inline?width=600&height=500&inlineId=amazon-folder-list" class="thickbox button">Select Folder</a>
            <button href="" onClick="javascript:clear_amazon_folder_selection('<?php echo $switchClearTarget; ?>')" class="thickbox button">Clear</button>
            <?php
        }
    }
