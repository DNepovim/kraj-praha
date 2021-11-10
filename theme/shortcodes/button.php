<?php
function shortcode_button($atts) {
	return renderLatteToString(__DIR__ . '/../views/components/button.latte', $atts);
}

add_shortcode('button', 'shortcode_button');
