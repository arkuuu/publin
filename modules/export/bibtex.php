<?php

/* create list of authors */
$authors = $publication -> getAuthors();
$num = count($authors);
$authors_string = '';

if ($num < 1) {
	$authors_string = 'Unknown Author';
}
else {
	$i = 1;
	foreach ($authors as $author) {
		if ($i == 1) {
			$authors_string = $author -> getName();
		}
		else {
			$authors_string .= ' and '.$author -> getName();
		}
		$i++;
	}
}

/* create CiteKey */
$citeKey = 'todo';

/* TODO: temporary variables */	
$journal = 'todo';
$volume = 'todo';

/* create BibTeX code */
$export = 	
	"@".$publication -> getType()."{".$citeKey.",
		author = {".$authors_string."},
		title = {".$publication -> getTitle()."},
		journal = {".$journal."},
		volume = {".$volume."},
		year = {".$publication -> getDatePublished('Y')."},
	}";
