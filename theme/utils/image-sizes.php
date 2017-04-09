<?php

add_action('after_setup_theme', 'add_image_sizes');
function add_image_sizes() {
	add_image_size('post-thumbnails', 1200, 9999);
	add_image_size('post_thumb', 150, 150, true);
	add_image_size('og', 1200, 630);
}
