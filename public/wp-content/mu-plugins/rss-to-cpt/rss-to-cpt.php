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

function rss_show_update_button() {
	global $pagenow;
	if ( !empty($_GET['post_type']) && $pagenow == 'edit.php' && $_GET['post_type']=='rss' ) {
		echo '<a id="update-from-provider" href="' . get_admin_url() . 'edit.php?post-type=rss&refresh-stream=rss">Update stream</a>';
	}
}
add_action( 'views_edit-rss', 'rss_show_update_button' );

function rtc_refresh_stream() {
	if ( isset( $_GET['refresh-stream'] ) ) {
		if ( $_GET['refresh-stream'] == "rss" ) {
			rtc_load();
			wp_redirect(wp_get_referer());
			exit;
		}
	}
}

add_action( 'init', 'rtc_refresh_stream' );
