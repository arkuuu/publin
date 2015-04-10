<?php

namespace publin\oai;

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

header('Content-type: text/xml; charset=utf-8');
$request = new Request();
$parser = new OAIParser();
echo $parser->run($request);



