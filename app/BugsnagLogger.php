<?php

namespace App\Config;

use Bugsnag\Client;
use Tracy\BlueScreen;
use Tracy\Logger;

/**
 * Tracy logger that also reports everything above INFO level to Bugsnag.
 */
class BugsnagLogger extends Logger
{
	/** @var Client */
	private $client;

	public function __construct(Client $client, ?string $directory, $email = null, ?BlueScreen $blueScreen = null)
	{
		parent::__construct($directory, $email, $blueScreen);
		$this->client = $client;
	}

	public function log($value, $level = self::INFO)
	{
		$file = parent::log($value, $level);

		if (!in_array($level, [self::DEBUG, self::INFO], true)) {
			try {
				$this->notify($value, $level);
			} catch (\Throwable $e) {
				// reporting must never break the page
			}
		}

		return $file;
	}

	private function notify($value, string $level): void
	{
		$severity = $level === self::WARNING ? 'warning' : 'error';
		$callback = function ($report) use ($severity) {
			$report->setSeverity($severity);
		};

		if ($value instanceof \Throwable) {
			$this->client->notifyException($value, $callback);
		} else {
			$message = is_string($value) ? $value : print_r($value, true);
			$this->client->notifyError(ucfirst($level), $message, $callback);
		}
	}
}
