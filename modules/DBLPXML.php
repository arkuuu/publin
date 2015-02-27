<?php


namespace publin\modules;

use DOMDocument;
use Exception;
use publin\src\Publication;

class DBLPXML {

	public function export(Publication $publication) {

		if (!$publication->getTypeName()) {
			throw new Exception('publication type missing');
		}

		$fields = array();
		foreach ($publication->getAuthors() as $author) {
			if ($author->getFirstName() && $author->getLastName()) {
				$fields[] = array('author', $author->getFirstName().' '.$author->getLastName());
			}
		}
		$fields[] = array('title', $publication->getTitle());
		$fields[] = array('journal', $publication->getJournalName());
		$fields[] = array('booktitle', $publication->getBookName());
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
		$fields[] = array('publisher', $publication->getPublisherName());
		$fields[] = array('ee', $publication->getDoi());
		$fields[] = array('address', $publication->getAddress());
		$fields[] = array('howpublished', $publication->getHowpublished());
		$fields[] = array('note', $publication->getNote());
		$fields[] = array('abstract', $publication->getAbstract());
		//$fields[] = array('bibsource', $this->bibsource); // TODO
		//$fields[] = array('biburl', $this->url.$publication->getId()); // TODO

		$dom = new DOMDocument('1.0', 'utf-8');
		$entry = $dom->createElement($publication->getTypeName());
		$entry->setAttribute('key', 'todo'); // TODO
		$entry->setAttribute('mdate', 'todo'); // TODO
		foreach ($fields as $field) {
			if ($field[1]) {
				$entry->appendChild($dom->createElement($field[0], $field[1]));
			}
		}
		$dom->appendChild($entry);

		return $dom->saveXML();
	}
}
