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

	// proportional (not cropped), used by the banner widget; the sizes above are hard-cropped squares
	// widths cover the sidebar at 1x/2x: 291px desktop, ~375px phone, up to ~694px tablet; larger falls back to the full image
	add_image_size('banner_300', 300, 9999);
	add_image_size('banner_600', 600, 9999);
	add_image_size('banner_900', 900, 9999);
}
