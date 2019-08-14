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
class Filetrip_Contact7
{
    protected static $instance = null;
    function __construct()
    {
        // If form integration is enabled, ask the user to install Contact Forms 7
        if (class_exists('WPCF7')) {
            add_action('wpcf7_init', array($this, 'set_filetrip_field_type'), 1);
            add_action('wpcf7_admin_init', array($this, 'filetrip_tag_generator'), 25);
            add_filter( 'wpcf7_form_enctype', array($this,'filetrip_file_form_enctype_filter') );
            add_filter('wpcf7_mail_components', array($this, 'embed_upload_links_in_body'), 10, 3);
            add_action('wp_enqueue_scripts', array($this, 'load_cf7_viewer_scripts'));
            add_action('admin_enqueue_scripts', array($this, 'load_cf7_admin_scripts'));
        }else{
            add_action('admin_notices', array($this, 'my_admin_error_notice'));
        }
    }
    /**
     * Set new Contact Form element for Arfaly
     *
     */
    function set_filetrip_field_type($fields)
    {
        wpcf7_add_form_tag('filetrip_uploader', array($this, 'fitetrip_tag_insert'), array('name-attr' => true));
        wpcf7_add_form_tag('filetrip_uploader*', array($this, 'fitetrip_tag_insert'), array('name-attr' => true));
    }
    /**
     * @param $tag
     * @return string
     */
    function fitetrip_tag_insert($tag)
    {
        if ( empty( $tag->name ) ) {
            return '';
        }
        ob_start();
        ?>
        <div class="filetirp-cf">
            <?php
            if(isset($tag->get_option('filetrip_shortcode_id')[0]) && intval($tag->get_option('filetrip_shortcode_id')[0])>=1)
            {
                echo \Filetrip_Uploader::building_arfaly_container($tag->get_option('filetrip_shortcode_id')[0], $tag->is_required());
            }else{
                echo __("Please update Filetrip shortcode ID in your form to be able to render the uploader", 'filetrip-plugin');
            }

            ?>
            <input type="hidden" data-required="<?php echo esc_attr($tag->is_required()) ?>" id="filetrip-cf" name="<?php echo esc_attr($tag->name); ?>">
        </div>
        <?php
        $output = ob_get_clean();
        return $output;
    }

    /**
     * Tag generot form filetrip field
     */
    function filetrip_tag_generator()
    {
        if (class_exists('WPCF7_TagGenerator')) {
            $tag_generator = \WPCF7_TagGenerator::get_instance();
            $tag_generator->add('filetrip', __('Filetrip', 'filetrip-plugin'),
                array($this, 'filetrip_tag_generator_function'));
        }

    }
    /**
     * Tag Generator Form
     * @param $contact_form
     * @param string $args
     */
    function filetrip_tag_generator_function($contact_form, $args = '')
    {
        $args = wp_parse_args($args, array());
        ?>
        <div class="control-box">
            <fieldset>
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row"><?php echo esc_html(__('Field type', 'filetrip-plugin')); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><?php echo esc_html(__('Field type', 'filetrip-plugin')); ?></legend>
                                <label><input type="checkbox"
                                              name="required"/> <?php echo esc_html(__('Required field', 'filetrip-plugin')); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label
                                    for="<?php echo esc_attr($args['content'] . '-name'); ?>"><?php echo esc_html(__('Name', 'filetrip-plugin')); ?></label>
                        </th>
                        <td><input type="text" name="name" class="tg-name oneline"
                                   id="<?php echo esc_attr($args['content'] . '-name'); ?>"/></td>
                    </tr>

                    <tr>
                        <th scope="row"><label
                                    for="<?php echo esc_attr('filetrip_shortcode_id'); ?>"> <?php echo esc_html(__('Filetrip Shortcode ID', 'filetrip-plugin')); ?></label>
                        </th>
                        <td>
                            <select name="shortcode" id="<?php echo esc_attr('filetrip_shortcode_id'); ?>">
                                <option value="">select</option>
                                <?php
                                $wp_query = new \WP_Query();
                                $wp_query->query('showposts=-1&post_type=' . \Filetrip_Constants::POST_TYPE);
                                while ($wp_query->have_posts()) : $wp_query->the_post();
                                    ?>
                                    <option value="<?php echo get_the_ID(); ?>"><?php echo get_the_title(); ?></option>
                                    <?php
                                endwhile;
                                ?>
                            </select>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
        <!--        readonly="readonly"-->
        <div class="insert-box">
            <input type="text" name="filetrip_uploader" class="tag code" onfocus="this.select()"/>
            <div class="submitbox">
                <input type="button" class="button button-primary insert-tag"
                       value="<?php echo esc_attr(__('Insert Tag', 'filetrip-plugin')); ?>"/>
            </div>
            <br class="clear"/>
            <p class="description mail-tag"><label
                        for="<?php echo esc_attr($args['content'] . '-mailtag'); ?>"><?php echo sprintf(esc_html(__("Don't need to insert the corresponding filetrip-tag into the field on the Mail tab.Filetrip will attach image automatically", 'filetrip-plugin')), '<strong><span class="mail-tag"></span></strong>'); ?>
                    <input type="text" class="mail-tag code hidden" readonly="readonly"
                           id="<?php echo esc_attr($args['content'] . '-mailtag'); ?>"/></label></p>
        </div>
        <?php
    }

