<?php

/* create list of authors */
$authors = $publication -> getAuthors();
$num = count($authors);

$authors_string = '';
foreach ($publication -> getAuthors() as $author) {
	$authors_string .= $author -> getName().' and ';
}
$authors_string = substr($authors_string, 0, -5);


$key_terms_string = '';
foreach ($publication -> getKeyTerms() as $key_term) {
	$key_terms_string .= $key_term -> getName().', ';
}
$key_terms_string = substr($key_terms_string, 0, -2);


$data['type'] = $publication -> getTypeName();
$data['cite_key'] = 'todo';
$data['title'] = $publication -> getTitle();
$data['author'] = $authors_string;
$data['journal'] = $publication -> getJournalName();
$data['booktitle'] = $publication -> getBookName();
$data['publisher'] = $publication -> getPublisherName();
// $data['edition'] = $publication -> getEdition();
// $data['institution'] = $publication -> getInstitutionName();
// $data['howpublished'] = $publication -> getHowpublished();
$data['year'] = $publication -> getDatePublished('Y');
$data['month'] = $publication -> getDatePublished('F');
$data['volume'] = $publication -> getVolume();
$data['pages'] = $publication -> getPages('--');
$data['number'] = $publication -> getNumber();
$data['series'] = $publication -> getSeries();
$data['abstract'] = $publication -> getAbstract();
// some more missing
$data['keywords'] = $key_terms_string;

/* create BibTeX code */
$export = '@'.$data['type'].'{'.$data['cite_key'];

unset($data['type'], $data['cite_key']);

foreach ($data as $key => $value) {
	if (!empty($value)) {
		$export .= ','."\n\t".$key.' = {'.$value.'}';
	}
}
$export .= "\n".'}';
