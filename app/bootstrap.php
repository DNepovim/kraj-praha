<?php

require __DIR__ . '/exceptions.php';
require __DIR__ . '/Env.php';
require __DIR__ . '/Configurator.php';
require __DIR__ . '/Container.php';
require __DIR__ . '/BugsnagLogger.php';

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
