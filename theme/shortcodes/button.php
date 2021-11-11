<?php
function shortcode_button($atts) {
	return renderLatteToString(__DIR__ . '/../views/components/button.latte', [
		"label" => !empty($atts["label"]) ? $atts["label"] : "",
		"style" => !empty($atts["style"]) ? $atts["style"] : "inv",
		"attrs" => [
			"href" => !empty($atts["link"]) ? $atts["link"] : "",
			"target" => !empty($atts["targetBlank"]) && $atts["targetBlank"] ? "_blank" : "_self",
			"rel" => !empty($atts["targetBlank"]) && $atts["targetBlank"] ? "noreferrer noopener" : ""
		]
	]);
}

add_shortcode('button', 'shortcode_button');
