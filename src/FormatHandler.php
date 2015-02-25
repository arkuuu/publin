<?php

namespace publin\src;

use BadMethodCallException;
use DomainException;

class FormatHandler {

	public static function export(Publication $publication, $format) {

		$class = '\\publin\\modules\\'.$format;

		if (class_exists($class)) {
			$module = new $class();

			if (method_exists($module, 'export')) {

				return $module->export($publication);
			}
			else {
				throw new BadMethodCallException('export method in module for '.$format.' not found');
			}
		}
		else {
			throw new DomainException('module class for '.$format.' not found');
		}
	}


	public static function import($data, $format) {

		$class = '\\publin\\modules\\'.$format;

		if (class_exists($class)) {
			$module = new $class();

			if (method_exists($module, 'import')) {

				return $module->import($data);
			}
			else {
				throw new BadMethodCallException('import method in module for '.$format.' not found');
			}
		}
		else {
			throw new DomainException('module class for '.$format.' not found');
		}
	}
}
