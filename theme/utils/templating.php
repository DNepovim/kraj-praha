<?php

use Nette\Utils\Html;

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

MangoFilters::$set['wp_contexcerpt'] = function($id, $length = 45, $more = '&hellip;') {
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

MangoFilters::$set['imgsrcset'] = function($img, $class = '',  $sizes = '100%', $srcset = ['100', '150', '200', '300', '500', '700', '900', '1200', '1600'], $alt = '') {
	return Html::el('img', [
		'src' => wp_get_attachment_image_src($img, 'post_feed')[0],
		'srcset' => implode(', ', array_map(function($size) use($img) {
				return wp_get_attachment_image_src($img, $size)[0] . ' ' . $size .'w';
			}, $srcset)),
		'sizes' => $sizes,
		'class' => $class,
		'alt' => $alt
	]);
};
