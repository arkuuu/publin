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

if (!isset($_GET['p'])) {
	$p = 'start';
}
else {
	$p = $_GET['p'];
}

if (!isset($_GET['id'])) {
	$id = null;
}
else {
	$id = $_GET['id'];
}

if (!isset($_GET['by'])) {
	$by = null;
}
else {
	$by = $_GET['by'];
}
$request = new Request();
$controller = new Controller();
echo $controller->run($request);

