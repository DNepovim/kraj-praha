<?php

if (ob_get_contents()) {
	ob_clean(); ob_start();
}

global $App;
if ($App->parameters['wpCron']) {
	if (!wp_next_scheduled('refres_streams_hook')) {
		wp_schedule_event(time(), 'hourly', 'refres_streams_hook');
	}

	add_action('refres_streams_hook', function() {
		rtc_load();
		fptc_load();
	});
}

// Register menu
register_nav_menu('main_menu', 'Hlavní menu');
register_nav_menu('footer_menu', 'Menu v patičce');

// Disable html editor
add_filter('wp_editor_settings', function($settings) {
	if (!is_user_logged_in()) {
		$settings['quicktags'] = false;
		return $settings;
	}
});


// Redirect blank search to frontpage
add_filter('request', function($query_variables) {
	if(isset( $_GET['s'] ) && (strlen(preg_replace('/\s+/u','', $_GET['s'])) == 0)) {
		wp_redirect(home_url());
	} else {
		return $query_variables;
	}
});

// Remove tools item form admin menu
add_action('admin_menu', function() {
	if(!is_super_admin()) {
		remove_menu_page('tools.php');
	}
});

// Remove custom fields from all posts
add_action('do_meta_boxes', function($post_type, $context, $post) {
	remove_meta_box('postcustom', $post_type, $context);
}, 1, 3);

// Remove tags
add_action('init', function() {
	unregister_taxonomy_for_object_type('post_tag', 'post');
});

add_filter('tiny_mce_before_init', function($in) {
	$in['block_formats'] = "Odstavec=p; Nadpis=h2; Podnadpis=h3";
	$in['toolbar1'] = 'formatselect,bold,underline,italic,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,spellchecker,wp_fullscreen,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help  ';
	$in['toolbar2'] = '';
	return $in;
});

// Remove emoji
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');

require __DIR__ . '/theme-init.php';

global $wp;
$View->wp = $wp;

// Exclude rss and fb post types from wp search
add_action('init', function() {
	global $wp_post_types;

	if (post_type_exists('rss')) {
		$wp_post_types['rss']->exclude_from_search = true;
	}

	if (post_type_exists('fb')) {
		$wp_post_types['fb']->exclude_from_search = true;
	}
}, 99);

add_filter('the_content', function($content) {
	$content = str_replace('<table', '</div></div><div class="table-container wysiwyg"><table', $content);
	$content = str_replace('</table>', '</table></div><div class="content__content"><div class="text">', $content);
	return $content;
});

// disable Gutenberg
add_filter('use_block_editor_for_post_type', '__return_false', 10);

// custom pagination
add_action('pre_get_posts', function($query) {
	if($query->is_main_query() && $query->post_count > 0) {
		global $wp;
		$current_url = home_url($wp->request);
		$current_page = !empty($_GET['strana']) ? $_GET['strana'] : null;
		$pages_count = intval($query->max_num_pages);

		if (!isset($current_page)) {
			$current_page = 1;
		} else if ($current_page <= 1) {
			wp_redirect(remove_query_arg('strana'));
			exit();
		} else if ($current_page > $pages_count) {
			wp_redirect(add_query_arg('strana', $pages_count));
			exit();
		}

		$query->set('paged', $current_page);
		remove_all_actions ( '__after_loop');
	}
});

function get_next_page_url($query) {
	$pages_count = intval($query->max_num_pages);
	$next_page = !empty($_GET['strana']) ? $_GET['strana'] + 1 : 2;
	return $next_page <= $pages_count ? add_query_arg( 'strana', $next_page) : false;
}

function get_prev_page_url($query) {
	$pages_count = intval($query->max_num_pages);
	$prev_page = !empty($_GET['strana']) ? $_GET['strana'] - 1 : 0;
	return $prev_page == 1 ? remove_query_arg('strana') : ($prev_page < 1 ? false : add_query_arg( 'strana', $prev_page));
}

// Dont load Gutenberg styles
add_action('wp_print_styles', function() {
	wp_dequeue_style( 'wp-block-library' );
	wp_deregister_style( 'wp-block-library' );
}, 100);


function get_rss_posts($count = 5) {
	$rss = simplexml_load_file('https://zpravodajstvi.skaut.cz/feed');

	if (empty($rss)) {
		return [];
	}

	$resutl = [];
	$index = 0;
	foreach ($rss->channel->item as $item) {
		$result[$index] = [
			"title" => (string) $item->title,
			"link" => (string) $item->link
		];
		$index++;
		if ($index == $count) {
			break;
		}
	}

	return $result;
}

function get_facebook_posts($limit = 5) {
	$options = get_option( 'fptc_option' );

	if (!($options['app_id'] && $options['app_secret'] && $options['page_id'] && $options['access_token'])) {
		return false;
	}

	$fb = new Facebook\Facebook( [
		'app_id'                => $options['app_id'],
		'app_secret'            => $options['app_secret'],
		'default_graph_version' => 'v2.8',
	] );

	try {
		$response = $fb->get( '/' . $options['page_id'] . '/posts?locale=cs_CZ&fields=id,message,story&limit=' . $limit, $options['access_token'] );
		return $response->getDecodedBody()['data'];
	} catch ( Facebook\Exceptions\FacebookResponseException $e ) {
		// TODO: Error handling
		return false;
	} catch ( Facebook\Exceptions\FacebookSDKException $e ) {
		// TODO: Error handling
		return false;
	}
}

if ( is_admin() ) {
	$settings_page = new FPTCSettingsPage();
};
