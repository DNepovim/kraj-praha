<?php

namespace App\Config;

use Nette;
use Nette\DI;
use Nette\DI\Container;
use Nette\FileNotFoundException;
use Nette\Loaders\RobotLoader;
use RuntimeException;
use Tracy\Debugger;


/**
 * @method void onInit
 * @method void onAfter
 */
class Configurator extends Nette\Configurator
{

	/**
	 * @var array of function(Configurator $sender); Occurs before first Container is created
	 */
	public $onInit = [];

	/**
	 * @var array of function(Configurator $sender); Occurs after first Container is created
	 */
	public $onAfter = [];


	/**
	 * @param NULL|array $params
	 */
	public function __construct(array $params = NULL)
	{
		parent::__construct();

		$this->setTempDirectory(TEMP_DIR);

		$defaults = array_map('realpath', [
			'appDir' => APP_DIR,
			'libsDir' => LIBS_DIR,
			'wwwDir' => WWW_DIR,
			'logDir' => LOG_DIR,
			'configDir' => CONFIG_DIR,
			'testsDir' => TESTS_DIR,
		]);
		$defaults += [
			'testMode' => FALSE,
		];

		$this->addParameters((array) $params + $defaults);

		foreach (get_class_methods($this) as $name)
		{
			if ($pos = strpos($name, 'onInit') === 0 && $name !== 'onInitPackages')
			{
				$this->onInit[lcfirst(substr($name, $pos + 5))] = [$this, $name];
			}
		}

		foreach (get_class_methods($this) as $name)
		{
			if ($pos = strpos($name, 'onAfter') === 0)
			{
				$this->onAfter[lcfirst(substr($name, $pos + 5))] = [$this, $name];
			}
		}

		$this->createRobotLoader()->register();
	}

	public function onInitConfigs()
	{
		$params = $this->getParameters();
		$this->addConfig($params['configDir'] . '/config.neon');
		$this->addConfig($params['configDir'] . '/config.local.neon');
	}

	public function onAfterDebug(Container $c)
	{
		$p = $c->parameters;
		if (isset($p['forceDebug']))
		{
			$mode = $p['forceDebug'] === FALSE
				? Debugger::PRODUCTION
				: Debugger::DEVELOPMENT;
			Debugger::enable($mode, LOG_DIR, 'bugs+mangopress@mangoweb.cz');
		}
	}

	/**
	 * @return RobotLoader
	 */
	public function createRobotLoader()
	{
		$params = $this->getParameters();
		$loader = parent::createRobotLoader();
		$loader->addDirectory($params['appDir']);

		if ($this->isTestMode())
		{
			$loader->addDirectory($params['testsDir']);
		}

		return $loader;
	}

	/**
	 * @return array
	 */
	public function getParameters()
	{
		return $this->parameters;
	}

	/**
	 * @throws MissingLocalConfigException
	 * @throws \Exception
	 * @throws \Nette\FileNotFoundException
	 * @return Container
	 */
	public function createContainer()
	{
		$this->onInit($this);
		$this->onInit = [];

		try {
			$container = parent::createContainer();
			$this->onAfter($container);

			return $container;
		}
		catch (FileNotFoundException $e)
		{
			if (strpos($e->getMessage(), 'local') !== FALSE)
			{
				throw new MissingLocalConfigException($e);
			}
			else
			{
				throw $e;
			}
		}
	}

	protected function isConsoleMode()
	{
		return $this->parameters['consoleMode'];
	}

	protected function isTestMode()
	{
		return $this->parameters['testMode'];
	}

}


class MissingLocalConfigException extends RuntimeException
{

	/**
	 * @param \Nette\FileNotFoundException $e
	 */
	public function __construct(FileNotFoundException $e)
	{
		parent::__construct('Copy "app/config/config.local.sample.neon" to "app/config/config.local.neon" and update credentials.', NULL, $e);
	}

}
