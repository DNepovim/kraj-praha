<?php

$categoryName = strtolower(trim(wp_title('', false)));

if (have_posts()) {
	$atts = [
		'title' => 'Články k tématu ' . $categoryName . ':',
	];
} else {
	$atts = [
		'title' => 'K tématu ' . $categoryName . ' nejsou žádné články.',
	];
}

view($atts);
