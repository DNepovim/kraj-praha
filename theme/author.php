<?php

$authorName = trim(wp_title('', false));

if (have_posts()) {
	$atts = [
		'title' => 'Články od autora ' . $authorName . '“:',
	];
} else {
	$atts = [
		'title' => 'Autor ' . $authorName . ' nenapsal žádné články.',
	];
}

view('archive', $atts);
