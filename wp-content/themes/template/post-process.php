<?php
include '../../../wp-load.php';

if ( isset( $_POST['submitted'] )
     && isset( $_POST['post_nonce_field'] )
     && wp_verify_nonce( $_POST['post_nonce_field'], 'post_nonce' )
) {

	if ( trim( $_POST['postTitle'] ) === '' ) {
		$err['postTitle'] = true;
		$hasError         = true;
	}

	if ( trim( $_POST['postContent'] ) === '' ) {
		$err['postContent'] = true;
		$hasError           = true;
	}

	if ( $hasError ) {
		$query = '?';
		foreach ( $err as $key => $value ) {
			$query .= $key . '=' . $value . '&';
		}
		wp_redirect( home_url( $query ) );
		exit;
	}

	if ( is_user_logged_in() ) {
		$post_status = 'publish';
	} else {
		$post_status = 'pending';
	}

	$post_information = array(
		'post_title'    => wp_strip_all_tags( $_POST['postTitle'] ),
		'post_content'  => $_POST['postContent'],
		'post_type'     => 'post',
		'post_status'   => $post_status,
		'post_category' => $_POST['postCategory']
	);

	$post_id = wp_insert_post( $post_information );

	$uploaddir = wp_upload_dir();
	$filename = basename($_FILES['postThumbnail']['name']);

	if ( wp_mkdir_p( $upload_dir['path'] ) ) {
		$file = $uploaddir['path'] . '/' . $filename;
	} else {
		$file = $uploaddir['basedir'] . '/' . $filename;
	}

	move_uploaded_file($_FILES['postThumbnail']['tmp_name'], $file);

	$wp_filetype = wp_check_filetype( $filename, null );

	$attachment  = array(
		'post_mime_type' => $wp_filetype['type'],
		'post_title'     => sanitize_file_name( $filename ),
		'post_content'   => '',
		'post_status'    => 'inherit'
	);

	$attach_id   = wp_insert_attachment( $attachment, $file, $post_id );

	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
	$res1        = wp_update_attachment_metadata( $attach_id, $attach_data );
	$res2        = set_post_thumbnail( $post_id, $attach_id );

	if ( $post_id ) {
		wp_redirect( home_url() );
	}
}
