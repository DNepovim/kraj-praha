<?php

add_action('init', 'create_post_type');

function create_post_type()
{
    register_post_type('event',
        array(
            'labels' => array(
                'name' => __('Událost'),
            ),
            'public' => true,
            'has_archive' => true,
        )
    );
}

