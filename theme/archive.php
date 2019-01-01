<?php

if (have_posts()) {
	$atts = [
		'title' => 'Příspěvky v rubrice „' . trim(wp_title('', false)) . '“:',
	];
} else {
	$atts = [
		'title' => 'V rubrice „' . trim(wp_title('', false)) . '“ nejsou žádné příspěvky.',
	];
}

view($atts);
