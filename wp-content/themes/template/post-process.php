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
		foreach ($err as $key => $value) {
			$query .= $key . '=' . $value . '&';
		}
		wp_redirect( home_url($query));
		exit;
	}

	$post_information = array(
		'post_title'   => wp_strip_all_tags( $_POST['postTitle'] ),
		'post_excerpt' => $_POST['postExcerpt'],
		'post_content' => $_POST['postContent'],
		'post_type'    => 'post',
		'post_status'  => 'publish'
	);

	$post_id = wp_insert_post( $post_information );

	if ( $post_id ){
		wp_redirect( home_url() );
	}
}
