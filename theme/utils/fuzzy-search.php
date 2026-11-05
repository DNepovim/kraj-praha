<?php

const FUZZY_VOCABULARY_KEY = 'fuzzy_search_vocabulary';

function fuzzy_normalize($text) {
	return mb_strtolower(remove_accents($text));
}

function fuzzy_words($text) {
	return preg_split('/[^\p{L}\p{N}]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
}

// Words used in titles of searchable posts, normalized form => original lowercase form
function fuzzy_vocabulary() {
	$vocabulary = get_transient(FUZZY_VOCABULARY_KEY);

	if ($vocabulary === false) {
		global $wpdb;

		$types = array_values(get_post_types(['exclude_from_search' => false]));
		$placeholders = implode(',', array_fill(0, count($types), '%s'));
		$titles = $wpdb->get_col($wpdb->prepare(
			"SELECT post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_type IN ($placeholders)",
			$types
		));

		$vocabulary = [];
		foreach ($titles as $title) {
			foreach (fuzzy_words($title) as $word) {
				if (mb_strlen($word) >= 3) {
					$vocabulary[fuzzy_normalize($word)] = mb_strtolower($word);
				}
			}
		}

		set_transient(FUZZY_VOCABULARY_KEY, $vocabulary, DAY_IN_SECONDS);
	}

	return $vocabulary;
}

add_action('save_post', function() {
	delete_transient(FUZZY_VOCABULARY_KEY);
});

// Returns the query with misspelled words replaced by the closest known word, or null when nothing was corrected
function fuzzy_suggestion($query) {
	$vocabulary = fuzzy_vocabulary();
	$corrected = false;
	$result = [];

	foreach (fuzzy_words($query) as $word) {
		$normalized = fuzzy_normalize($word);
		$maxDistance = mb_strlen($normalized) <= 4 ? 1 : 2;
		$best = $word;
		$bestDistance = $maxDistance + 1;

		if (isset($vocabulary[$normalized])) {
			$result[] = $word;
			continue;
		}

		foreach ($vocabulary as $candidateNormalized => $candidate) {
			// levenshtein() is byte based, so the diacritics-free normalized form is used
			$distance = levenshtein($normalized, $candidateNormalized);
			if ($distance < $bestDistance) {
				$best = $candidate;
				$bestDistance = $distance;
			}
		}

		if ($bestDistance <= $maxDistance) {
			$corrected = true;
		}
		$result[] = $best;
	}

	return $corrected ? implode(' ', $result) : null;
}
