<?php
if (have_posts()) {
	$atts = [
		'title' => 'Příspěvky od autora „' . trim(wp_title('', false)) . '“:',
	];
} else {
	$atts = [
		'title' => 'Autor „' . trim(wp_title('', false)) . '“ nenapsal žádné příspěvky.',
	];
}

view('archive', $atts);
