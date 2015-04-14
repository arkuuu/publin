<?php


namespace publin\modules;

use DOMDocument;
use Exception;
use InvalidArgumentException;
use publin\src\Publication;

class DBLPXML extends Module {

	/**
	 * @param Publication[] $publications
	 *
	 * @return string
	 * @throws Exception
	 */
	public function exportMultiple(array $publications) {

		$xml = new DOMDocument('1.0', 'utf-8');
		$dblp = $xml->appendChild($xml->createElement('dblp'));

		foreach ($publications as $publication) {

			if ($publication instanceof Publication) {
				if (!$publication->getTypeName()) {
					throw new Exception('publication type missing');
				}

				$entry = $xml->createElement($publication->getTypeName());
				$entry->setAttribute('key', 'todo'); // TODO
				$entry->setAttribute('mdate', 'todo'); // TODO
				$dblp->appendChild($entry);

				$fields = $this->createFields($publication);
				foreach ($fields as $field) {
					if ($field[1]) {
						$element = $xml->createElement($field[0]);
						$element->appendChild($xml->createTextNode($field[1]));
						$entry->appendChild($element);
					}
				}
			}
			else {
				throw new InvalidArgumentException('parameter must be Publication');
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
		foreach ($publication->getAuthors() as $author) {
			if ($author->getFirstName() && $author->getLastName()) {
				$fields[] = array('author', $author->getFirstName().' '.$author->getLastName());
			}
		}
		$fields[] = array('title', $publication->getTitle());
		$fields[] = array('journal', $publication->getJournal());
		$fields[] = array('booktitle', $publication->getBooktitle());
		$fields[] = array('volume', $publication->getVolume());
		$fields[] = array('number', $publication->getNumber());
		$fields[] = array('series', $publication->getSeries());
		$fields[] = array('edition', $publication->getEdition());
		$fields[] = array('pages', $publication->getPages('--'));
		$fields[] = array('month', $publication->getDatePublished('F'));
		$fields[] = array('year', $publication->getDatePublished('Y'));
		//$fields[] = array('url', false); // TODO: link to pdf
		//$fields[] = array('issn', false); // TODO
		$fields[] = array('isbn', $publication->getIsbn());
		$fields[] = array('institution', $publication->getInstitution());
		$fields[] = array('school', $publication->getSchool());
		$fields[] = array('publisher', $publication->getPublisher());
		$fields[] = array('ee', $publication->getDoi());
		$fields[] = array('address', $publication->getAddress());
		$fields[] = array('howpublished', $publication->getHowpublished());
		$fields[] = array('note', $publication->getNote());
		$fields[] = array('abstract', $publication->getAbstract());
		//$fields[] = array('bibsource', $this->bibsource); // TODO
		//$fields[] = array('biburl', $this->url.$publication->getId()); // TODO

		return $fields;
	}


	/**
	 * @param Publication $publication
	 *
	 * @return string
	 * @throws Exception
	 */
	public function export(Publication $publication) {

		if (!$publication->getTypeName()) {
			throw new Exception('publication type missing');
		}

		$xml = new DOMDocument('1.0', 'utf-8');
		$dblp = $xml->appendChild($xml->createElement('dblp'));
		$entry = $xml->createElement($publication->getTypeName());
		$entry->setAttribute('key', 'todo'); // TODO
		$entry->setAttribute('mdate', 'todo'); // TODO
		$dblp->appendChild($entry);

		$fields = $this->createFields($publication);
		foreach ($fields as $field) {
			if ($field[1]) {
				$entry->appendChild($xml->createElement($field[0], htmlspecialchars($field[1])));
			}
		}

		return $xml->saveXML();
	}
}
