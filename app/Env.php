<?php

namespace App\Config;

/**
 * Environment values for the config, read from a KEY=value file (config/.env).
 * Real environment variables override the keys listed in the file; other
 * variables are ignored so the process environment never ends up in the compiled container.
 */
class Env
{

	/**
	 * @return array<string, string>
	 */
	public static function load(string $file): array
	{
		$values = [];
		if (is_file($file)) {
			foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
				$line = trim($line);
				if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
					continue;
				}
				[$key, $value] = explode('=', $line, 2);
				$value = trim($value);
				if (strlen($value) >= 2 && $value[0] === substr($value, -1) && ($value[0] === '"' || $value[0] === "'")) {
					$value = substr($value, 1, -1);
				}
				$values[trim($key)] = $value;
			}
		}

		foreach ($values as $key => $value) {
			$override = getenv($key);
			if ($override !== false) {
				$values[$key] = $override;
			}
		}

		return $values;
	}

}
