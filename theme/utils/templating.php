<?php

MangoPressTemplating::init();

// czech number format
MangoFilters::$set['number'] = function($number, $decimal = 2) {
	if(fmod($number, 1) == 0) {
		$decimal = 0;
	}
	$sep = ',';
	$formatted = number_format($number, $decimal, $sep, "\xC2\xA0");
	if($decimal) {
		$formatted = Strings::replace($formatted, '~,?0$~');
	}

	return $formatted;
};

// czech Kč number format
MangoFilters::$set['czk'] = function($number, $decimal = 2){
	if(fmod($number, 1) == 0) {
		$decimal = 0;
	}
	$sep = ',';
	$formatted = number_format($number, $decimal, $sep, "\xC2\xA0");
	if($decimal) {
		$formatted = Strings::replace($formatted, '~,?0*$~', '');
	}

	return $formatted . (!$decimal ? ',-' : '') . "\xC2\xA0Kč";
};

MangoFilters::$set['wp_author'] = function($id) {
	$post = lazy_post($id);
	if(!$post) return $id;
	$user = get_user_by('id', $post->post_author);

	return safe($user->display_name);
};

MangoFilters::$set['wp_pubdate'] = function($id) {
	$post = lazy_post($id);
	if(!$post) return $id;

	return safe( get_the_time( get_option( 'date_format' ), $post));
};

MangoFilters::$set['wp_contexcerpt'] = function($id, $length = 55, $more = '&hellip;') {
	$post = lazy_post($id);
	if(!$post) return $id;

	if (!empty($post->post_excerpt)) {
		$output = $post->post_excerpt;
	} else {

		$output = strip_shortcodes($post->post_content);

		$output = apply_filters( 'the_content', $output );
		$output = str_replace(']]>', ']]&gt;', $output);

	}

	$output = wp_trim_words( $output, $length, $more );

	return safe($output);
};

MangoFilters::$set['attrs'] = function($array) {
	return safe(implode(', ', array_map(
		function ($v, $k) { return sprintf("%s='%s'", $k, $v); },
		$array,
		array_keys($array)
	)));
};
