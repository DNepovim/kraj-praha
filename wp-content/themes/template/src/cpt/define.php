<?php

add_action('init', 'create_post_type');

function create_post_type()
{
	add_post_type_support('page', 'excerpt');
	register_post_type('udalosti',
		array(
			'labels' => array(
				'name' => __('Událost'),
			),
			'public' => true,
			'has_archive' => true,
			'supports' => array(
				'title',
				'author',
				'editor',
				'thumbnail',
			)
		)
	);
}

