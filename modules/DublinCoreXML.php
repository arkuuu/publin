<?php


namespace publin\modules;

use DOMDocument;
use publin\src\Publication;

class DublinCoreXML {

	public function export(Publication $publication) {

		$dom = new DOMDocument('1.0', 'utf-8');
		$entry = $dom->createElement('metadata');
		$entry->setAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
		$entry->setAttribute('xmlns:dcterms', 'http://purl.org/dc/terms/');
		$entry->appendChild($dom->createElement('dc:type', 'Text'));

		if ($publication->getTitle()) {
			$entry->appendChild($dom->createElement('dc:title', $publication->getTitle()));
		}
		foreach ($publication->getAuthors() as $author) {
			if ($author->getLastName() && $author->getFirstName()) {
				$entry->appendChild($dom->createElement('dc:creator', $author->getLastName().', '.$author->getFirstName(true)));
			}
		}
		if ($publication->getDatePublished('Y-m-d')) {
			$entry->appendChild($dom->createElement('dcterms:issued', $publication->getDatePublished('Y-m-d')));
		}
		if (true) {
			$entry->appendChild($dom->createElement('dcterms:bibliographicCitation', 'todo'));
		}
		if ($publication->getPublisherName()) {
			$entry->appendChild($dom->createElement('dc:publisher', $publication->getPublisherName()));
		}
		if ($publication->getDoi()) {
			$entry->appendChild($dom->createElement('dc:identifier', $publication->getDoi()));
		}

		$dom->appendChild($entry);

		return $dom->saveXML();
	}
}
