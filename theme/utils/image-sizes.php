<?php

add_action('after_setup_theme', 'add_image_sizes');
function add_image_sizes() {
	add_image_size('100', 100, 100, ['center', 'top']);
	add_image_size('150', 150, 150, ['center', 'top']);
	add_image_size('200', 200, 200, ['center', 'top']);
	add_image_size('300', 300, 300, ['center', 'top']);
	add_image_size('500', 500, 500, ['center', 'top']);
	add_image_size('700', 700, 700, ['center', 'top']);
	add_image_size('900', 900, 900, ['center', 'top']);
	add_image_size('1200', 1200, 1200, ['center', 'top']);
	add_image_size('1600', 1600, 1600, ['center', 'top']);

	add_image_size('post_detail', 330, 330);
	add_image_size('post_feed', 450, 450);
	add_image_size('og', 1200, 630);
}
