<?php

// Include Custom Post Types definition
include_once 'src/cpt/define.php';
include_once 'src/cpt/meta.php';

// Register menu
register_nav_menu('main_menu', 'Hlavní menu');

// Register thumbnail
add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 1200, 9999 );
add_image_size('post_thumb', 150, 150);

