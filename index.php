<?php

namespace publin;

use publin\config\Config;
use publin\src\MainController;
use publin\src\Request;

require_once 'autoload.php';

Config::setup();

$request = new Request();
$controller = new MainController();
echo $controller->run($request);

