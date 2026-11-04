<?php

require_once __DIR__ . '/../src/MangoPressTemplatingMacroSet.php';
require_once __DIR__ . '/../src/MangoPressTemplatingFilterSet.php';
require_once __DIR__ . '/../src/MangoPressSnippetBridge.php';

function toPath($url) {
	$urlscript = new Nette\Http\UrlScript($url);
	return rtrim($urlscript->scheme . '://' . $urlscript->authority . $urlscript->path, '/');
}

function toRelativePath($url) {
	$urlscript = new Nette\Http\UrlScript($url);
	return rtrim($urlscript->getPath(), '/');
}

function renderLatte($path, $parameters = [], $snippetMode = false) {
	global $App;
	global $View;
	global $wp_query;
	global $post;

	$assetsDirname = !empty($App->parameters['assetsDirname']) ? trim($App->parameters['assetsDirname'], '/') : 'assets';
	$home = \get_home_url();
	$fullParameters = [
		'App' => $App,
		'baseUrl' => toPath($home),
		'basePath' => toRelativePath($home),
		'assetsUrl' => toPath($home) . '/' . $assetsDirname,
		'assetsPath' => toRelativePath($home) . '/' . $assetsDirname,
		'wp_query' => $wp_query,
		'post' => $post,
		'flashes' => (defined('DISABLE_FLASH_MESSAGES') && DISABLE_FLASH_MESSAGES) ? [] : getFlashMessages(),
	];

	if(isset($View)) {
		foreach($View as $key => $val) {
			$fullParameters[$key] = $val;
		}
	}

	foreach($parameters as $key => $val) {
		$fullParameters[$key] = $val;
	}

	$latte = new Latte\Engine;
	$latte->setTempDirectory(TEMP_DIR . '/cache/latte');

	$snippetBridge = new MangoPressSnippetBridge;
	$snippetBridge->snippetMode = $snippetMode;
	$latte->addProvider('snippetBridge', $snippetBridge);

	MangoPressTemplatingMacroSet::install($latte->getCompiler());
	Nette\Bridges\FormsLatte\FormMacros::install($latte->getCompiler());

	$latte->addProvider('cacheStorage', $App->getService('cache.storage'));
	$latte->getCompiler()->addMacro('cache', new \Nette\Bridges\CacheLatte\CacheMacro($latte->getCompiler()));

	MangoPressTemplatingFilterSet::install($latte);

	MangoPressLatteExtensions::invoke($latte);

	$output = $latte->render($path, (array) $fullParameters);

	if ($snippetMode) {
		return $snippetBridge->payload;
	}

	return $output;
}

function sanitizeViewParams($view = null, $parameters = null) {
	if(is_array($view) && !$parameters) {
		$parameters = $view;
		$view = NULL;
	}
	$parameters = (array)$parameters;
	if(!$view) {
		$bt =  debug_backtrace();
		$view = basename($bt[1]['file'], '.php');
	}
	$path = THEME_VIEWS_DIR . "/$view.latte";
	return [ 'path' => $path, 'parameters' => $parameters ];
}

function view($view = NULL, $parameters = NULL) {
	$p = sanitizeViewParams($view, $parameters);
	do_action('pre_render_view');
	return renderLatte($p['path'], $p['parameters']);
}

function viewString($view = null, $parameters = null) {
	$p = sanitizeViewParams($view, $parameters);
	do_action('pre_render_view');
	return renderLatteToString($p['path'], $p['parameters']);
}

function viewSnippets($view = NULL, $parameters = NULL) {
	$p = sanitizeViewParams($view, $parameters);
	do_action('pre_render_view');
	return renderLatteToString($p['path'], $p['parameters'], true);
}

function renderLatteToString($path, $parameters = [], $snippetMode = false) {
	ob_start();
	$result = renderLatte($path, $parameters, $snippetMode);
	$str = ob_get_contents();
	ob_end_clean();
	if ($snippetMode) {
		return $result;
	}
	return $str;
}
