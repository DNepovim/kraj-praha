<?php

setlocale(LC_ALL,  get_locale().'.utf-8');

use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

$View = new ArrayHash;
$View->parameters = ArrayHash::from($App->parameters);

define('THEME_DIR', dirname(__FILE__));
define('THEME_UTILS_DIR', THEME_DIR . '/utils');
define('ADMIN_UTILS_DIR', THEME_DIR . '/admin');
define('API_DIR', THEME_DIR . '/api');
define('FORMS_DIR', THEME_DIR . '/forms');
define('THEME_VIEWS_DIR', THEME_DIR . '/views');
define('NEON_WP_DIR', __DIR__ . '/define');

foreach(glob(THEME_UTILS_DIR . '/*.php') as $filename) {
	require_once $filename;
}

$Forms = new ArrayHash;
foreach(glob(FORMS_DIR . '/*.php') as $filename) {
	$Forms[basename($filename, '.php')] = require_once $filename;
}
$View->Forms = $Forms;

if(is_admin()) foreach(glob(ADMIN_UTILS_DIR . '/*.php') as $filename) {
	require_once $filename;
}

@include __DIR__ . '/init.php';

// CSRF protection
$App->getService('session')->start();

if(Strings::startsWith($Url->pathInfo, 'api/')) {
	$ApiRequest = Strings::split(Strings::trim($Url->pathInfo, '~/+~'), '~/~');
	array_shift($ApiRequest);
	require API_DIR . '/index.php';
}
