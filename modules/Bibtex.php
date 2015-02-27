<?php

namespace publin\modules;

use Exception;
use publin\src\Publication;

/**
 * Class Bibtex
 */
class Bibtex {

	/**
	 * @var array
	 */
	private $fields;
	/**
	 * @var array
	 */
	private $author_fields;
	/**
	 * @var array
	 */
	private $pages_fields;

	/**
	 * @var string
	 */
	private $bibsource;
	/**
	 * @var string
	 */
	private $url;


	/**
	 *
	 */
	public function __construct() {

		$this->bibsource = 'publin';
		$this->url = 'http://localhost:8888/publin/?p=publication&amp;id=';

		/* Map your fields here. You can change the order or leave out fields. */
		$this->fields = array(
			/* bibtex field => your field */
			'type'         => 'type',
			'cite_key'     => 'cite_key',
			'author'       => 'authors',
			'title'        => 'title',
			'journal'      => 'journal',
			'booktitle'    => 'booktitle',
			'publisher'    => 'publisher',
			'edition'      => 'edition',
			'institution'  => 'institution',
			'school'       => 'school',
			'howpublished' => 'howpublished',
			'year'         => 'year',
			'month'        => 'month',
			'volume'       => 'volume',
			'pages'        => 'pages',
			'number'       => 'number',
			'series'       => 'series',
			'abstract'     => 'abstract',
			'bibsource'    => 'bibsource',
			'copyright'    => 'copyright',
			'url'          => 'url',
			'doi'          => 'doi',
			'address'      => 'address',
			'note'         => 'note',
			'keywords'     => 'keywords',
		);

		$this->author_fields = array(
			/* parser field => your field */
			'given'  => 'given',
			'family' => 'family',
		);

		$this->pages_fields = array(
			/* parser field => your field */
			'from' => 'from',
			'to'   => 'to',
		);
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

		$authors = '';
		foreach ($publication->getAuthors() as $author) {
			if ($author->getFirstName() && $author->getLastName()) {
				$authors .= $author->getFirstName().' '.$author->getLastName().' and ';
			}
		}
		$authors = substr($authors, 0, -5);

		$keywords = '';
		foreach ($publication->getKeywords() as $keyword) {
			if ($keyword->getName()) {
				$keywords .= $keyword->getName().', ';
			}
		}
		$keywords = substr($keywords, 0, -2);

		$fields = array();
		$fields[] = array('author', $authors);
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
		$fields[] = array('doi', $publication->getDoi());
		$fields[] = array('address', $publication->getAddress());
		$fields[] = array('howpublished', $publication->getHowpublished());
		$fields[] = array('note', $publication->getNote());
		$fields[] = array('abstract', $publication->getAbstract());
		$fields[] = array('bibsource', $this->bibsource);
		$fields[] = array('biburl', $this->url.$publication->getId());
		$fields[] = array('keywords', $keywords);

		$result = '@'.$publication->getTypeName().'{'.$this->generateCiteKey($publication);
		foreach ($fields as $field) {
			if ($field[1]) {
				$result .= ','."\n\t".$field[0].' = {'.$this->encodeSpecialChars($field[1]).'}';
			}
		}
		$result .= "\n".'}';

		return $result;
	}


