<?php

namespace publin;

use publin\src\Controller;
use publin\src\Request;

spl_autoload_register(function ($class) {

	$path = str_replace('\\', '/', $class);
	$root = substr(__DIR__, 0, -(strlen(__NAMESPACE__)));
	$file = $root.$path.'.php';

	if (file_exists($file)) {
		/** @noinspection PhpIncludeInspection */
		require $file;
	}
});

if (get_magic_quotes_gpc()) {
	function stripslashes_array(array &$array) {

		foreach ($array as $key => $value) {
			$new_key = stripslashes($key);
			if ($new_key != $key) {
				$array[$new_key] = &$value;
				unset($array[$key]);
			}
			if (is_array($value)) {
				stripslashes_array($value);
			}
			else {
				$array[$new_key] = stripslashes($value);
			}
		}
	}

	;
	stripslashes_array($_POST);
	stripslashes_array($_GET);
	stripslashes_array($_REQUEST);
	stripslashes_array($_COOKIE);
}

$request = new Request();
$controller = new Controller();
echo $controller->run($request);

