<?php
function shortcode_button($atts) {
	$targetBlank = in_array("targetBlank", $atts);
	$fullWidth = in_array("fullWidth", $atts);
	return renderLatteToString(__DIR__ . '/../views/components/button.latte', [
		"label" => !empty($atts["label"]) ? $atts["label"] : "",
		"style" => !empty($atts["style"]) ? $atts["style"] : "inv",
		"attrs" => [
			"href" => !empty($atts["link"]) ? $atts["link"] : "",
			"fullWidth" => $fullWidth,
			"target" => $targetBlank ? "_blank" : "_self",
			"rel" => $targetBlank ? "noreferrer noopener" : "",
		]
	]);
}

add_shortcode('button', 'shortcode_button');
