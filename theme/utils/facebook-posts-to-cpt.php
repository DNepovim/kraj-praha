<?php
/**
 * Plugin Name: FB posts to CPT
 * Plugin URI:
 * Version: 0.1
 * Author: Dominik Blaha
 * Author URI: http://www.dombl.cz
 * License: MIT
 */

define('FPTC_PLUGIN_DIRECTORY', 'facebook-posts-to-cpt/');

require_once FPTC_PLUGIN_DIRECTORY . 'cpt.php';
require_once FPTC_PLUGIN_DIRECTORY . 'settings-page.php';
require_once FPTC_PLUGIN_DIRECTORY . 'load-fb-posts.php';

if ( is_admin() ) {
	$settings_page = new FPTCSettingsPage();
};

function fptc_show_update_button() {
	global $pagenow;
	if ( !empty($_GET['post_type']) && $pagenow == 'edit.php' && $_GET['post_type']=='fb' ) {
		echo '<a id="update-from-provider" href="' . get_admin_url() . 'edit.php?post-type=fb&refresh-stream=fb">Update stream</a>';
	}
}
add_action( 'views_edit-fb', 'fptc_show_update_button' );

function fptc_refresh_stream() {
	if ( isset( $_GET['refresh-stream'] ) ) {
		if ( $_GET['refresh-stream'] == 'fb' ) {
			fptc_load();
			wp_redirect(wp_get_referer());
			exit;
		}
	}
}

add_action( 'init', 'fptc_refresh_stream' );
