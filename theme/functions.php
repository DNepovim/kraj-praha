<?php

global $App;
if ($App->parameters['wpCron']) {
	if ( ! wp_next_scheduled( 'refres_streams_hook' ) ) {
		wp_schedule_event( time(), 'hourly', 'refres_streams_hook' );
	}

	add_action( 'refres_streams_hook', 'refres_streams' );

	function refres_streams() {
			rtc_load();
			fptc_load();
	}
}

// Register menu
register_nav_menu('main_menu', 'Hlavní menu');

// Disable html editor
function editor_settings($settings) {
	if ( !is_user_logged_in() ) {
		$settings['quicktags'] = false;
		return $settings;
	}
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
	if(!is_super_admin()) {
		remove_menu_page( 'tools.php' );
	}
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

// Remove emoji
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

require __DIR__ . '/theme-init.php';

// Exclude rss and fb post types from wp search
add_action( 'init', 'exclude_cpt_from_search', 99 );

function exclude_cpt_from_search() {
	global $wp_post_types;

	if (post_type_exists('rss')) {
		$wp_post_types['rss']->exclude_from_search = true;
	}

	if (post_type_exists('fb')) {
		$wp_post_types['fb']->exclude_from_search = true;
	}
}
