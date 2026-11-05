<?php

$search = $_GET['s'];

if (!have_posts()) {
	$suggestion = fuzzy_suggestion($search);

	if ($suggestion !== null) {
		// Replaces the global main query, so the template loop and paginator use the corrected results
		query_posts([
			's' => $suggestion,
			'paged' => !empty($_GET['strana']) ? (int) $_GET['strana'] : 1,
		]);

		if (!have_posts()) {
			$suggestion = null;
		}
	}
}

if (have_posts()) {
	$atts = [
		'title' => 'Výsledky vyhledávání pro dotaz „' . $search .'“:',
	];
} else {
	$atts = [
		'title' => 'Pro dotaz „' . $search . '“ nejsou žádné výsledky.',
	];
}

view('archive', $atts);
