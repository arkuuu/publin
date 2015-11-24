<?php


namespace publin\modules;

use DOMDocument;
use publin\src\Publication;

/**
 * Class DublinCoreXML
 *
 * @package publin\modules
 */
class DublinCoreXML extends Module {

	/**
	 * @param Publication $publication
	 *
	 * @return string
	 */
	public function export(Publication $publication) {

		$xml = new DOMDocument('1.0', 'utf-8');
		$metadata = $xml->createElement('metadata');
		$metadata->setAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
		$metadata->setAttribute('xmlns:dcterms', 'http://purl.org/dc/terms/');
		$xml->appendChild($metadata);

		$fields = $this->createFields($publication);
		foreach ($fields as $field) {
			if ($field[1]) {
				$element = $xml->createElement($field[0]);
				$element->appendChild($xml->createTextNode($field[1]));
				$metadata->appendChild($element);
			}
		}

		return $xml->saveXML();
	}


	/**
	 * @param Publication $publication
	 *
	 * @return array
	 */
	private function createFields(Publication $publication) {

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
		$fields[] = array('dc:publisher', $publication->getPublisher());
		$fields[] = array('dc:identifier', $publication->getDoi());

		return $fields;
	}
}