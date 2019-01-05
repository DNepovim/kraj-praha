<?php

$args = [];
$postID = get_the_ID();
$taxonomies = wp_get_post_categories($postID);

if ($taxonomies) {
	$args['relatedTitle'] = 'Podobné články:';

	$args['relatedQuery'] = new WP_Query([
		'category__in' => $taxonomies,
		'post__not_in' => [$postID],
		'posts_per_page' => 3,
	]);

	$args['relatedButton'] = [
		'label' => 'Všechny články',
		'link' => '/',
	];
}

view('singular', $args);
