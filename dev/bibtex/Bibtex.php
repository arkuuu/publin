<?php

class Bibtex {

	/**
	 * TODO: comment
	 *
	 * @param	array	$data	description
	 *
	 * @return	string
	 */
	public static function export($data) {

		/* Map your fields here. */
		$fields = array('author', 'title', 'journal', 'booktitle', 'publisher', 'edition', 'institution', 'howpublished', 'year', 'month', 'volume', 'pages', 'number', 'series', 'abstract', 'bibsource', 'copyright', 'url', 'doi', 'address', 'date-added', 'date-modified', 'keywords');

		/* Gets the BibTeX type or returns false if there is no type given */
		if (isset($data['type']) && !empty($data['type'])) {

			$type = $data['type'];
			unset($data['type']);
		}
		else {
			return false;
		}

		/* Gets the cite key or generates one if there is none given */
		if (isset($data['cite_key']) && !empty($data['cite_key'])) {
			$cite_key = $data['cite_key'];
			unset($data['cite_key']);
		}
		else {
			// TODO: generate cite key
			$cite_key = self::generateCiteKey($data);
		}

		/* Composes the authors string */
		if (isset($data['author'])) {
			$string = '';
			foreach ($data['author'] as $author) {
				$string .= $author['given'].' '.$author['family'].' and ';
			}
			$string = substr($string, 0, -5);
			$data['author'] = $string;
		}

		/* Composes the keywords string */
		if (isset($data['keywords'])) {
			$string = '';
			foreach ($data['keywords'] as $keywords) {
				$string .= $keywords.', ';
			}
			$string = substr($string, 0, -2);
			$data['keywords'] = $string;
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
	 * TODO: comment
	 *
	 * @param	string	$bibtex	description
	 *
	 * @return	array
	 */
	public static function import($bibtex) {

		/* Map your fields here. */
		$fields = array('author', 'title', 'journal', 'booktitle', 'publisher', 'edition', 'institution', 'howpublished', 'year', 'month', 'volume', 'pages', 'number', 'series', 'abstract', 'bibsource', 'copyright', 'url', 'doi', 'address', 'date-added', 'date-modified', 'keywords');

		$result = array();

		$bibtex = stripslashes($bibtex);

		/* Gets rid of some special symbols. This needs to be extended further */
		$bibtex = str_replace(array('\"u', '\"{u}', '{\"u}'), 'ü', $bibtex);
		$bibtex = str_replace(array('\"a', '\"{a}', '{\"a}'), 'ä', $bibtex);
		$bibtex = str_replace(array('\"o', '\"{o}', '{\"o}'), 'ö', $bibtex);
		$bibtex = str_replace(array('\"U', '\"{U}', '{\"U}'), 'Ü', $bibtex);
		$bibtex = str_replace(array('\"A', '\"{A}', '{\"A}'), 'Ä', $bibtex);
		$bibtex = str_replace(array('\"O', '\"{O}', '{\"O}'), 'Ö', $bibtex);

		$bibtex = str_replace('\c{c}', 'ç', $bibtex);
		$bibtex = str_replace('\c{C}', 'Ç', $bibtex);

		/* Gets the entry type and the cite key */
		preg_match('/\@(article|book|incollection|inproceedings|masterthesis|misc|phdthesis|techreport|unpublished|inbook)\s?(\"|\{)([^\"\},]*),/i', $bibtex, $typeReg);
		$result['type'] = strtolower($typeReg[1]);
		$result['cite_key'] = $typeReg[3];

		/* Gets all other fields */
		foreach ($fields as $field) {
			$regex = '/\b'.$field.'\b\s*=\s*(.*)/i';
			preg_match($regex, $bibtex, $reg_result);

			if (!empty($reg_result[1])) {
				$value = self::stripField($reg_result[1]);

				if (!empty($value)) {

					/* Extracts the authors with their given and family names */
					if ($field == 'author') {
						$result[$field] = self::extractAuthors($value);
					}
					/* Extracts the keywords */
					else if ($field == 'keywords') {
						$result[$field] = self::extractKeywords($value);
					}
					else {
						$result[$field] = $value;					
					}
				}
			}
		}

		return $result;
	}


	/**
	 * TODO: comment
	 *
	 * @param	string	$string	description
	 *
	 * @return	string
	 */
	private static function stripField($string) {

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
	 * @param	string	$string	description
	 *
	 * @return	array
	 */
	private static function extractAuthors($string) {

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
	 * TODO: comment
	 *
	 * @param	string	$string	description
	 *
	 * @return	array
	 */
	private static function extractKeywords($string) {

		$keywords = array();
		$strings = explode(',', $string);

		foreach ($strings as $string) {
			$string = trim($string);
			if (!empty($string)) {
				$keywords[] = $string;
			}
		}

		return $keywords;
	}


	/**
	 * TODO: comment
	 *
	 * @param	array	$data	description
	 *
	 * @return	string
	 */
	private static function generateCiteKey($data) {

		return 'todo';
	}

}
