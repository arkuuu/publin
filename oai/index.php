<?php

namespace publin\oai;

use publin\config\Config;
use publin\src\Request;

require_once '../vendor/autoload.php';

Config::setup();

header('Content-type: text/xml; charset=utf-8');
$request = new Request();
$parser = new OAIParser();
echo $parser->run($request);
