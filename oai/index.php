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

mb_internal_encoding('utf8');

if (isset($_GET['verb'])) {
	header('Content-type: text/xml; charset=utf-8');
	$request = new Request();
	$parser = new OAIParser();
	echo $parser->run($request);
}
else {
	header('Content-Type: text/html; charset=UTF-8');

	echo '<html>
<head>
	<title>OAI test site</title>
</head>
<body>
<h1>OAI test site</h1>

<h2>Identify</h2>
<ul>
	<li><a href="?verb=Identify">successful</a></li>
</ul>

<h2>ListMetadataFormats</h2>
<ul>
	<li><a href="?verb=ListMetadataFormats">successful</a></li>
</ul>

<h2>ListSets</h2>
<ul>
	<li><a href="?verb=ListSets">successful</a></li>
</ul>

<h2>ListIdentifiers</h2>
<ul>
	<li><a href="?verb=ListIdentifiers">successful</a></li>
</ul>

<h2>ListRecords</h2>
<ul>
	<li><a href="?verb=ListRecords">fail missing metadataPrefix</a></li>
	<li><a href="?verb=ListRecords&amp;metadataPrefix=oaidc">fail wrong metadataPrefix</a></li>
	<li><a href="?verb=ListRecords&amp;metadataPrefix=oai_dc">success metadataPrefix</a></li>
	<li><a href="?verb=ListRecords&amp;resumptionToken=123">success resumptionToken</a></li>
</ul>

<h2>GetRecord</h2>
<ul>
	<li><a href="?verb=GetRecord&amp;identifier=bla&amp;metadataPrefix=oai_dc">fail identifier</a></li>
	<li><a href="?verb=GetRecord&amp;identifier=24&amp;metadataPrefix=oaidc">fail wrong metadataPrefix</a></li>
	<li><a href="?verb=GetRecord&amp;identifier=24&amp;metadataPrefix=oai_dc">successful</a></li>
</ul>
</body>
</html>
	';
}
