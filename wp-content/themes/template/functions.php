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
add_image_size('og', 1200, 630);

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

// Remove tools item form admin menu
function custom_menu_page_removing() {
    remove_menu_page( 'tools.php' );
}
add_action( 'admin_menu', 'custom_menu_page_removing' );

// Remove custom fields from all posts
add_action( 'do_meta_boxes', 'remove_default_custom_fields_meta_box', 1, 3 );
function remove_default_custom_fields_meta_box( $post_type, $context, $post ) {
    remove_meta_box( 'postcustom', $post_type, $context );
}

// Remove tags
function unregister_tags() {
    unregister_taxonomy_for_object_type('post_tag', 'post');
}
add_action('init', 'unregister_tags');

function format_TinyMCE( $in ) {
    $in['block_formats'] = "Odstavec=p; Nadpis=h2; Podnadpis=h3";
    $in['toolbar1'] = 'formatselect,bold,underline,italic,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,spellchecker,wp_fullscreen,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help  ';
	$in['toolbar2'] = '';
	return $in;
}
add_filter( 'tiny_mce_before_init', 'format_TinyMCE' );