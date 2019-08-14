<?php


add_filter( 'caldera_forms_process_field_filetrip_uploader', 'filetrip_handle_file_upload', 10, 3 );

/* IMPORTANT:: This function process form information and return list of URL's to Caldera according to its standard */

function filetrip_handle_file_upload( $entry, $field, $form ) {

	$uploads = array();
	$required = false;
	if ( isset( $field['required'] ) && $field['required'] ) {
		$required = true;
	}

	if ( isset( $_POST["image-id"] ) ) {
		$att_ids = array_filter($_POST["image-id"], 'intval');

		$filetrip_id        = $field['config']['shortcode'];
		$selected_uploaders = get_post_meta( $filetrip_id, '_filetrip_cdn_select', true );
		foreach ( $att_ids as $att ) {
			// Notify channels to get the shared link URL for 
			$shared_url_links = [];
			$shared_url_links = apply_filters('itf/filetrip/filter/channels/get/shared/link', $shared_url_links, $att);

			if(!empty($shared_url_links)){
			  $uploads[] = $shared_url_links[0] . '<br>';
			}
		}
	} else if ( ! ! isset( $_POST["image-id"] ) && $required ) {
		return new WP_Error( 'fail', __( 'No file has been uploaded', 'filetrip-plugin' ) );
	}

	if ( count( $uploads ) > 1 ) {
		return $uploads;
	}

	if ( ! empty( $uploads ) ) {
		return $uploads[0];
	} else {
		return '';
	}
}