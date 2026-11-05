<?php
// Template, the deploy workflow replaces __TOKEN_HASH__ and uploads it to public/ next to release.zip.
// Unpacks the release over the site and removes itself, so it is only reachable during a deploy.

header('Content-Type: text/plain; charset=utf-8');

function fail($code, $message)
{
	http_response_code($code);
	echo $message . "\n";
	exit;
}

function remove_dir($dir)
{
	if (!is_dir($dir)) {
		return;
	}
	$items = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
		RecursiveIteratorIterator::CHILD_FIRST
	);
	foreach ($items as $item) {
		$item->isDir() && !$item->isLink() ? rmdir($item->getPathname()) : unlink($item->getPathname());
	}
	rmdir($dir);
}

$token = isset($_POST['token']) ? (string) $_POST['token'] : '';
if (!hash_equals('__TOKEN_HASH__', hash('sha256', $token))) {
	fail(403, 'forbidden');
}

@set_time_limit(0);
ignore_user_abort(true);

$root = dirname(__DIR__);
$archive = $root . '/release.zip';

if (!class_exists('ZipArchive')) {
	fail(500, 'ZipArchive extension is missing');
}

$zip = new ZipArchive;
if ($zip->open($archive) !== true) {
	fail(500, 'cannot open release.zip');
}
if (!$zip->extractTo($root)) {
	fail(500, 'extraction failed, release.zip kept for a retry');
}
$count = $zip->numFiles;
$zip->close();

// Nette container cache is not invalidated automatically in production.
remove_dir($root . '/temp/cache');
mkdir($root . '/temp/cache', 0775, true);

if (function_exists('opcache_reset')) {
	opcache_reset();
}

@unlink($root . '/public/maintenance.php');
unlink($archive);

echo 'DEPLOY_OK ' . $count . "\n";
@unlink(__FILE__);
