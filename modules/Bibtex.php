<?php

namespace publin\modules;

use Exception;
use publin\src\Publication;
use publin\src\Request;

/**
 * Class Bibtex
 */
class Bibtex extends Module {

	/**
	 * @var array
	 */
	private $fields;
	/**
	 * @var string
	 */
	private $bibsource;



	/**
	 *
	 */
	public function __construct() {

		//$this->bibsource = 'publin';

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
			'isbn'     => 'isbn',
			'address'      => 'address',
			'location' => 'location',
			'note'         => 'note',
			'keywords'     => 'keywords',
		);
	}


	/**
	 * @param Publication[] $publications
	 *
	 * @return string
	 * @throws Exception
	 */
	public function exportMultiple(array $publications) {

		$result = '';
		foreach ($publications as $publication) {
			if ($publication instanceof Publication) {
				$result .= $this->export($publication)."\n\n";
			}
		}

		return $result;
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
		$fields[] = array('journal', $publication->getJournal());
		$fields[] = array('volume', $publication->getVolume());
		$fields[] = array('number', $publication->getNumber());
		$fields[] = array('booktitle', $publication->getBooktitle());
		$fields[] = array('series', $publication->getSeries());
		$fields[] = array('edition', $publication->getEdition());
		$fields[] = array('pages', $publication->getPages('--'));
		$fields[] = array('note', $publication->getNote());
		$fields[] = array('location', $publication->getLocation());
		$fields[] = array('month', $publication->getDatePublished('F'));
		$fields[] = array('year', $publication->getDatePublished('Y'));
		//$fields[] = array('url', false); // TODO: link to pdf
		//$fields[] = array('issn', false);
		$fields[] = array('publisher', $publication->getPublisher());
		$fields[] = array('institution', $publication->getInstitution());
		$fields[] = array('school', $publication->getSchool());
		$fields[] = array('address', $publication->getAddress());
		$fields[] = array('howpublished', $publication->getHowpublished());
		$fields[] = array('copyright', $publication->getCopyright());
		$fields[] = array('doi', $publication->getDoi());
		$fields[] = array('isbn', $publication->getIsbn());
		$fields[] = array('abstract', $publication->getAbstract());
		if ($file = $publication->getFullTextFile()) {
			$fields[] = array('url', Request::createUrl(array('p' => 'publication', 'id' => $publication->getId(), 'file_id' => $file->getId())));
		}
		//$fields[] = array('bibsource', $this->bibsource);
		$fields[] = array('biburl', Request::createUrl(array('p' => 'publication', 'id' => $publication->getId())));
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


	/**
	 * @param $string
	 *
	 * @return mixed
	 */
	private function encodeSpecialChars($string) {

		$string = str_replace('ü', '{\"u}', $string);
		$string = str_replace('ä', '{\"a}', $string);
		$string = str_replace('ö', '{\"o}', $string);
		$string = str_replace('Ü', '{\"U}', $string);
		$string = str_replace('Ä', '{\"A}', $string);
		$string = str_replace('Ö', '{\"O}', $string);

		$string = str_replace('ç', '{\c c}', $string);
		$string = str_replace('Ç', '{\c C}', $string);
		$string = str_replace('ú', '{\'u}', $string);
		$string = str_replace('ñ', '{\~n}', $string);

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

		$input = $this->decodeSpecialChars($input);

		/* Gets the entry type and the cite key */
		preg_match('/\@(article|book|incollection|inproceedings|masterthesis|misc|phdthesis|techreport|unpublished|inbook)\s?(\"|\{)([^\"\},]*),/i', $input, $typeReg);

		if (!empty($typeReg[1])) {
			$result['type'] = strtolower($typeReg[1]);
		}
		else {
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
							$result['pages_from'] = $pages[0];
							$result['pages_to'] = $pages[1];
						}
					}
					/* The rest */
					else {
						$result[$your_field] = $value;
					}
				}
			}
		}
		if (!empty($result[$this->fields['year']]) && !empty($result[$this->fields['month']])) {
			$result['date_published'] = self::extractDate($result[$this->fields['year']], $result[$this->fields['month']]);
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

		$string = str_replace(array('\c{c}', '{\c c}'), 'ç', $string);
		$string = str_replace(array('\c{C}', '{\c C}'), 'ç', $string);
		$string = str_replace(array('\'{u}', '{\' u}', '{\'u}'), 'ú', $string);
		$string = str_replace(array('\~{n}', '{\~ n}', '{\~u}'), 'ñ', $string);

		// TODO continue

		return $string;
	}


	/**
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

			$author['given'] = trim($given);
			$author['family'] = trim($family);

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
			$pages[0] = trim($strings[0]);
			$pages[1] = trim($strings[1]);
		}

		return $pages;
	}


	/**
	 * @param $input_year
	 * @param $input_month
	 *
	 * @return bool|string
	 */
	private function extractDate($input_year, $input_month) {

		$input_month = explode(' ', $input_month);
		$month = $input_month[0];
		if (isset($input_month[1])) {
			$day = $input_month[1];
		}
		else {
			$day = '01';
		}
		$date = strtotime($day.' '.$month.' '.$input_year);

		if ($date) {
			return date('Y-m-d', $date);
		}
		else {
			return false;
		}
	}
}