	/**
	 * @param Publication $publication
	 *
	 * @return string
	 */
	private function generateCiteKey(Publication $publication) {

		// TODO implement

		return 'cite_key_'.$publication->getId();
	}



//	public function exportOld(Publication $publication) {
//
//		$input = $publication->getData();
//
//		/* Maps your fields to the BibTeX fields. */
//		foreach ($this->fields as $bibtex_field => $your_field) {
//			if (isset($input[$your_field])) {
//				$data[$bibtex_field] = $input[$your_field];
//			}
//		}
//
//		/* Gets the BibTeX type or returns false if there is no type given */
//		if (!empty($data['type'])) {
//			$type = $data['type'];
//			unset($data['type']);
//		}
//		else {
//			return false;
//		}
//
//		/* Gets the cite key or generates one if there is none given */
//		if (!empty($data['cite_key'])) {
//			$cite_key = $data['cite_key'];
//			unset($data['cite_key']);
//		}
//		else {
//			$cite_key = self::generateCiteKey($data);
//		}
//
//		/* Composes the authors string */
//		if (isset($data['author']) && is_array($data['author'])) {
//			$string = '';
//			$given = $this->author_fields['given'];
//			$family = $this->author_fields['family'];
//
//			foreach ($data['author'] as $author) {
//				if (!empty($author[$given]) && !empty($author[$family])) {
//					$string .= $author[$given].' '.$author[$family].' and ';
//				}
//			}
//			$string = substr($string, 0, -5);
//			$data['author'] = $string;
//		}
//
//		/* Composes the keywords string */
//		if (isset($data['keywords']) && is_array($data['keywords'])) {
//			$string = '';
//			foreach ($data['keywords'] as $keyword) {
//				$string .= $keyword.', ';
//			}
//			$string = substr($string, 0, -2);
//			$data['keywords'] = $string;
//		}
//
//		/* Composes the pages string */
//		if (isset($data['pages']) && is_array($data['pages'])) {
//			$string = '';
//			$from = $this->pages_fields['from'];
//			$to = $this->pages_fields['to'];
//
//			if (!empty($data['pages'][$from]) && !empty($data['pages'][$to])) {
//				$string = $data['pages'][$from].'--'.$data['pages'][$to];
//			}
//			$data['pages'] = $string;
//		}
//
//		/* Composes the BibTeX entry */
//		$result = '@'.$type.'{'.$cite_key;
//		foreach ($data as $key => $value) {
//			if (!empty($value)) {
//				$result .= ','."\n\t".$key.' = {'.$value.'}';
//			}
//		}
//		$result .= "\n".'}';
//
//		return $result;
//	}

	/**
	 * @param $string
	 *
	 * @return mixed
	 */
	private function encodeSpecialChars($string) {

		$string = str_replace('ü', '\"{u}', $string);
		$string = str_replace('ä', '\"{a}', $string);
		$string = str_replace('ö', '\"{o}', $string);
		$string = str_replace('Ü', '\"{U}', $string);
		$string = str_replace('Ä', '\"{A}', $string);
		$string = str_replace('Ö', '\"{O}', $string);

		$string = str_replace('ç', '\c{c}', $string);
		$string = str_replace('Ç', '\c{C}', $string);

		// TODO continue

		return $string;
	}


	/**
	 * @param $input
	 *
	 * @return array
	 */
	public function import($input) {

		$result = array();

		$input = stripslashes($input);
		$input = $this->decodeSpecialChars($input);

		/* Gets the entry type and the cite key */
		preg_match('/\@(article|book|incollection|inproceedings|masterthesis|misc|phdthesis|techreport|unpublished|inbook)\s?(\"|\{)([^\"\},]*),/i', $input, $typeReg);

		if (!empty($typeReg[1])) {
			$result['type'] = strtolower($typeReg[1]);
		}
		else {
			// TODO: throw exception?
			return false;
		}
		if (!empty($typeReg[3])) {
			$result['cite_key'] = $typeReg[3];
		}

		/* Gets all other fields */
		foreach ($this->fields as $bibtex_field => $your_field) {
			$regex = '/\b'.$bibtex_field.'\b\s*=\s*(.*)/i';
			preg_match($regex, $input, $reg_result);

			if (!empty($reg_result[1])) {
				$value = self::stripField($reg_result[1]);

				if ($value) {

					/* Extracts the authors with their given and family names */
					if ($bibtex_field == 'author') {
						$author = self::extractAuthors($value);

						if ($author) {
							$result[$your_field] = $author;
						}
					}
					/* Extracts the single keywords */
					else if ($bibtex_field == 'keywords') {
						$keywords = self::extractKeywords($value);

						if ($keywords) {
							$result[$your_field] = $keywords;
						}
					}
					/* Extracts the pages into from and to */
					else if ($bibtex_field == 'pages') {
						$pages = self::extractPages($value);

						if ($pages) {
							$result[$your_field] = $pages;
						}
					}
					/* The rest */
					else {
						$result[$your_field] = $value;
					}
				}
			}
		}

		return $result;
	}


