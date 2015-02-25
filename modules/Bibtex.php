<?php
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

	private $bibsource;
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
			'type'          => 'type',
			'cite_key'      => 'cite_key',
			'author'        => 'authors',
			'title'         => 'title',
			'journal'       => 'journal',
			'booktitle'     => 'booktitle',
			'publisher'     => 'publisher',
			'edition'       => 'edition',
			'institution'   => 'institution',
			'school'        => 'school',
			'howpublished'  => 'howpublished',
			'year'          => 'year',
			'month'         => 'month',
			'volume'        => 'volume',
			'pages'         => 'pages',
			'number'        => 'number',
			'series'        => 'series',
			'abstract'      => 'abstract',
			'bibsource'     => 'bibsource',
			'copyright'     => 'copyright',
			'url'           => 'url',
			'doi'           => 'doi',
			'address'       => 'address',
			'note'          => 'note',
			'keywords' => 'keywords',
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


	public function export(Publication $publication) {

		// TODO: encode bibtex characters?
		$result = '@'.$publication->getTypeName().'{'.$this->generateCiteKey($publication).','."\n\t";

		$authors = '';
		foreach ($publication->getAuthors() as $author) {
			if ($author->getFirstName() && $author->getLastName()) {
				$authors .= $author->getFirstName().' '.$author->getLastName().' and ';
			}
		}
		if (!empty($authors)) {
			$authors = substr($authors, 0, -5);
			$result .= 'author = {'.$authors.'}'."\n\t";;
		}

		if ($publication->getTitle()) {
			$result .= 'title = {'.$publication->getTitle().'}'."\n\t";
		}
		if ($publication->getJournalName()) {
			$result .= 'journal = {'.$publication->getJournalName().'}'."\n\t";
		}
		if ($publication->getBookName()) {
			$result .= 'booktitle = {'.$publication->getBookName().'}'."\n\t";
		}
		if ($publication->getVolume()) {
			$result .= 'volume = {'.$publication->getVolume().'}'."\n\t";
		}
		if ($publication->getNumber()) {
			$result .= 'number = {'.$publication->getNumber().'}'."\n\t";
		}
		if ($publication->getSeries()) {
			$result .= 'series = {'.$publication->getSeries().'}'."\n\t";
		}
		if ($publication->getEdition()) {
			$result .= 'edition = {'.$publication->getEdition().'}'."\n\t";
		}
		if ($publication->getPages('--')) {
			$result .= 'pages = {'.$publication->getPages('--').'}'."\n\t";
		}
		if ($publication->getDatePublished('Y-m-d')) {
			$result .= 'month = {'.$publication->getDatePublished('F').'}'."\n\t";
			$result .= 'year = {'.$publication->getDatePublished('Y').'}'."\n\t";
		}
		if (false) {
			// TODO: link to pdf
			$result .= '<meta name="DC.identifier" content="'.false.'" />'."\n\t";
		}
		if (false) {
			$result .= '<meta name="citation_issn" content="'.false.'" />'."\n\t";
		}
		if (false) {
			$result .= '<meta name="citation_isbn" content="'.false.'" />'."\n\t";
		}
		if ($publication->getInstitution()) {
			$result .= 'institution = {'.$publication->getInstitution().'}'."\n\t";
		}
		if ($publication->getSchool()) {
			$result .= 'school = {'.$publication->getSchool().'}'."\n\t";
		}
		if ($publication->getPublisherName()) {
			$result .= 'publisher = {'.$publication->getPublisherName().'}'."\n\t";
		}
//		if ($publication->getDoi()) {
//			$result .= 'url = {'.$publication->getDoi().'}'."\n\t";
//		}
		if ($publication->getAddress()) {
			$result .= 'address = {'.$publication->getAddress().'}'."\n\t";
		}
		if ($publication->getHowpublished()) {
			$result .= 'howpublished = {'.$publication->getHowpublished().'}'."\n\t";
		}
		if ($publication->getNote()) {
			$result .= 'note = {'.$publication->getNote().'}'."\n\t";
		}
		if ($publication->getAbstract()) {
			$result .= 'abstract = {'.$publication->getAbstract().'}'."\n\t";
		}

		$keywords = '';
		foreach ($publication->getKeywords() as $keyword) {
			$keywords .= $keyword->getName().', ';
		}
		if (!empty($keywords)) {
			$keywords = substr($keywords, 0, -2);
			$result .= 'keywords = {'.$keywords.'}'."\n\t";;
		}

		$result .= 'bibsource = {'.$this->bibsource.'}'."\n\t";
		$result .= 'biburl = {'.$this->url.$publication->getId().'}'."\n";
		$result .= '}';

		return $result;
	}


	/**
	 * @param Publication $publication
	 *
	 * @return string
	 */
	private function generateCiteKey(Publication $publication) {

		// TODO: implement
		return 'cite_key_'.$publication->getId();
	}


	/**
	 * @param Publication $publication
	 *
	 * @return string
	 */
	public function exportOld(Publication $publication) {

		// TODO: work with publication and not with array
		$input = $publication->getData();

		/* Maps your fields to the BibTeX fields. */
		foreach ($this->fields as $bibtex_field => $your_field) {
			if (isset($input[$your_field])) {
				$data[$bibtex_field] = $input[$your_field];
			}
		}

		/* Gets the BibTeX type or returns false if there is no type given */
		if (!empty($data['type'])) {
			$type = $data['type'];
			unset($data['type']);
		}
		else {
			// TODO: throw exception?
			return false;
		}

		/* Gets the cite key or generates one if there is none given */
		if (!empty($data['cite_key'])) {
			$cite_key = $data['cite_key'];
			unset($data['cite_key']);
		}
		else {
			$cite_key = self::generateCiteKey($data);
		}

		/* Composes the authors string */
		if (isset($data['author']) && is_array($data['author'])) {
			$string = '';
			$given = $this->author_fields['given'];
			$family = $this->author_fields['family'];

			foreach ($data['author'] as $author) {
				if (!empty($author[$given]) && !empty($author[$family])) {
					$string .= $author[$given].' '.$author[$family].' and ';
				}
			}
			$string = substr($string, 0, -5);
			$data['author'] = $string;
		}

		/* Composes the keywords string */
		if (isset($data['keywords']) && is_array($data['keywords'])) {
			$string = '';
			foreach ($data['keywords'] as $keyword) {
				$string .= $keyword.', ';
			}
			$string = substr($string, 0, -2);
			$data['keywords'] = $string;
		}

		/* Composes the pages string */
		if (isset($data['pages']) && is_array($data['pages'])) {
			$string = '';
			$from = $this->pages_fields['from'];
			$to = $this->pages_fields['to'];

			if (!empty($data['pages'][$from]) && !empty($data['pages'][$to])) {
				$string = $data['pages'][$from].'--'.$data['pages'][$to];
			}
			$data['pages'] = $string;
		}

		/* Composes the BibTeX entry */
		$result = '@'.$type.'{'.$cite_key;
		foreach ($data as $key => $value) {
			if (!empty($value)) {
				$result .= ','."\n\t".$key.' = {'.$value.'}';
			}
		}
		$result .= "\n".'}';

		return $result;
	}


	/**
	 * @param $input
	 *
	 * @return array
	 */
	public function import($input) {

		$result = array();

		$input = stripslashes($input);

		/* Gets rid of some special symbols. This needs to be extended further */
		$input = str_replace(array('\"u', '\"{u}', '{\"u}'), 'ü', $input);
		$input = str_replace(array('\"a', '\"{a}', '{\"a}'), 'ä', $input);
		$input = str_replace(array('\"o', '\"{o}', '{\"o}'), 'ö', $input);
		$input = str_replace(array('\"U', '\"{U}', '{\"U}'), 'Ü', $input);
		$input = str_replace(array('\"A', '\"{A}', '{\"A}'), 'Ä', $input);
		$input = str_replace(array('\"O', '\"{O}', '{\"O}'), 'Ö', $input);

		$input = str_replace('\c{c}', 'ç', $input);
		$input = str_replace('\c{C}', 'Ç', $input);

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
	 * TODO: comment
	 *
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
	 * TODO: comment
	 *
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
	 * TODO: comment
	 *
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
