<?php


namespace publin\modules;

use Exception;
use publin\src\Publication;

class RIS {

	public function export(Publication $publication) {

		// http://de.wikipedia.org/wiki/RIS_%28Dateiformat%29
		// TODO: maybe make this a individual method?
		switch ($publication->getTypeName()) {
			case 'article':
				$type = 'JOUR';
				break;
			case 'inproceedings':
				$type = 'CONF';
				break;
			case 'incollection':
				$type = 'CHAP';
				break;
			case 'book':
				$type = 'BOOK';
				break;
			case 'masterthesis':
			case 'phdthesis':
				$type = 'THES';
				break;
			case 'techreport':
				$type = 'RPRT'; // TODO: check if valid
				break;
			case 'misc':
				$type = 'GEN';
				break;
			case 'unpublished':
				$type = 'UNPB';
				break;
			default:
				throw new Exception('unknown or missing publication type');
				break;
		}

		$fields = array();
		$fields[] = array('TY', $type);
		foreach ($publication->getAuthors() as $keyword) {
			if ($keyword->getLastName() && $keyword->getFirstName()) {
				$fields[] = array('AU', $keyword->getLastName().', '.$keyword->getFirstName());
			}
		}
		$fields[] = array('T1', $publication->getTitle()); // TODO: check if valid
		$fields[] = array('JA', $publication->getJournalName()); // TODO: check if valid
		$fields[] = array('TI', $publication->getBookName()); // TODO: check if valid
		$fields[] = array('VL', $publication->getVolume());
		$fields[] = array('IS', $publication->getNumber());
		$fields[] = array('SP', $publication->getFirstPage());
		$fields[] = array('EP', $publication->getLastPage());
		$fields[] = array('PY', $publication->getDatePublished('Y/m/d'));
		$fields[] = array('PB', $publication->getPublisherName());
		$fields[] = array('N1', $publication->getNote());
		$fields[] = array('L1', false); // TODO: link to pdf
		$fields[] = array('UR', $publication->getDoi()); // TODO: link to doi or link to publin page
		$fields[] = array('SN', $publication->getIsbn());
		$fields[] = array('AB', $publication->getAbstract());
		foreach ($publication->getKeywords() as $keyword) {
			$fields[] = array('KW', $keyword->getName());
		}

		$result = '';
		foreach ($fields as $field) {
			if ($field[1]) {
				$result .= "\n".$field[0].'  - '.htmlspecialchars($field[1]);
			}
		}
		$result .= "\n".'ER  -';

		return $result;
	}
}
