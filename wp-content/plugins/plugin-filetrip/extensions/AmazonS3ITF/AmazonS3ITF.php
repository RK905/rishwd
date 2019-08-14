<?php

namespace iTechFlare\WP\iTechFlareExtension;

use iTechFlare\WP\Plugin\FileTrip\Core\Abstracts\FlareExtension;
use iTechFlare\WP\Plugin\FileTrip\Core\Helper\LoaderOnce;
use Aws\S3\S3Client;

require 'lib/vendor/autoload.php';

/**
 * Class AmazonS3ITF
 * @package iTechFlare\WP\iTechFlareExtension
 */
class AmazonS3ITF extends FlareExtension
{
    /**
     * Channel Name
     *
     * @var string
     */
    protected $extension_name = 'Amazon S3 Channel';

    /**
     * @var string
     */
    protected $extension_uri = 'https://itechflare.com'; // with or without http://

    /**
     * @var string
     */
    protected $setting_field_type = 'amazon_s3_select';

    /**
     * @var string
     */
    protected $meta_field_type = 'amazon_s3_folder_select';

    /**
     * @var string
     */
    protected $select_folder_id = 'amazon_s3_folder';

    /**
     * @var string
     */
    protected $field_slug = 'amazon-s3-drive';

    /**
     * @var string
     */
    protected $field_name = 'Amazon S3';

    /**
     * @var string
     */
    protected $ajax_action_ref = 'amazon_s3_send_file';

    /**
     * @var string
     */
    protected $extension_author = 'iTechFlare';

    /**
     * @var string
     */
    protected $extension_author_uri = 'https://itechflare.com';
    
    protected $database_fields = array(
		\Filetrip_Constants::CDN_URL => "s3_cdn_url",
		\Filetrip_Constants::SHARED_URL => "s3_shared_url"
    );
    
    /**
     * @var string
     */
    protected $extension_version = '1.0';

    /**
     * @var string
     */
    protected $extension_description = 'Activate to start transferring Filetrip Uploads to Amazon S3. Go to Settings to complete your configuration';

	protected $filetrip_s3_link_met = 'filetrip_s3_link';
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        LoaderOnce::load( __DIR__ .'/s3/Filetrip_AmazonS3_Utility.php');
        LoaderOnce::load( __DIR__ .'/s3/Filetrip_AmazonS3.php');

        define("AmazonS3ITF_ACTIVE", true);

        // Filters
        // Filters
        add_filter( 'itf/filetrip/channel/cdn_selection', array( $this, 'add_cdn_option_to_dropmenu' ) );
        add_filter( 'itf/filetrip/channel/selection_filter', array($this, 'add_meta_channel_configuration'));
        add_filter( 'itf/filetrip/settings/add/section_fields', array($this, 'add_setting_field'));

        // Register this channel to the core channel distributor [Filetrip_Distributor]
        add_filter( 'itf/filetrip/filter/register/channels', array($this, 'register_me_to_channels'));
        add_filter( 'itf/filetrip/filter/channels/media/dest', array($this, 'register_me_as_forwarder'), 10, 2);

        add_filter( 'itf/filetrip/filter/channels/get/shared/link', array($this, 'get_shared_link_if_avaliable'), 10, 2);

        // Amazon meta type to render folder selection widget
        add_action( 'wp_ajax_' . $this->ajax_action_ref, array($this, 'send_media_to_cloud'), 10 );
        add_action( 'wp_ajax_' . $this->ajax_action_ref . '_backup', array($this, 'send_file_to_cloud'), 10 );
        add_action( 'filetrip_cmb_render_' . $this->meta_field_type, array($this, 'folder_select_widget'), 10, 5 );
        add_action( 'itf/main_setting/add_field/' . $this->setting_field_type, array($this, 'add_settings_field_callback'), 10, 4);
        add_action( 'admin_init', array($this ,'init_admin_hooks') );

        // Deamon transfer / CLI Mode: Transfer files when they are been auto-approve
        add_action( 'itf/filetrip/upload/forward/me', array( $this, 'CLI_forward_upload' ), 10, 3 );
        //generate shared link
        add_action( 'itf/filetrip/amazons3/file/uploded', array( $this, 'save_amazons3_shared_link' ), 10, 2 );


        $this->amazon_settings = (array)get_option(\Filetrip_AmazonS3_Setting_Page::$amazon_s3_setting_page);

        /**
         * Hook to admin footer to inject jQuery for Media action addition
         */
        add_action('admin_footer-upload.php', array( $this, 'custom_bulk_admin_media_library_footer'));

