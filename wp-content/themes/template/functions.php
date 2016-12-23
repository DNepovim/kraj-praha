<?php

// Include Custom Post Types definition
include_once 'src/cpt/define.php';
include_once 'src/cpt/meta.php';

// Register menu
register_nav_menu('main_menu', 'Hlavní menu');

// Register thumbnail
add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 1200, 9999 );
add_image_size('post_thumb', 150, 150);

// Disable html editor
function editor_settings($settings) {
	$settings['quicktags'] = false;
	return $settings;
}
add_filter('wp_editor_settings', 'editor_settings');

// Redirect blank search to frontpage
function change_blank_search( $query_variables ) {
	if(isset( $_GET['s'] ) && (strlen(preg_replace('/\s+/u','', $_GET['s'])) == 0)) {
		bdump('serach');
		wp_redirect(home_url());
	} else {
		return $query_variables;
	}
}
add_filter( 'request', 'change_blank_search' );