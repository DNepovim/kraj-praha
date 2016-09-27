<?php


add_filter( 'rwmb_meta_boxes', 'your_prefix_meta_boxes' );
function your_prefix_meta_boxes( $meta_boxes ) {

    $prefix = 'praha_';

    $meta_boxes[] = array(
        'title'      => __( 'Podrobnosti', 'textdomain' ),
        'post_types' => 'event',
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
                'type' => 'map',
	            'std' => '50.0823452,14.4196943,14.93z'
            ),
        ),
    );
    $meta_boxes[] = array(
        'title'      => __( 'Kontakt', 'textdomain' ),
        'post_types' => 'event',
        'context'    => 'side',
        'priority'   => 'low',
        'fields'     => array(
            array(
                'id'      => $prefix . 'url',
                'name'    => __( 'Web', 'textdomain' ),
                'type'    => 'url'
            ),
            array(
                'id'   => $prefix . 'email',
                'name' => __( 'E-mail', 'textdomain' ),
                'type' => 'email',
            ),
            array(
                'id'   => $prefix . 'login',
                'name' => __( 'Odkaz na přihlašování', 'textdomain' ),
                'type' => 'url',
            ),
            array(
                'id'   => $prefix . 'fb',
                'name' => __('Facebook'),
                'desc' => __('Odkaz na stránku nebo událost'),
                'type' => 'url'
            )
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
