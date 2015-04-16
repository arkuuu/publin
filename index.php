<?php

namespace publin;

use publin\src\MainController;
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

Config::setup();

$request = new Request();
$controller = new MainController();
echo $controller->run($request);

