<?php

use publin\oai\OAIParser;

include 'OAIParser.php';
include '../src/Database.php';

$parser = new OAIParser();

if (isset($_GET['verb'])) {
	header('Content-type: text/xml; charset=utf-8');
	echo $parser->run($_GET);
}
else {
	echo '	<a href="?verb=Identify">Identify</a><br/>
			<a href="?verb=ListMetadataFormats">ListMetadataFormats</a><br/>
			<a href="?verb=ListSets">ListSets</a><br/>';
}
