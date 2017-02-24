<?php

use arkuuu\Publin\Config\Config;
use arkuuu\Publin\OAI\OAIParser;
use arkuuu\Publin\Request;

require_once '../../vendor/autoload.php';

Config::setup();

header('Content-type: text/xml; charset=utf-8');
$request = new Request();
$parser = new OAIParser();
echo $parser->run($request);
