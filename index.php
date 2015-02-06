<?php

namespace publin;

use publin\src\Controller;

spl_autoload_register(function ($class) {

	$path = substr(str_replace('\\', '/', $class), strlen(__NAMESPACE__));
	$path = __DIR__.$path.'.php';
	if (file_exists($path)) {
		require $path;
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

$controller = new Controller();
echo $controller->run($p, $id, $by);

