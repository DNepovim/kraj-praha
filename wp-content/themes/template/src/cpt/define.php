<?php

add_action('init', 'create_post_type');

function create_post_type()
{
	add_post_type_support('page', 'excerpt');
	register_post_type('udalosti',
		array(
			'labels' => array(
				'name'               => __('Události'),
				'name_admin_bar'     => __('Přidat událost'),
				'add_new'            => __('Přidat událost'),
				'add_new_item'       => __('Přidat událost' ),
				'new_item'           => __('Nová událost'),
				'edit_item'          => __('Upravit událost'),
				'view_item'          => __('Zobrazit událost'),
				'all_items'          => __('Všechny události'),
				'search_items'       => __('Hledat událost'),
				'not_found'          => __('Žádné události nebyly nalezeny'),
				'not_found_in_trash' => __('Žádné události nebyly nalezeny')
			),
			'public' => true,
			'menu_icon' => 'dashicons-calendar-alt',
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

