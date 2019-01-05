<?php

add_action('after_setup_theme', 'add_image_sizes');
function add_image_sizes() {
	add_image_size('100', 100);
	add_image_size('150', 150);
	add_image_size('200', 200);
	add_image_size('300', 300);
	add_image_size('500', 500);
	add_image_size('700', 700);
	add_image_size('900', 900);
	add_image_size('1200', 1200);
	add_image_size('1600', 1600);

	add_image_size('post_detail', 330, 330);
	add_image_size('post_feed', 450, 450);
	add_image_size('og', 1200, 630);
}
