<?php

use App\Typopo;

// required explicitly, the class index of the autoloader may not know the class yet
require_once __DIR__ . '/../../app/Typopo.php';

// Fix typography (quotes, dashes, ellipses, spaces, non-breaking spaces) before a post is saved
add_filter('wp_insert_post_data', function ($data) {
	if (in_array($data['post_type'], ['revision', 'attachment', 'nav_menu_item', 'customize_changeset'], true)
		|| $data['post_status'] === 'auto-draft'
	) {
		return $data;
	}

	// the data are slashed at this point
	$fixed = [
		'post_title' => Typopo::fixText(wp_unslash($data['post_title'])),
		'post_excerpt' => Typopo::fixText(wp_unslash($data['post_excerpt'])),
		'post_content' => Typopo::fixHtml(wp_unslash($data['post_content'])),
	];

	return array_merge($data, wp_slash($fixed));
}, 20);

// Fix typography of posts and pages when they are rendered too, so the content saved before is covered as well.
// It runs after wpautop and the shortcodes, the fixing is idempotent, so already fixed content is left as it is.
add_filter('the_content', function ($content) {
	return in_array(get_post_type(), ['post', 'page'], true) ? Typopo::fixHtml($content) : $content;
}, 20);
