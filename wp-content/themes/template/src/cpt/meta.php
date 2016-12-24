<?php


add_filter( 'rwmb_meta_boxes', 'your_prefix_meta_boxes' );
function your_prefix_meta_boxes( $meta_boxes ) {

    $prefix = 'praha_';

    $meta_boxes[] = array(
        'title'      => __( 'Podrobnosti', 'textdomain' ),
        'post_types' => 'udalosti',
        'fields'     => array(
            array(
                'id'   => $prefix . 'start_date',
                'name' => __('Začátek'),
                'type' => 'datetime'
            ),
            array(
                'id'   => $prefix . 'end_date',
                'name' => __('Konec'),
                'type' => 'datetime'
            ),
	        array(
		        'id'   => $prefix . 'position',
		        'name' => __( 'Místo', 'textdomain' ),
		        'type' => 'text'
	        )
        ),
    );
    $meta_boxes[] = array(
        'title'      => __( 'Kontakt', 'textdomain' ),
        'post_types' => 'udalosti',
        'context'    => 'side',
        'priority'   => 'low',
        'fields'     => array(
            array(
                'id'      => $prefix . 'url',
                'name'    => __( 'Web', 'textdomain' ),
                'type'    => 'url'
            ),
        ),
    );
    $meta_boxes[] = array(
        'title'      => __( 'Související články', 'textdomain' ),
        'post_types' => 'page',
        'context'    => 'side',
        'fields'     => array(
            array(
                'id'      => $prefix . 'taxonomy',
                'name'    => __( 'Kategorie', 'textdomain' ),
                'type'    => 'taxonomy_advanced'
            )
        )
    );
    return $meta_boxes;
}
