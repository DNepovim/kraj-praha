<?php
/**
 * Plugin Name: FB posts to CPT
 * Plugin URI:
 * Version: 0.1
 * Author: Dominik Blaha
 * Author URI: http://www.dombl.cz
 * License: MIT
 */

require_once ABSPATH . 'vendor/autoload.php';

require_once 'cpt.php';
require_once 'settings-page.php';
require_once 'load-fb-posts.php';

if ( is_admin() ) {
	$settings_page = new FPTCSettingsPage();
};

//function fptc_show_update_button( $views )
//{
//	$update_url = add_query_arg('refresh-stream', 'fb', get_permalink());
//	$views['my-button'] = '<a id='update-from-provider' href='' . $update_url . ''>Update stream</a>';
//	return $views;
//}
//add_filter( 'views_edit-facebook', 'fptc_show_update_button' );

function fptc_refresh_stream() {
	if ( isset( $_GET['refresh-stream'] ) ) {
		if ( $_GET['refresh-stream'] == 'fb' ) {
			fptc_load();
		}
	}
}

add_action( 'init', 'fptc_refresh_stream' );
