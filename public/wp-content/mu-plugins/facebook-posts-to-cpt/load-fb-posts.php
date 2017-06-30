<?php
function fptc_load() {

	$options = get_option( 'fptc_option' );

	$fb = new Facebook\Facebook( [
		'app_id'                => $options['app_id'],
		'app_secret'            => $options['app_secret'],
		'default_graph_version' => 'v2.8',
	] );

	try {
		$response = $fb->get( '/' . $options['page_id'] . '/posts?locale=cs_CZ&fields=message,link,created_time,story', $options['access_token'] );
	} catch ( Facebook\Exceptions\FacebookResponseException $e ) {
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch ( Facebook\Exceptions\FacebookSDKException $e ) {
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}

	fptc_save( $response->getDecodedBody()['data'] );
}


function fptc_save( $obj ) {

	foreach ( $obj as $item ) {
		$args = fptc_colect_data( $item );

		if ( $exist_post = fptc_exist_post( $args['meta_input']['fptc_fb_id'] ) ) {
			$args['ID'] = $exist_post;
			wp_update_post( $args );
		} else {
			wp_insert_post( $args );
		}
	}
}

function fptc_exist_post( $fb_id ) {

	$args = array(
		'post_type'  => 'fb',
		'meta_key'   => 'fptc_fb_id',
		'meta_value' => $fb_id
	);

	$posts = get_posts( $args );

	try {
		if ( $posts ) {
			if ( count( $posts ) > 1 ) {
				throw new Exception( 'There are more then one results' );
			} else {
				return $posts[0]->ID;
			}
		} else {
			return false;
		};
	} catch ( Exception $e ) {
		echo 'Caught exception: ', $e->getMessage(), "\n";
	}
}

function fptc_colect_data( $data ) {
	if ( empty( $data['message'] ) ) {
		$title = $data['story'];
	} else {
		$title = $data['message'];
	}
	$title = wp_trim_words( $title, 7 );

	$args = array(
		'post_title'  => $title,
		'post_status' => 'publish',
		'post_type'   => 'fb',
		'post_date'   => $data['created_time'],
		'meta_input'  => array(
			'fptc_fb_id'   => (string) $data['id'],
			'fptc_story'   => (string) $data['story'],
			'fptc_message' => (string) $data['message']
		)
	);

	return $args;
}
