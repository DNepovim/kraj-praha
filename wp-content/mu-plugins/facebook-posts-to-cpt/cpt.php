<?php

// Create and configure custom post type

function fptc_create_post_type() {
	register_post_type( 'fb',
		array(
			'labels'      => array(
				'name' => 'Facebook',
			),
			'public'      => true,
			'has_archive' => true,
			'menu_icon'   => 'dashicons-facebook',
			'supports'    => array( 'title' )
		)
	);
}

add_action( 'init', 'fptc_create_post_type' );

function fptc_add_meta( $meta_boxes ) {
	$prefix       = 'fptc_';
	$meta_boxes[] = array(
		'title'      => __( 'Data', 'textdomain' ),
		'post_types' => 'fb',
		'fields'     => array(
			array(
				'id'   => $prefix . 'fb_id',
				'name' => __( 'ID' ),
				'type' => 'text'
			),
			array(
				'id'   => $prefix . 'created_time',
				'name' => __( 'Vytvořeno' ),
				'type' => 'datetime'
			),
			array(
				'id'   => $prefix . 'story',
				'name' => __( 'Story' ),
				'type' => 'text',
			),
			array(
				'id'   => $prefix . 'message',
				'name' => __( 'Zpráva' ),
				'type' => 'textarea',
			)
		),
	);

	return $meta_boxes;
}

add_filter( 'rwmb_meta_boxes', 'fptc_add_meta' );
