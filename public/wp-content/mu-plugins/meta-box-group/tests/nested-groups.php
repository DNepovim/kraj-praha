<?php
add_filter( 'rwmb_meta_boxes', function ( $meta_boxes )
{
	// Meta Box
	$meta_boxes[] = array(
		'title' => __( 'Books', 'rwmb' ),

		'fields' => array(
			array(
				'id'     => 'authors',
				'name'   => __( 'Authors', 'rwmb' ),
				'type'   => 'group', // Group type
				'clone'  => true,

				// List of child fields
				'fields' => array(
					array(
						'name'    => __( 'Full Name', 'rwmb' ),
						'id'      => 'name',
						'type'    => 'group',
						'columns' => 4, // Display child field in grid columns

						'fields' => array(
							[
								'id'   => 'first_name',
								'name' => 'First Name',
								'type' => 'text',
								'std'  => 'Test name',
							],
							[
								'id'   => 'last_name',
								'name' => 'Last Name',
								'type' => 'text',
							],
						),
					),
					array(
						'name'    => __( 'Phone', 'rwmb' ),
						'id'      => 'phone',
						'type'    => 'text',
						'size'    => 10,
						'columns' => 4, // Display child field in grid columns
						'std' => '123123'
					),
					array(
						'name'    => __( 'Email', 'rwmb' ),
						'id'      => 'email',
						'type'    => 'email',
						'size'    => 15,
						'columns' => 4, // Display child field in grid columns
					),
				),
			),
		),
	);

	return $meta_boxes;
} );
