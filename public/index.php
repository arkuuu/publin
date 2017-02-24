<?php

use arkuuu\Publin\Config\Config;
use arkuuu\Publin\MainController;
use arkuuu\Publin\Request;

require_once '../vendor/autoload.php';

Config::setup();

$request = new Request();
$controller = new MainController();
echo $controller->run($request);

