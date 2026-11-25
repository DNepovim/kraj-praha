<?php

require __DIR__ . '/exceptions.php';
require __DIR__ . '/Env.php';
require __DIR__ . '/Configurator.php';
require __DIR__ . '/Container.php';
require __DIR__ . '/BugsnagLogger.php';

// Hosting may configure session.save_handler = redis without the redis extension loaded,
// which makes every session_start() fail. Fall back to file sessions in that case.
if (ini_get('session.save_handler') === 'redis' && !extension_loaded('redis')) {
	$sessionDir = TEMP_DIR . '/sessions';
	@mkdir($sessionDir, 0770, true);
	ini_set('session.save_handler', 'files');
	ini_set('session.save_path', $sessionDir);
}

$configurator = new App\Config\Configurator;
$container = $configurator->createContainer();

// Report errors logged by Tracy to Bugsnag (production only)
$bugsnagKey = $container->parameters['bugsnagKey'] ?? null;
if ($bugsnagKey && Tracy\Debugger::$productionMode === true && class_exists(Bugsnag\Client::class)) {
	$bugsnag = Bugsnag\Client::make($bugsnagKey);
	$bugsnag->setReleaseStage('production');
	Tracy\Debugger::setLogger(new App\Config\BugsnagLogger(
		$bugsnag,
		Tracy\Debugger::$logDirectory,
		Tracy\Debugger::$email,
		Tracy\Debugger::getBlueScreen()
	));
}

require __DIR__ . '/shortcuts.php';
require __DIR__ . '/wp-utils.php';

// Pass the Container to the WordPress part
return new MangoPress\Container($container);
