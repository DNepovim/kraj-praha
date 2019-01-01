<?php

if (have_posts()) {
	$atts = [
		'title' => 'Výsledky vyhledávání pro dotaz „' . $_GET['s'] . '“:',
	];
} else {
	$atts = [
		'title' => 'Pro dotaz „' . $_GET['s'] . '“ nejsou žádné výsledky.',
	];
}

view('archive', $atts);
