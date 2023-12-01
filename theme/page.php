<?php

$args = [];
$taxonomy = get_post_meta(get_the_ID(), 'praha_taxonomy', true);
$hideTitle = get_post_meta(get_the_ID(), 'praha_hide_title', true);
$args['hideTitle'] = $hideTitle;

if ($taxonomy) {

	$relatedQuery = new WP_Query([
		'cat' => $taxonomy,
		'posts_per_page' => 3,
	]);

	if ($relatedQuery->post_count) {
		$args['relatedQuery'] = $relatedQuery;

		$catName = strtolower(get_cat_name($taxonomy));

		$args['relatedTitle'] = 'Články k tématu ' . $catName . ':';

		if ($args['relatedQuery']->max_num_pages > 1) {
			$args['relatedButton'] = [
				'label' => 'Všechny články k&nbsp;tématu ' . $catName,
				'link' => get_category_link($taxonomy),
			];
		}
	}
}

view('singular', $args);