        //================ Amazon Initiation and handling =================
        $amazon_obj = new \Filetrip_AmazonS3($this->amazon_settings);

    }

    function add_cdn_option_to_dropmenu($link_upload_cdn_options)
    {
        if(!isset($_REQUEST['post'])) return $link_upload_cdn_options;

        $uploader_id = intval($_REQUEST['post']);
        $selectedChannels = \Filetrip_Channel_Utility::get_channel_selected( $uploader_id );

        // If channel list is empty exit
        if(empty($selectedChannels)) return $link_upload_cdn_options;

		foreach ( $selectedChannels as $select ) {
			if ( $select == $this->field_slug ) {
                $link_upload_cdn_options[$this->filetrip_s3_link_met] =  arfaly_get_icon('amazon-s3-drive', ' S3 Link');
			}
		}

        return $link_upload_cdn_options;
    }

	function save_amazons3_shared_link( $result, $att_id ) {
		$att_info         = get_post( $att_id );
        $selectedCdnChannel = \Filetrip_Channel_Utility::get_generate_cdn_selected( $att_info->post_parent );
        
		if ( $selectedCdnChannel == $this->filetrip_s3_link_met ) {
			if ( $result && $att_id ) {
				$shared_url = $result['ObjectURL'];
				if ( $shared_url ) {
                    update_post_meta( $att_id, $this->filetrip_s3_link_met, $shared_url );
                    
                    //Insert metadata to filetrip metadata database
                    global $wpdb;
                    $table_name  = $wpdb->prefix . \Filetrip_Constants::METADATA_TABLE_NAME;
                    $data = array(
                        'att_id' => $att_id, 
                        'meta_key' => $this->database_fields[\Filetrip_Constants::SHARED_URL], 
                        'meta_val' => $shared_url
                    );
                    $format = array('%d', '%s', '%s');

                    $wpdb->insert( $table_name, $data, $format);
				}
			}
		}
	}

	function get_meta_destination_folder( $post_id ) {
		$meta = get_post_meta( $post_id );
		if ( isset( $meta[ \Filetrip_Constants::METABOX_PREFIX . $this->select_folder_id ] ) ) {
			$folder = $meta[ \Filetrip_Constants::METABOX_PREFIX . $this->select_folder_id ][0];

			return $folder;
		}

		return false;
	}
    /**
     * @param array
     *      This function responsible of registering this channel to the core
     *		Filetrip channel distributor [Filetrip_Distributor]. Also it should pass
     *		all the necessary information to the client js.
     */
    function register_me_to_channels($channels)
    {
        /**
         ** Channel array structure:
        array(
        'channel_key' => 'key',
        'channel_name' => 'name',
        'channel_icon' => 'path/img',
        'channel_action_url' => 'action_url',
        'active' => true|false
        )
         **/

        $filetrip_settings = \Filetrip_Uploader::get_filetrip_main_settings();

        $destination = isset($filetrip_settings[$this->select_folder_id])?$filetrip_settings[$this->select_folder_id]:'';

        $channels[$this->field_slug] = array(
            'destination' => $destination,
            'channel_key' => $this->field_slug,
            'channel_name' => $this->field_name,
            'channel_icon' => $this->get_icon('', '', '20', ''),
            'channel_action_url' => $this->ajax_action_ref,

            'active' => \Filetrip_AmazonS3::is_amazon_s3_active(),
            'security' => wp_create_nonce( $this->ajax_action_ref )
        );

        return $channels;
    }
    function folder_select_widget($field_args, $escaped_value, $object_id, $object_type, $field_type_object ) {
        \Filetrip_AmazonS3_Utility::build_select_folder_widget($field_args, $field_type_object, true);
    }

    function custom_bulk_admin_media_library_footer() {

        ?>
        <script type="text/javascript">
            jQuery(document).ready(function() {
                jQuery('<option>').val('<?php echo $this->field_slug; ?>').text('<?php _e('Filetrip to '.$this->field_name, 'filetrip-plugin'); ?>').appendTo("select[name='action']");
                jQuery('<option>').val('<?php echo $this->field_slug; ?>').text('<?php _e('Filetrip to '.$this->field_name, 'filetrip-plugin'); ?>').appendTo("select[name='action2']");
            });
        </script>
        <?php
    }


	/**
	 * @param (int) ($uploader_id)
	 *      Gets attachment ID and find if this channel is selected as forwarding channel, and retrive destination.
	 */
	function register_me_as_forwarder( $forwardingChannels, $uploader_id ) {
		$post             = get_post( $uploader_id );
		$selectedChannels = \Filetrip_Channel_Utility::get_channel_selected( $post->post_parent );

        $destination = $this->get_meta_destination_folder( $post->post_parent );
        
        if(empty($selectedChannels)) return $forwardingChannels;

		foreach ( $selectedChannels as $select ) {
			if ( $select == $this->field_slug ) {
				$forwardingChannels[] = array(
					'destination'        => $destination,
					'channel_key'        => $this->field_slug,
					'channel_name'       => $this->field_name,
					'channel_icon'       => $this->get_icon( '', '', '20', 'Amazon S3' ),
					'channel_action_url' => $this->ajax_action_ref,
					'active'             => \Filetrip_AmazonS3::is_amazon_s3_active(),
					'security'           => wp_create_nonce( $this->ajax_action_ref )
				);
			}
		}

		return $forwardingChannels;
	}

	/**
	 * Responsible of forwarding uploads with auto-approve setting set to ON
	 */
	function CLI_forward_upload($att_id, $title, $description)
	{
		$att_info = get_post($att_id);
        $selectedChannels = \Filetrip_Channel_Utility::get_channel_selected($att_info->post_parent);
        
        // No channel selected
        if(empty($selectedChannels)) return;

		foreach($selectedChannels as $select)
		{
			if($select == $this->field_slug){
				// I am in his list of seleced channel. Let's send the file
				$file_path = get_attached_file( $att_id );
				$size = filesize( $file_path );
				$destination = $this->get_meta_destination_folder($att_info->post_parent);
				$username = 'Guest';
				$subfolder_enabled = false;

				if(isset($_GET['subfolder']) && $_GET['subfolder']!='false' ){
					$subfolder_enabled = true;
					$userid = $att_info->post_author;

					if($userid == 0)
					{
						$username = 'Guest';
					}else{
						$user_info = get_userdata($userid);
						$username = $user_info->user_login;
					}
				}


				\Filetrip_AmazonS3::resumable_file_upload($destination, $file_path, $size, $subfolder_enabled, $username, false,$att_id);
			}
		}
	}


	/**
     * @param string {folder_destination}:
     * @param string {targetFileURLPath}:
     * @param bool {subfolder_enabled}:
     * @param string {username}:
     *
     *	@return EventSource data message
     */
    function send_file_to_cloud( )
    {
        error_log('send_file_to_cloud');
        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache");

        /**
         *  Parse {security}
         */
        if( !isset($_GET['security']) || !wp_verify_nonce( $_GET['security'], $this->ajax_action_ref) ){
            /**
             *  Construct SSE message and echo it to the client
             */
            \Filetrip_Channel_Utility::sse_send_message("", "Invalid security check", "error");
            die();
        }

        /**
         *  Parse {$file_path}
         */
        if( !isset($_GET['file_path']) ){
            /**
             *  Construct SSE message and echo it to the client
             */
            \Filetrip_Channel_Utility::sse_send_message("", "FTP: No filepath was attached", "error");
            die();
        }
        $file_path = base64_decode($_GET['file_path']);

        /**
         *  Parse {subfolder}
         */
        $username = 'Guest';
        $subfolder_enabled = false;

        /**
         *  Parse {destination}
         */
        $destination = (isset($_GET['target_folder']) && $_GET['target_folder']!="false")?base64_decode($_GET['target_folder']).'/':'';

        \Filetrip_AmazonS3::resumable_file_upload($destination, $file_path, filesize($file_path), $subfolder_enabled, $username);

    }

    /**
     * @param string {folder_destination}:
     * @param string {targetFileURLPath}:
     * @param bool {subfolder_enabled}:
     * @param string {username}:
     *
     *	@return EventSource data messageitf/filetrip/upload/forward/me
     */
    function send_media_to_cloud( )
    {
        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache");


        /**
         *  Parse {mediaID}
         */
        if( !isset($_GET['mediaID']) || intval($_GET['mediaID']) <= 0 ){
            /**
             *  Construct SSE message and echo it to the client
             */
            Filetrip_Channel_Utility::sse_send_message("", "Invalid attachment ID", "error");
            die();
        }
        $att_id = intval($_GET['mediaID']);

        /**
         *  Parse {security}
         */
        if( !isset($_GET['security']) || !wp_verify_nonce( $_GET['security'], $this->ajax_action_ref) ){
            /**
             *  Construct SSE message and echo it to the client
             */
            Filetrip_Channel_Utility::sse_send_message("", "Invalid security check", "error");
            die();
        }

        /**
         *  Parse {subfolder}
         */
        $username = 'Guest';
        $subfolder_enabled = false;
        $post = get_post($att_id);

        if(isset($_GET['subfolder']) && $_GET['subfolder']!='false' ){
            $subfolder_enabled = true;
            $userid = $post->post_author;

            if($userid == 0)
            {
                $username = 'Guest';
            }else{
                $user_info = get_userdata($userid);
                $username = $user_info->user_login;
            }
        }

        /**
         *  Parse {subfolder}
         */
        $file_path = get_attached_file( $att_id );
        $destination = (isset($_GET['target_folder']) && $_GET['target_folder']!="false")?base64_decode($_GET['target_folder']).'/':'';


        \Filetrip_AmazonS3::resumable_file_upload($destination, $file_path, filesize($file_path), $subfolder_enabled, $username,true,$att_id);
    }


    function add_settings_field_callback( $args, $section, $option, $obj)
    {
        // Render my setting
        add_settings_field( $section . '[' . $option['name'] . ']', $option['label'],
            function() use($args, $obj) {
            \Filetrip_AmazonS3_Utility::build_select_folder_widget($args, $obj);

            }
            , $section, $section, $args );
    }


    public function get_icon($link = '', $title = '', $size = '30', $label = 'Amazon S3')
    {
        // If there is no link? Don't add href
        $hrefStart = '<a target="_blank" href="'.$link.'">';
        $hrefEnd = '</a> ';

        if($link == '') {
            $hrefStart = '';
            $hrefEnd = '';
        }

        return $hrefStart.'<img title="Google Drive" src="'.$this->extensionGetIcon().'" width="'. $size .'" height="'. $size .'">'.$hrefEnd." ".$label;
    }

    public function add_meta_channel_configuration($meta_config_array)
    {
        // Add checkbox field
        $meta_config_array['fields']['channels']['options'][$this->field_slug] = $this->get_icon();

        // Add folder selection field
        $meta_config_array['fields'][] = array(
            'name'    => 'Amazon S3 folder',
            'desc'    => 'Select the destination folder for your Amazon S3 channel',
            'id'      => \Filetrip_Constants::METABOX_PREFIX . $this->select_folder_id,
            'type'    => $this->meta_field_type,
            'attributes' => array('readonly'=>'')
        );

        return $meta_config_array;
    }

    public function add_setting_field($setting_array)
    {

        $setting_array[\Filetrip_Constants::POST_TYPE.'_settings'][] = array(
            'name' => $this->select_folder_id,
            'label' => __( 'Select Amazon S3 Folder', 'filetrip-plugin' ),
            'desc' => __( 'Select default Amazon S3 folder as Media destination', 'filetrip-plugin' ),
            'type' => $this->setting_field_type,
            'default' => '',
        );

        return $setting_array;
    }

    /**
     * Add a trigger to inject Channel icon in Media Column
     *
     * @return null
     */
    function init_admin_hooks() {
        // All hooks that need to be called at admin_init trigger should be placed hear
        add_action( 'manage_media_custom_column', array($this, 'insert_channel_hyperlinked_icon'), 10, 2 );
    }




    function insert_channel_hyperlinked_icon($column_name, $id)
    {
        switch($column_name)
        {
            case \Filetrip_Constants::MEDIA_COLUMN_SLUG;
                $query_s = admin_url(\Filetrip_Constants::FILETRIP_DISTRIBUTOR_PAGE);
                $query_s = $query_s.'&media='.$id.'&source='.\Filetrip_Constants::TransferType('media').'&';

                echo $this->get_icon($query_s.'channel='.$this->field_slug, 'Send file to Amazon S3', '20', '');

                break;
            default:
                break;
        }
    }

    public function init_s3_client($key, $secret, $region){

        $s3Client = new S3Client([
            'version'     => 'latest',
            'region'      => $region,
            'credentials' => [
                'key'    => $key,
                'secret' => $secret
            ]

        ]);
        return $s3Client;
    }

	/* Get shared link for an attachment if avaliable */
	function get_shared_link_if_avaliable($shared_url_links, $att_id)
	{
		$parent_id = \Filetrip_Channel_Utility::get_uploader_id_by_att_id( $att_id );
		if(!$parent_id) return $shared_url_links;

		$selectedCdnChannel = \Filetrip_Channel_Utility::get_generate_cdn_selected( $parent_id );

		if ( $selectedCdnChannel == $this->filetrip_s3_link_met ) {
				// Read shared CDN link from database
				global $wpdb;
				$table_name  = $wpdb->prefix . \Filetrip_Constants::METADATA_TABLE_NAME;
				$meta_key = $this->database_fields[\Filetrip_Constants::SHARED_URL];

				try{
					// Get meta_val for shared_url
					$result = $wpdb->get_row( "SELECT meta_val FROM $table_name WHERE att_id = $att_id AND meta_key = '$meta_key'", 
						ARRAY_A );

					if ($result['meta_val'] != null) {
						$shared_url_links[] = $result['meta_val'];
					}
				}catch(Exception $exp){
					error_log("Filetrip: " . $exp->getMessage());
				}
		}

		return $shared_url_links;
	}

}