<?php
add_filter( 'rwmb_meta_boxes', function ( $meta_boxes )
{
	$meta_boxes[] = [
		'title'  => 'Test',
		'fields' => [
			array(
				'id'         => 'numbered_items',
				'type'       => 'group',
				'columns'    => '12',
				'clone'      => true,
				'sort_clone' => true,
				// List of sub-fields
				'fields'     => array(

					array(
						'name'    => __( 'Title', 'indigo-metaboxes' ),
						'id'      => 'title',
						'type'    => 'text',
						'class'   => 'big-text',
						'columns' => 3,
					),
					array(
						'name'    => __( 'Description', 'indigo-metaboxes' ),
						'id'      => 'desc',
						'type'    => 'text',
						'class'   => 'big-text',
						'columns' => 9,
					),
				),
			),
		],
	];
	return $meta_boxes;
} );
