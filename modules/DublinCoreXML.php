<?php


namespace publin\modules;

use DOMDocument;
use publin\src\Publication;

class DublinCoreXML {

	public function export(Publication $publication) {

		$fields = array();
		$fields[] = array('dc:type', 'Text');
		$fields[] = array('dc:title', $publication->getTitle());
		foreach ($publication->getAuthors() as $author) {
			if ($author->getLastName() && $author->getFirstName()) {
				$fields[] = array('dc:creator', $author->getLastName().', '.$author->getFirstName(true));
			}
		}
		$fields[] = array('dcterms:issued', $publication->getDatePublished('Y-m-d'));
		//$fields[] = array('dcterms:bibliographicCitation', false); // TODO
		$fields[] = array('dc:publisher', $publication->getPublisherName());
		$fields[] = array('dc:identifier', $publication->getDoi());

		$dom = new DOMDocument('1.0', 'utf-8');
		$entry = $dom->createElement('metadata');
		$entry->setAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
		$entry->setAttribute('xmlns:dcterms', 'http://purl.org/dc/terms/');
		foreach ($fields as $field) {
			if ($field[1]) {
				$entry->appendChild($dom->createElement($field[0], $field[1]));
			}
		}
		$dom->appendChild($entry);

		return $dom->saveXML();
	}
}
