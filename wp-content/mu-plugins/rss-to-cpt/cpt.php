<?php

// Create and configure custom post type

function rtc_create_post_type() {
	register_post_type( 'rss',
		array(
			'labels'      => array(
				'name' => 'Křižovatka',
			),
			'public'      => true,
			'has_archive' => true,
			'menu_icon'   => 'dashicons-book-alt',
			'capabilities' => 'administrator',
			'supports'    => array( 'title' )
		)
	);
}

add_action( 'init', 'rtc_create_post_type' );

function rtc_add_meta( $meta_boxes ) {
	$prefix       = 'rtc_';
	$meta_boxes[] = array(
		'title'      => __( 'Data', 'textdomain' ),
		'post_types' => 'rss',
		'fields'     => array(
			array(
				'id'   => $prefix . 'pub_date',
				'name' => __( 'Publikováno' ),
				'type' => 'datetime'
			),
			array(
				'id'   => $prefix . 'description',
				'name' => __( 'Popis' ),
				'type' => 'textarea'
			),
			array(
				'id'   => $prefix . 'author',
				'name' => __( 'Autor', 'textdomain' ),
				'type' => 'text',
			),
			array(
				'id'   => $prefix . 'guid',
				'name' => __( 'Odkaz', 'textdomain' ),
				'type' => 'url',
			)
		),
	);

	return $meta_boxes;
}

add_filter( 'rwmb_meta_boxes', 'rtc_add_meta' );
