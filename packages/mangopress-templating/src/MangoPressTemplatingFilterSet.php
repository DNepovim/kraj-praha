<?php

class MangoPressTemplatingFilterSet {

	public static $set = [];

	public static function install(Latte\Engine $latte) {
		$me = new static;
		$latte->addFilter('emphasize', [$me, 'filterEmphasize']);

		foreach(self::$set as $filter_name => $callback) {
			$latte->addFilter($filter_name, $callback);
		}
	}

	public function filterEmphasize($value) {
		return "› $value ‹";
	}

}

// alias
class MangoFilters extends MangoPressTemplatingFilterSet {}
