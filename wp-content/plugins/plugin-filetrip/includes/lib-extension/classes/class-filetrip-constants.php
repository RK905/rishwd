<?php

class Filetrip_Constants 
{
	
	// General
	const PLUGIN_NAME = 'Filetrip';
	const NONCE =  'itech_filetrip_plugin'; 
	const FIX_CONTEXT = "itf_filetrip";
	const ERROR_TRANSIENT = 'filetrip_error_transient'; 
	const VERSION = '2.8.2.7'; 
	const TEXT_DOMAIN = 'filetrip-plugin';

	// Channel types
	private static $channel_types = array(
		'dropbox' => 'filetrip_dropbox_link',
		'gdrive' => 'filetrip_gdrive_link',
		's3' => 'filetrip_s3_link'
    );

	const POST_STATUS = 'filetrip'; 
	const POST_TYPE = 'filetrip'; 
    const METABOX_PREFIX = '_filetrip_'; 
	const MEDIA_COLUMN_SLUG = 'filetrip';

	const ITF_CORE_EXTENSION_VER = '1.0.3';
	
	const ITF_WEBSITE_LINK = 'https://www.itechflare.com';

	/*
	* Filetrip Menu slugs
	*/
	const FILETRIP_MAIN_MENU = 'edit.php?post_type=filetrip';
	const MAIN_MENU_PARENT_SLUG = 'edit.php?post_type=filetrip';
	const FILETRIP_DISTRIBUTOR_PAGE = 'edit.php?page=filetrip_files_distributor';
	const MEDIA_LIBRARY_PAGE = 'upload.php';

	const MAIN_MENU_SLUG = 'filetrip';
	const REVIEW_APPROVE_MENU_PAGE = 'filetrip_manage_list';

	const UPLOAD_PAGE_MENU = 'edit.php?post_type=filetrip&page=filetrip_manage_list';
	const OPTION_PAGE = 'edit.php?post_type=filetrip&page=filetrip_settings';

	const RECORD_TABLE_NAME = 'itf_filetrip_record_tbl';
	const METADATA_TABLE_NAME = 'itf_filetrip_metadata_tbl';

	const ERROR_MESSAGE_MAX_LENGHT = 155;

	/*
		Cloud upload metadata
	*/
	const CDN_URL = 'cdn_url_meta_key';
	const SHARED_URL = 'shared_url_meta_key';


	private static $transfer_type = array(
		'media' => 'media-library',
		'backup' => 'filetrip-backup',
		'forward' => 'upload-forwarder'
	);

	/*
	 * Static Public Functions
	 *
	 */
    public static function TransferType($key){
        return self::$transfer_type[$key];
	}
	
	// To enable demo
	const DEMO_MODE = false;

} // end class.