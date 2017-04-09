<?php
add_filter( 'rwmb_meta_boxes', function ( $meta_boxes )
{
	$meta_boxes[] = [
		'title'  => 'Clone group with image',
		'fields' => [
			[
				'name'  => 'Single Image',
				'id'    => 'img2',
				'type'  => 'image_advanced',
			],
			[
				'name'  => 'Single Image clone',
				'id'    => 'img',
				'type'  => 'image_advanced',
				'clone' => true,
			],
			[
				'id'     => 'group5',
				'type'   => 'group',
				'clone'  => true,
				'fields' => [
					[
						'name'  => 'Image',
						'id'    => 'text5',
						'type'  => 'image_advanced',
						'clone' => true,
					],
					[
						'name'   => 'Sub group',
						'id'     => 'subgroup5',
						'type'   => 'group',
						'clone'  => true,
						'fields' => [
							[
								'name'  => 'Image',
								'id'    => 'text6',
								'type'  => 'image_advanced',
								'clone' => true,
							],
						],
					],
				],
			],
		],
	];
	return $meta_boxes;
} );