	/**
	 * @param $string
	 *
	 * @return mixed
	 */
	private function decodeSpecialChars($string) {

		$string = str_replace(array('\"u', '\"{u}', '{\"u}'), 'ü', $string);
		$string = str_replace(array('\"a', '\"{a}', '{\"a}'), 'ä', $string);
		$string = str_replace(array('\"o', '\"{o}', '{\"o}'), 'ö', $string);
		$string = str_replace(array('\"U', '\"{U}', '{\"U}'), 'Ü', $string);
		$string = str_replace(array('\"A', '\"{A}', '{\"A}'), 'Ä', $string);
		$string = str_replace(array('\"O', '\"{O}', '{\"O}'), 'Ö', $string);

		$string = str_replace('\c{c}', 'ç', $string);
		$string = str_replace('\c{C}', 'Ç', $string);

		// TODO continue

		return $string;
	}


	/**
	 * TODO: comment
	 *
	 * @param    string $string description
	 *
	 * @return    string
	 */
	private function stripField($string) {

		/* Determines the position of {} and "" which are used as delimiter */
		$first_brace_pos = stripos($string, '{');
		$first_other_pos = stripos($string, '"');
		$last_brace_pos = strrpos($string, '}');
		$last_other_pos = strrpos($string, '"');

		/* Determines which delimiter is used */
		if ($first_brace_pos !== false && $first_other_pos !== false) {
			if ($first_brace_pos < $first_other_pos + 1) {
				$first_pos = $first_brace_pos;
				$last_pos = $last_brace_pos;
			}
			else {
				$first_pos = $first_other_pos;
				$last_pos = $last_other_pos;
			}
		}
		else if ($first_brace_pos !== false) {
			$first_pos = $first_brace_pos;
			$last_pos = $last_brace_pos;
		}
		else if ($first_other_pos !== false) {
			$first_pos = $first_other_pos;
			$last_pos = $last_other_pos;
		}
		else {
			return false;
		}

		/* Cuts out the content between the delimiters */
		$string = substr($string, $first_pos + 1, ($last_pos - $first_pos - 1));

		/* Gets rid of {} and spaces in and around the content */
		$string = str_replace(array('{', '}'), '', $string);
		$string = trim($string);

		return $string;
	}


	/**
	 * @param    string $string description
	 *
	 * @return    array
	 */
	private function extractAuthors($string) {

		$authors = array();
		$strings = explode(' and ', $string);

		foreach ($strings as $string) {

			if (substr_count($string, ',') == 1) {
				$names = explode(',', $string);
				$given = $names[1];
				$family = $names[0];
			}
			else if (substr_count($string, ' ') == 1) {
				$names = explode(' ', $string);
				$given = $names[0];
				$family = $names[1];
			}
			else if (substr_count($string, ' ') > 1) {
				$pos = strrpos($string, ' ');
				$given = substr($string, 0, $pos);
				$family = substr($string, $pos);
			}
			else {
				$given = '';
				$family = '';
			}

			$author[$this->author_fields['given']] = trim($given);
			$author[$this->author_fields['family']] = trim($family);

			$authors[] = $author;
		}

		return $authors;
	}


	/**
	 * @param    string $string description
	 *
	 * @return    array
	 */
	private function extractKeywords($string) {

		$keywords = array();
		$strings = explode(',', $string);

		foreach ($strings as $string) {
			$string = trim($string);

			if ($string) {
				$keywords[] = $string;
			}
		}

		return $keywords;
	}


	/**
	 * @param    string $string description
	 *
	 * @return    array
	 */
	private function extractPages($string) {

		$pages = array();
		$strings = explode('--', $string);

		if (count($strings) == 2) {
			$pages[$this->pages_fields['from']] = trim($strings[0]);
			$pages[$this->pages_fields['to']] = trim($strings[1]);
		}

		return $pages;
	}
}
