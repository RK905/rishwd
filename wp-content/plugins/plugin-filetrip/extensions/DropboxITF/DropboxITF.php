<?php
/**
 * Example Extension
 *      Example extension just like below
 *      Use Extension Name Unique as Possible, because same Name Will Be [Override Able]
 *
 * @method init() as initialization after active
 */

namespace iTechFlare\WP\iTechFlareExtension;

use iTechFlare\WP\Plugin\FileTrip\Core\Abstracts\FlareExtension;
use iTechFlare\WP\Plugin\FileTrip\Core\Helper\LoaderOnce;

/**
 * Class Example
 * @package iTechFlare\WP\iTechFlareExtension
 */
class DropboxITF extends FlareExtension {
	/**
	 * @var string
	 */
	protected $extension_name = 'Dropbox Channel';

	/**
	 * @var string
	 */
	protected $extension_uri = 'https://itechflare.com'; // with or without http://

	/**
	 * @var string
	 */
	protected $extension_author = 'iTechFlare';

	/**
	 * @var string
	 */
	protected $extension_author_uri = 'https://itechflare.com';

	/**
	 * @var string
	 */
	protected $setting_field_type = 'dropbox_select';

	/**
	 * @var string
	 */
	protected $meta_field_type = 'dropbox_folder_select';

	/**
	 * @var string
	 */
	protected $meta_subfolder_id = 'enable_user_subfolder';

	/**
	 * @var string
	 */
	protected $select_folder_id = 'dropbox_folder';

	/**
	 * @var string
	 */
	protected $field_slug = 'dropbox';

	/**
	 * @var string
	 */
	protected $field_name = 'Dropbox';

	/**
	 * @var string
	 */
	protected $ajax_action_ref = 'dropbox_send_file';

	/**
	 * @var string
	 */
	protected $extension_version = '1.2';

	/**
	 * @var capability
	 */
	protected $capability = 'edit_posts';

	protected $filetrip_dropbox_link_met = 'filetrip_dropbox_link';

	protected $database_fields = array(
		\Filetrip_Constants::CDN_URL => "dropbox_cdn_url",
		\Filetrip_Constants::SHARED_URL => "dropbox_shared_url"
	);

	/**
	 * @var string
	 */
	protected $extension_description = 'Activate to start transferring Filetrip Uploads to Dropbox. Go to Settings to complete your configuration';
	/**
	 * @var string
	 *      fill with full URL to Extension icon
	 *      please use Square dimension :
	 *      128px square max 256px
	 *      Extension must be transparent png
	 */
	protected $extension_icon; // fill with icon url

