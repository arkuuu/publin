<?php


namespace publin\modules;

use Exception;
use publin\src\Publication;
use publin\src\Request;

/**
 * Class CSLJson
 *
 * @package publin\modules
 */
class CSLJson extends Module {

	/**
	 * @param Publication $publication
	 *
	 * @return string
	 */
	public function export(Publication $publication) {

		$fields = $this->createFields($publication);
		foreach ($fields as $key => $value) {
			if (empty($value)) {
				unset ($fields[$key]);
			}
		}

		//return json_encode(array($fields), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); // TODO: needs PHP>5.4
		return json_encode(array($fields));
	}


	/**
	 * @param Publication $publication
	 *
	 * @return array
	 * @throws Exception
	 */
	private function createFields(Publication $publication) {

		$fields = array();
		$fields['type'] = $this->encodeTypes($publication->getTypeName());
		$fields['id'] = 'TODO_'.$publication->getId(); // TODO
		$fields['title'] = $publication->getTitle();
		foreach ($publication->getAuthors() as $author) {
			if ($author->getFirstName() && $author->getLastName()) {
				$fields['author'][] = array('family' => $author->getLastName(),
											'given'  => $author->getFirstName());
			}
		}
		$fields['issued'] = array('date-parts' => array($publication->getDatePublished('Y'),
														$publication->getDatePublished('m'),
														$publication->getDatePublished('d')));

		$fields['container-title'] = $publication->getJournal(); // TODO
		$fields['container-title'] = $publication->getBooktitle();

		$fields['citation-number'] = $publication->getNumber();
		$fields['volume'] = $publication->getVolume();
		$fields['number'] = ''; // TODO
		$fields['issue'] = ''; // TODO
		$fields['page'] = '';
		$fields['page-first'] = $publication->getFirstPage();

		$fields['edition'] = $publication->getEdition();

		$fields['abstract'] = $publication->getAbstract();
		$fields['DOI'] = $publication->getDoi();
		$fields['ISBN'] = $publication->getIsbn();
		$fields['ISSN'] = ''; // TODO
		if ($file = $publication->getFullTextFile()) {
			$fields['URL'] = Request::createUrl(array('p' => 'publication', 'id' => $publication->getId(), 'file_id' => $file->getId()), true);
		}
		$fields['note'] = $publication->getNote();
		$fields['publisher'] = $publication->getPublisher();

		return $fields;
	}


	/**
	 * @param $type
	 *
	 * @return string
	 * @throws Exception
	 */
	private function encodeTypes($type) {

		switch ($type) {
			case 'article':
				return 'article-journal'; // TODO: check if valid
				break;
			case 'inproceedings':
				return 'paper-conference';
				break;
			case 'incollection':
			case 'inbook':
				return 'chapter';
				break;
			case 'book':
				return 'book';
				break;
			case 'masterthesis':
			case 'phdthesis':
				return 'thesis';
				break;
			case 'techreport':
				return 'report'; // TODO: check if valid
				break;
			case 'misc':
				return 'TODO'; // TODO: what to use?
				break;
			case 'unpublished':
				return 'manuscript'; // TODO: check if valid
				break;
			default:
				throw new Exception('unknown or missing publication type');
				break;
		}
	}
}
