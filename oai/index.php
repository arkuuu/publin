<?php

namespace publin\oai;

spl_autoload_register(function ($class) {

	$path = str_replace('\\', '/', $class);
	$root = substr(__DIR__, 0, -(strlen(__NAMESPACE__)));
	$file = $root.$path.'.php';

	if (file_exists($file)) {
		/** @noinspection PhpIncludeInspection */
		require $file;
	}
});

if (isset($_GET['verb'])) {
	header('Content-type: text/xml; charset=utf-8');
	$parser = new OAIParser();
	echo $parser->run($_GET);
}
else {
	echo '	<a href="?verb=Identify">Identify</a><br/>
			<a href="?verb=ListMetadataFormats">ListMetadataFormats</a><br/>
			<a href="?verb=ListSets">ListSets</a><br/>
			<a href="?verb=GetRecord">GetRecord (example)</a><br/>';
}