	/**
	 * Initials
	 */
	public function init() {
		// do module
		// ************************* Bootstrapping *************
		//if (current_user_can($this->capability)) { // FIXED: To allow guests to forward files
		// include
		if ( ! class_exists( 'DropboxITF\Client' ) ) {
			LoaderOnce::load( __DIR__ . '/dropbox/lib/autoload.php' );
		}
		LoaderOnce::load( __DIR__ . '/dropbox/class-utilities.php' );
		LoaderOnce::load( __DIR__ . '/dropbox/class-filetrip-dropbox.php' );

		// Init
		define( "DropboxITF_ACTIVE", true );

		// Filters
		add_filter( 'itf/filetrip/channel/cdn_selection', array( $this, 'add_cdn_option_to_dropmenu' ) );
		add_filter( 'itf/filetrip/channel/selection_filter', array( $this, 'add_meta_channel_configuration' ) );
		add_filter( 'itf/filetrip/settings/add/section_fields', array( $this, 'add_setting_field' ) );

		// Register this channel to the core channel distributor [Filetrip_Distributor]
		add_filter( 'itf/filetrip/filter/register/channels', array( $this, 'register_me_to_channels' ) );
		add_filter( 'itf/filetrip/filter/channels/media/dest', array( $this, 'register_me_as_forwarder' ), 10, 2 );
		add_filter( 'itf/filetrip/filter/channels/get/shared/link', array($this, 'get_shared_link_if_avaliable'), 10, 2);

		// Dropbox meta type to render folder selection widget
		add_action( 'wp_ajax_' . $this->ajax_action_ref, array( $this, 'send_media_to_cloud' ), 10 );
		add_action( 'wp_ajax_' . $this->ajax_action_ref . '_backup', array( $this, 'send_file_to_cloud' ), 10 );
		add_action( 'filetrip_cmb_render_' . $this->meta_field_type, array( $this, 'folder_select_widget' ), 10, 5 );
		add_action( 'itf/main_setting/add_field/' . $this->setting_field_type, array(
			$this,
			'add_settings_field_callback'
		), 10, 4 );
		add_action( 'admin_init', array( $this, 'init_admin_hooks' ) );

		//save Dropbox link to database
		add_action( 'itf/filetrip/dropbox/file/uploaded', array($this,'save_dropbox_shared_link'), 10, 2 );

		// Deamon transfer / CLI Mode: Transfer files when they are been auto-approve
		add_action( 'itf/filetrip/upload/forward/me', array( $this, 'CLI_forward_upload' ), 10, 3 );

		/**
		 * Hook to admin footer to inject jQuery for Media action addition
		 */
		add_action( 'admin_footer-upload.php', array( $this, 'custom_bulk_admin_media_library_footer' ) );

		$this->dropbox_settings = (array) get_option( \Filetrip_Dropbox_Setting_page::$dropbox_settings_slug );

		//================ Dropbox Initiation and handling =================
		$dropbox_obj = new \Filetrip_Dropbox( $this->dropbox_settings );

		// Check if authorization action was triggered
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'do_dropbox_auth' && wp_verify_nonce( $_REQUEST['arfaly_nonce'], \Filetrip_Constants::NONCE ) ) {
			$dropbox_obj->auth_start();
		}
		// }
	}

	/***
	 * @param $result
	 * @param $att_id
	 */

	function save_dropbox_shared_link( $result, $att_id ) {

		$att_info         = get_post( $att_id );
		$selectedCdnChannel = \Filetrip_Channel_Utility::get_generate_cdn_selected( $att_info->post_parent );

		if ( $selectedCdnChannel == $this->filetrip_dropbox_link_met ) {
			if($result && $att_id){
					$server_file_path = $result['path_lower'];//file server path
					$shared_url = \Filetrip_Dropbox::get_share_link($server_file_path);
					if($shared_url){
						update_post_meta($att_id, $this->filetrip_dropbox_link_met,$shared_url);
						
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


	/**
	 * Responsible of forwarding uploads with auto-approve setting set to ON
	 */
	function CLI_forward_upload( $att_id, $title, $description ) {
		$att_info         = get_post( $att_id );
		if(empty($att_info)) return false;

		$selectedChannels = \Filetrip_Channel_Utility::get_channel_selected( $att_info->post_parent );

		// No channel selected
		if(empty($selectedChannels)) return;
		
		foreach ( $selectedChannels as $select ) {
			if ( $select == $this->field_slug ) {
				// I am in his list of seleced channel. Let's send the file
				$file_path   = get_attached_file( $att_id );
				$size        = filesize( $file_path );
				$destination = $this->get_meta_destination_folder( $att_info->post_parent );

				$username          = 'Guest';
				$subfolder_enabled = $this->get_subfolder_option_value( $att_info->post_parent );

				if ( $subfolder_enabled ) {
					$userid = $att_info->post_author;

					if ( $userid == 0 ) {
						$username = 'Guest';
					} else {
						$user_info = get_userdata( $userid );
						$username  = $user_info->user_login;
					}
				}

				\Filetrip_Dropbox::resumable_file_upload( $destination, $file_path, $size, $subfolder_enabled, $username, false, null, $att_id);
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

	function get_subfolder_option_value( $post_id ) {
		if ( empty( $post_id ) ) {
			return false;
		}

		$meta = get_post_meta( $post_id );
		if ( isset( $meta[ \Filetrip_Constants::METABOX_PREFIX . $this->meta_subfolder_id ] ) ) {
			$channel_selected = $meta[ \Filetrip_Constants::METABOX_PREFIX . $this->meta_subfolder_id ][0];

			return $channel_selected;
		}

		return false;
	}

	function custom_bulk_admin_media_library_footer() {

		?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery('<option>').val('<?php echo $this->field_slug; ?>').text('<?php _e( 'Filetrip to ' . $this->field_name, 'filetrip-plugin' ); ?>').appendTo("select[name='action']");
                jQuery('<option>').val('<?php echo $this->field_slug; ?>').text('<?php _e( 'Filetrip to ' . $this->field_name, 'filetrip-plugin' ); ?>').appendTo("select[name='action2']");
            });
        </script>
		<?php
	}

	/**
	 * @param string {folder_destination}:
	 * @param string {targetFileURLPath}:
	 * @param bool {subfolder_enabled}:
	 * @param string {username}:
	 *
	 * @return EventSource data message
	 * 
	 * This function is only responsible to upload backup files to the cloud. The cloud files
	 * don't have native identifiers, and this is why it accept the file path instead of $media_id
	 */
	function send_file_to_cloud() {
		header( "Content-Type: text/event-stream" );
		header( "Cache-Control: no-cache" );

		/**
		 *  Parse {security}
		 */
		if ( ! isset( $_GET['security'] ) || ! wp_verify_nonce( $_GET['security'], $this->ajax_action_ref ) ) {
			/**
			 *  Construct SSE message and echo it to the client
			 */
			\Filetrip_Channel_Utility::sse_send_message( "", "Invalid security check", "error" );
			die();
		}

		/**
		 *  Parse {$file_path}
		 */
		if ( ! isset( $_GET['file_path'] ) ) {
			/**
			 *  Construct SSE message and echo it to the client
			 */
			\Filetrip_Channel_Utility::sse_send_message( "", "FTP: No filepath was attached", "error" );
			die();
		}
		$file_path = base64_decode( $_GET['file_path'] );

		/**
		 *  Parse {subfolder}
		 */
		$username          = 'Guest';
		$subfolder_enabled = false;

		/**
		 *  Parse {destination}
		 */
		$destination = ( isset( $_GET['target_folder'] ) && $_GET['target_folder'] != "false" ) ? base64_decode( $_GET['target_folder'] ) : '';

		\Filetrip_Dropbox::resumable_file_upload( $destination, $file_path, filesize( $file_path ), $subfolder_enabled, $username );


	}

	/**
	 * @param string {folder_destination}:
	 * @param string {targetFileURLPath}:
	 * @param bool {subfolder_enabled}:
	 * @param string {username}:
	 *
	 * @return EventSource data message
	 */
	function send_media_to_cloud() {
		header( "Content-Type: text/event-stream" );
		header( "Cache-Control: no-cache" );

		/**
		 *  Parse {mediaID}
		 */
		if ( ! isset( $_GET['mediaID'] ) || intval( $_GET['mediaID'] ) <= 0 ) {
			/**
			 *  Construct SSE message and echo it to the client
			 */
			Filetrip_Channel_Utility::sse_send_message( "", "Invalid attachment ID", "error" );
			die();
		}
		$att_id = intval( $_GET['mediaID'] );

		/**
		 *  Parse {security}
		 */
		if ( ! isset( $_GET['security'] ) || ! wp_verify_nonce( $_GET['security'], $this->ajax_action_ref ) ) {
			/**
			 *  Construct SSE message and echo it to the client
			 */
			Filetrip_Channel_Utility::sse_send_message( "", "Invalid security check", "error" );
			die();
		}

		/**
		 *  Parse {subfolder}
		 */
		$username          = 'Guest';
		$subfolder_enabled = false;
		$post              = get_post( $att_id );

		if ( isset( $_GET['subfolder'] ) && $_GET['subfolder'] != 'false' ) {
			$subfolder_enabled = true;
			$userid            = $post->post_author;

			if ( $userid == 0 ) {
				$username = 'Guest';
			} else {
				$user_info = get_userdata( $userid );
				$username  = $user_info->user_login;
			}
		}

		/**
		 *  Parse {subfolder}
		 */
		$file_path   = get_attached_file( $att_id );
		$destination = ( isset( $_GET['target_folder'] ) && $_GET['target_folder'] != "false" ) ? base64_decode( $_GET['target_folder'] ) : '';

		\Filetrip_Dropbox::resumable_file_upload( $destination, $file_path, filesize( $file_path ), $subfolder_enabled, $username, true, $att_id,$att_id );

	}

	/**
	 * @param (array) ($channels)
	 *      This function responsible of registering this channel to the core
	 *        Filetrip channel distributor [Filetrip_Distributor]. Also it should pass
	 *        all the necessary information to the client js.
	 */
	function register_me_to_channels( $channels ) {
		/**
		 ** Channel array structure:
		 * array(
		 * 'channel_key' => 'key',
		 * 'channel_name' => 'name',
		 * 'channel_icon' => 'path/img',
		 * 'channel_action_url' => 'action_url',
		 * 'active' => \Filetrip_Dropbox::is_dropbox_active(),
		 * 'security' => wp_create_nonce( $this->ajax_action_ref )
		 * )
		 **/
		$filetrip_settings = \Filetrip_Uploader::get_filetrip_main_settings();

		$destination = isset( $filetrip_settings[ $this->select_folder_id ] ) ? $filetrip_settings[ $this->select_folder_id ] : '';

		$channels[ $this->field_slug ] = array(
			'destination'        => $destination,
			'channel_key'        => $this->field_slug,
			'channel_name'       => $this->field_name,
			'channel_icon'       => $this->get_icon( '', '', '20', '' ),
			'channel_action_url' => $this->ajax_action_ref,
			'active'             => \Filetrip_Dropbox::is_dropbox_active(),
			'security'           => wp_create_nonce( $this->ajax_action_ref ),
			'is_subfolder'       => false
		);

		return $channels;
	}

	function get_icon( $link = '', $title = '', $size = '30', $label = 'Dropbox' ) {
		// If there is no link? Don't add href
		$hrefStart = '<a target="_blank" href="' . $link . '">';
		$hrefEnd   = '</a> ';

		if ( $link == '' ) {
			$hrefStart = '';
			$hrefEnd   = '';
		}

		return $hrefStart . '<img title="Dropbox" src="' . $this->extensionGetDirectoryUrl() . '/assets/img/dropbox.png" width="' . $size . '" height="' . $size . '">' . $hrefEnd . " " . $label;
	}

	/**
	 * @param (int) ($uploader_id)
	 *      Gets attachment ID and find if this channel is selected as forwarding channel, and retrive destination.
	 */
	function register_me_as_forwarder( $forwardingChannels, $uploader_id ) {
		$post             = get_post( $uploader_id );
		$selectedChannels = \Filetrip_Channel_Utility::get_channel_selected( $post->post_parent );

		if(empty($selectedChannels)) return $forwardingChannels;

		/**
		 * Check if this channel is been selected for the designated uploader.
		 */
		foreach ( (array) $selectedChannels as $select ) {
			if ( $select == $this->field_slug ) {
				$forwardingChannels[] = array(
					'destination'        => $this->get_meta_destination_folder( $post->post_parent ),
					'channel_key'        => $this->field_slug,
					'channel_name'       => $this->field_name,
					'channel_icon'       => $this->get_icon( '', '', '20', '' ),
					'channel_action_url' => $this->ajax_action_ref,
					'active'             => \Filetrip_Dropbox::is_dropbox_active(),
					'security'           => wp_create_nonce( $this->ajax_action_ref ),
					'is_subfolder'       => $this->get_subfolder_option_value( $post->post_parent )
				);
			}
		}

		return $forwardingChannels;
	}

	/**
	 * Add a trigger to inject Channel icon in Media Column
	 *
	 * @return null
	 */
	function init_admin_hooks() {
		// All hooks that need to be called at admin_init trigger should be placed hear
		add_action( 'manage_media_custom_column', array( $this, 'insert_channel_hyperlinked_icon' ), 10, 2 );
	}

	/**
	 * This function will render the channel folder listing widget.
	 *
	 * @return html
	 */
	function folder_select_widget( $field_args, $escaped_value, $object_id, $object_type, $field_type_object ) {
		\Filetrip_Dropbox_Utility::build_select_folder_widget( $field_args, $field_type_object, true );
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
                $link_upload_cdn_options[$this->filetrip_dropbox_link_met] = arfaly_get_icon('dropbox', ' Dropbox Link');
			}
		}
		return $link_upload_cdn_options;
	}

	function add_meta_channel_configuration( $meta_config_array ) {
		// Add checkbox field
		$meta_config_array['fields']['channels']['options'][ $this->field_slug ] = $this->get_icon();

		// Add folder selection field
		$meta_config_array['fields'][] = array(
			'name'       => 'Dropbox folder',
			'desc'       => 'Select the destination folder for your Dropbox folder channel. <span style="color:red">NO SPACES ALLOWED For FOLDER NAMES</span>.',
			'id'         => \Filetrip_Constants::METABOX_PREFIX . $this->select_folder_id,
			'type'       => $this->meta_field_type,
			'attributes' => array( 'readonly' => '' )
		);

		$meta_config_array['fields'][] = array(
			'name' => 'Automatic sub-folder creation',
			'desc' => '(Dropbox Channel Only) Automatically create sub-folders with the uploader name under the main destination folder as a new partitioning mechanism',
			'id'   => \Filetrip_Constants::METABOX_PREFIX . $this->meta_subfolder_id,
			'type' => 'checkbox'
		);


		return $meta_config_array;
	}

	function add_setting_field( $setting_array ) {
		$setting_array[ \Filetrip_Constants::POST_TYPE . '_settings' ][] = array(
			'name'    => $this->select_folder_id,
			'label'   => __( 'Select Dropbox Folder', 'filetrip-plugin' ),
			'desc'    => __( 'Select default Dropbox folder as Media destination', 'filetrip-plugin' ),
			'type'    => $this->setting_field_type,
			'default' => '',
		);

		return $setting_array;
	}

	function add_settings_field_callback( $args, $section, $option, $obj ) {
		// Render my setting
		add_settings_field( $section . '[' . $option['name'] . ']', $option['label'],
			function () use ( $args, $obj ) {
				\Filetrip_Dropbox_Utility::build_select_folder_widget( $args, $obj );
			}
			, $section, $section, $args );
	}

	function insert_channel_hyperlinked_icon( $column_name, $id ) {
		switch ( $column_name ) {
			case \Filetrip_Constants::MEDIA_COLUMN_SLUG;
				$query_s = admin_url( \Filetrip_Constants::FILETRIP_DISTRIBUTOR_PAGE );
				$query_s = $query_s . '&media=' . $id . '&source=' . \Filetrip_Constants::TransferType( 'media' ) . '&';

				echo $this->get_icon( $query_s . 'channel=' . $this->field_slug, 'Send file to Dropbox', '20', '' );

				break;
			default:
				break;
		}
	}

	/* Get shared link for an attachment if avaliable */
	function get_shared_link_if_avaliable($shared_url_links, $att_id)
	{
		$parent_id = \Filetrip_Channel_Utility::get_uploader_id_by_att_id( $att_id );
		if(!$parent_id) return $shared_url_links;

		$selectedCdnChannel = \Filetrip_Channel_Utility::get_generate_cdn_selected( $parent_id );

		if ( $selectedCdnChannel == $this->filetrip_dropbox_link_met ) {
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
