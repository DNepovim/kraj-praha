<?php
function rtc_load() {
	$options = get_option( 'rtc_option' );

	$url = 'http://krizovatka.skaut.cz/index.php?option=com_ninjarsssyndicator&feed_id=1&format=raw&autologin=1';
	$url = preg_replace( "/ /", "%20", $url );

	$xml = simplexml_load_file( $url );


	foreach ( $xml->channel->item as $item ) {
		$args = rtc_colect_data( $item );

		if ( $exist_post = rtc_exist_post( $args['meta_input']['rtc_pub_date'] ) ) {
			$args['ID'] = $exist_post;
			wp_update_post( $args );
		} else {
			wp_insert_post( $args );
		}
	}
}

function rtc_exist_post( $pubDate ) {
	$args  = array(
		'post_type'  => 'rss',
		'meta_key'   => 'rtc_pub_date',
		'meta_value' => $pubDate
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

function rtc_colect_data( $data ) {
	$args = array(
		'post_title'  => (string)$data->title,
		'post_status' => 'publish',
		'post_type'   => 'rss',
		'meta_input'  => array(
			'rtc_pub_date'    => (string)$data->pubDate,
			'rtc_description' => (string)$data->description,
			'rtc_author'      => (string)$data->author,
			'rtc_guid'        => (string)$data->guid
		)
	);

	return $args;
}

