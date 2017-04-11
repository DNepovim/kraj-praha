<?php
/**
 * Plugin Name: RSS to CPT
 * Plugin URI:
 * Version: 0.1
 * Author: Dominik Blaha
 * Author URI: http://www.dombl.cz
 * License: MIT
 */

require_once 'cpt.php';
require_once 'settings-page.php';
require_once 'rss-load.php';


if ( is_admin() ) {
	$settings_page = new RTCSettingsPage();
};

function rtc_show_update_button( $views )
{
	$update_url = add_query_arg('refresh-stream', 'rss', get_permalink());
	$views['my-button'] = '<a id="update-from-provider" href="' . $update_url . '">Update stream</a>';
	return $views;
}
add_filter( 'views_edit-facebook', 'rtc_show_update_button' );

function rtc_refresh_stream() {
	if ( isset( $_GET['refresh-stream'] ) ) {
		if ( $_GET['refresh-stream'] == "rss" ) {
			rtc_load();
		}
	}
}

add_action( 'init', 'rtc_refresh_stream' );
