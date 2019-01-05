<?php

$args = [];
$taxonomy = get_post_meta(get_the_ID(), 'praha_taxonomy', true);

if ($taxonomy) {
	$catName = strtolower(get_cat_name($taxonomy));
	$args['relatedTitle'] = 'Články k tématu ' . $catName . ':';

	$args['relatedQuery'] = new WP_Query([
		'cat' => $taxonomy,
		'posts_per_page' => 3,
	]);

	if ($args['relatedQuery']->max_num_pages > 1) {
		$args['relatedButton'] = [
			'label' => 'Všechny články k&nbsp;tématu ' . $catName,
			'link' => get_category_link($taxonomy),
		];
	}
}

view('singular', $args);
