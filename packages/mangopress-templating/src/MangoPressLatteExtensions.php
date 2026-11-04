<?php declare(strict_types = 1);

use Latte\Engine;
use Nette\StaticClass;
use Nette\Utils\Callback;


class MangoPressLatteExtensions {

	use StaticClass;

	/** @var callable[] (Engine $latte) */
	private static $extensions = [];


	public static function addExtension(callable $extension) {
		self::$extensions[] = $extension;
	}


	public static function invoke(Engine $latte) {
		foreach (self::$extensions as $extension) {
			Callback::invoke($extension, $latte);
		}
	}

}
