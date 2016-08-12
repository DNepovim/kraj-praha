<?php

add_action('init', 'create_post_type');

function create_post_type()
{
    add_post_type_support('page', 'excerpt');
    register_post_type('event',
        array(
            'labels' => array(
                'name' => __('Událost'),
            ),
            'public' => true,
            'has_archive' => true,
        )
    );
    register_post_type('krizovatka',
        array(
            'labels' => array(
                'name' => __('Křižovatka'),
            ),
            'public' => true,
            'has_archive' => true,
        )
    );
}

