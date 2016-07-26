<?php

add_filter( 'rwmb_meta_boxes', 'your_prefix_meta_boxes' );
function your_prefix_meta_boxes( $meta_boxes ) {
    $meta_boxes[] = array(
        'title'      => __( 'Podrobnosti', 'textdomain' ),
        'post_types' => 'event',
        'fields'     => array(
            array(
                'id'   => 'start_date',
                'name' => __('Začátek'),
                'type' => 'datetime'
            ),
            array(
                'id'   => 'end_date',
                'name' => __('Konec'),
                'type' => 'datetime'
            ),
            array(
                'id'   => 'position',
                'name' => __( 'Místo', 'textdomain' ),
                'type' => 'map',
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
                'id'      => 'url',
                'name'    => __( 'Web', 'textdomain' ),
                'type'    => 'url'
            ),
            array(
                'id'   => 'email',
                'name' => __( 'E-mail', 'textdomain' ),
                'type' => 'email',
            ),
            array(
                'id'   => 'login',
                'name' => __( 'Odkaz na přihlašování', 'textdomain' ),
                'type' => 'url',
            ),
            array(
                'id'   => 'fb',
                'name' => __('Facebook'),
                'desc' => __('Odkaz na stránku nebo událost'),
                'type' => 'url'
            )
        ),
    );
    return $meta_boxes;
}