    /* Encode type filter */
    function filetrip_file_form_enctype_filter( $enctype ) {
        $multipart = (bool) wpcf7_scan_form_tags( array( 'type' => array( 'filetrip_uploader', 'filetrip_uploader*' ) ) );
        if ( $multipart ) {
            $enctype = 'multipart/form-data';
        }
        return $enctype;
    }
    /**
     * Automatically Added filetrip field in Contact FROM 7
     * @param $components
     * @param $form
     * @return mixed
     */
    function embed_upload_links_in_body($components, $form, $cf7_mail)
    {
        $filetrip_tag_pattern = '/\{.*filetrip.*}/i';

        $submission = \WPCF7_Submission::get_instance();
        $submited['posted_data'] = $submission->get_posted_data();

        $upload_links_excerpt_html = '<div><ul>';
        $upload_links_excerpt_non_html = '';

        if (array_key_exists('image-id', $submited['posted_data'])) ;
        {
            foreach($submited['posted_data']['image-id'] as $id){
                // Notify channels to get the shared link URL for 
                $shared_url_links = [];
                $shared_url_links = apply_filters('itf/filetrip/filter/channels/get/shared/link', $shared_url_links, $id);

                if(!empty($shared_url_links)){
                    $file_url = $shared_url_links[0];
                    
                    $upload_links_excerpt_html = $upload_links_excerpt_html . '<li><a href="'.$file_url.'">'.basename(urldecode($file_url)).'</a></li>';
                    $upload_links_excerpt_non_html = $upload_links_excerpt_non_html . $file_url . ' || ';
                }
            }
        }

        // Check if the use_html is selected or not?
        if($this->get_use_html($cf7_mail)){
            $upload_links_excerpt_html = $upload_links_excerpt_html . '</ul></div>';
            $components['body'] = preg_replace($filetrip_tag_pattern, $upload_links_excerpt_html, $components['body']);
        }else{
            $components['body'] = preg_replace($filetrip_tag_pattern, $upload_links_excerpt_non_html, $components['body']);
        }        

        return $components;
    }


    /**
     * Load Admin Scripts
     */
    function load_cf7_admin_scripts()
    {
        wp_enqueue_script('cf7_tag_generator', plugin_dir_url(__FILE__) . '../../assets/js/cf7-tag-generator.js', array(), \Filetrip_Constants::VERSION, true);
    }
    /**
     * Load Viewer Scripts
     */
    function load_cf7_viewer_scripts()
    {

        wp_enqueue_script('c7-filetrip.js', plugin_dir_url(__FILE__) . '../../assets/js/c7-filetrip.js', array(), \Filetrip_Constants::VERSION, true);
    }
    /**
     * Admin notice if Contact From 7 not installed
     */
    function my_admin_error_notice()
    {
        $msg = 'The ' . \Filetrip_Constants::PLUGIN_NAME . ' plugin needs <a target="_blank" href="https://en-ca.wordpress.org/plugins/contact-form-7/">Contact Form 7</a> so you can build forms';
        $class = "error";
        echo '<div class="' . $class . '"> <p>' . $msg . '</p></div>';
    }
    /**
     * Return an instance of this class.
     *
     *
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

    public function get_use_html($wpcf7_mail)
    {
        $use_html = false;
        if (strnatcmp(phpversion(),'7.0.0') >= 0)
        {
            $use_html="\0WPCF7_Mail\0use_html";
            $a = (array) $wpcf7_mail;
            return $a[$use_html];
        }
        else
        {
            return $use_html;
        }
    }
}
