<?php

$args = [];
$postID = get_the_ID();
$categories = wp_get_post_categories($postID);

$relatedTitle = 'Podobné články:';
$relatedQuery = null;

if ($categories) {
	$relatedQuery = new WP_Query([
		'category__in' => $categories,
		'post__not_in' => [$postID],
		'posts_per_page' => 3,
	]);
}

if (!$relatedQuery || !$relatedQuery->have_posts()) {
	$relatedTitle = 'Další články:';
	$relatedQuery = new WP_Query([
		'post__not_in' => [$postID],
		'posts_per_page' => 3,
	]);
}

$args['relatedTitle'] = $relatedTitle;
$args['relatedQuery'] = $relatedQuery;
$args['relatedButton'] = [
	'label' => 'Všechny články',
	'link' => '/',
];

view('singular', $args);
