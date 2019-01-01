<?php
use Nette\Utils\Html;

function site_title() {
	$title = Html::el();

	if (function_exists('is_tag') && is_tag()) {
		$title->setHtml('Štítky "' . $tag . '" | ' . get_bloginfo('name'));
	} elseif (is_archive()) {
		$title->setHtml(wp_title('', FALSE) . ' - archiv příspěvků | ' . get_bloginfo('name'));
	} elseif (is_search()) {
		$title->setHtml('Vyhledávání "' . wp_specialchars($_GET['s']) . '" | ' . get_bloginfo('name'));
	} elseif (!(is_404()) && (is_single()) || (is_page())) {
		$title->setHtml(wp_title('', FALSE) . ' | ' . get_bloginfo('name'));
	} elseif (is_404()) {
		$title->setHtml('Stránka nenalezena | ' . get_bloginfo('name'));
	} else {
		$title->setHtml(get_bloginfo('name'));
	}

	return $title;
}
