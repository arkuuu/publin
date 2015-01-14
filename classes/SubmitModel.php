<?php

require_once 'Model.php';
require_once 'Publication.php';

class SubmitModel extends Model {

	private $fields = array(
			'type_id' => 'number',
			'study_field_id' => 'number',
			'date_published' => 'date',
			'title' => 'text',
			'journal' => 'text',
			'booktitle' => 'text',
			'publisher' => 'text',
			'edition' => 'text',
			'institution' => 'text',
			'howpublished' => 'text',
			'pages_from' => 'number',
			'pages_to' => 'number',
			'volume' => 'number',
			'number' => 'number',
			'series' => 'number',
			'authors' => 'array',
			'key_terms' => 'array',
			'abstract' => 'text',
			'academic_title' => 'text',
			'first_name' => 'text',
			'last_name' => 'text');

	private $required_fields = array(
			/* for all types */
			'all' => array('type_id', 'study_field_id', 'date_published', 'title', 'authors'),
			/* article */
			'1' => 	array('journal'),
			/* book */
			'2' =>	array('publisher'),
			/* incollection */
			'4' =>	array('booktitle'),
			/* inproceedings */
			'3' =>	array('booktitle'),
			/* masterthesis */
			'6' =>	array('institution'),
			/* misc */
			'9' =>	array('howpublished'),
			/* phdthesis */
			'7' =>	array('institution'),
			/* techreport */
			'5' =>	array('institution'),
			/* unpublished */
			'8' => array(),
			/* authors */
			'author' => array('first_name', 'last_name'));

	private $optional_fields = array(
			/* for all types */
			'all' => array('key_terms', 'abstract'),
			/* article */
			'1' => 	array('volume', 'number', 'pages_from', 'pages_to'),
			/* book */
			'2' =>	array('volume', 'series', 'edition'),
			/* incollection */
			'4' =>	array('publisher', 'pages_from', 'pages_to'),
			/* inproceedings */
			'3' =>	array('publisher', 'pages_from', 'pages_to'),
			/* masterthesis */
			'6' =>	array(),
			/* misc */
			'9' =>	array(),
			/* phdthesis */
			'7' =>	array(),
			/* techreport */
			'5' =>	array('number'),
			/* unpublished */
			'8' =>	array(),
			/* authors */
			'author' => array('academic_title'));

	private $errors = array();

	private $publication;



	public function __construct() {

		parent::__construct();
	}

	public function getPublication() {
		return $this -> publication;
	}


	private function validateInput($key, $value) {

		if (array_key_exists($key, $this -> fields)) {

			switch ($this -> fields[$key]) {

				case 'number':
					if ((is_numeric($value) && $value >= 0)) {
						$value = (int)$value;
					}
					else {
						return false;
					}
					break;
				
				case 'text':				
					if (is_string($value)) {						
						$value = trim($value);
						$value = stripslashes($value);
						$value = htmlspecialchars($value);

						if (empty($value)) {
							return false;
						}
					}
					else {
						return false;
					}
					break;

				case 'array':
					if (is_array($value)) {
						$value = array_filter($value);
					}
					else {
						return false;
					}
					break;

				case 'date':
					// TODO: test for date
					if (empty($value)) {
						return false;
					}
					break;

				default:
					// $this -> errors[] = 'ADMIN: no content type defined for '.$key;
					throw new Exception('no content type defined for '.$key);
					break;
			}
		}
		else {
			// $this -> errors[] = 'ADMIN: unknown field '.$key;
			throw new Exception('unknown field '.$key);
		}
		
		return $value;
	}


	// TODO: make private
	public function validatePublication(array $input) {

		$data = array();
		
		/* Checks for type_id which is crucial */
		if (!isset($input['type_id'])) {
			$this -> errors[] = 'type_id is required but missing';
			return false;
		}

		/* Checks required fields */
		foreach (array_merge(
						$this -> required_fields['all'],
						$this -> required_fields[$input['type_id']])
					as $field) {

			if (!empty($input[$field])) {
				$input[$field] = $this -> validateInput($field, $input[$field]);

				if ($input[$field]) {
					$data[$field] = $input[$field];
				}
				else {
					$this -> errors[] = 'bad input for '.$field;
				}
			}
			else {
				$this -> errors[] = $field.' is required but missing';
			}
		}

		/* Checks optional fields */
		foreach (array_merge(
						$this -> optional_fields['all'],
						$this -> optional_fields[$input['type_id']])
					as $field) {

			if (!empty($input[$field])) {
				$input[$field] = $this -> validateInput($field, $input[$field]);
				print_r($input[$field]);
				if ($input[$field] || is_array($input[$field])) {
					$data[$field] = $input[$field];
				}
				else {
					$this -> errors[] = 'bad input for '.$field;
				}
			}
		}

		return $data;
	}


	public function validateAuthor(array $input) {

		$data = array();

		foreach ($this -> required_fields['author'] as $field) {
			if (!empty($input[$field])) {
				$input[$field] = $this -> validateInput($field, $input[$field]);

				if ($input[$field]) {
					$data[$field] = $input[$field];
				}
				else {
					$this -> errors[] = 'bad input for '.$field;
				}
			}
			else {
				$this -> errors[] = $field.' is required but missing';
			}
		}

		foreach ($this -> optional_fields['author'] as $field) {
			if (!empty($input[$field])) {
				$input[$field] = $this -> validateInput($field, $input[$field]);

				if ($input[$field] || is_array($input[$field])) {
					$data[$field] = $input[$field];
				}
				else {
					$this -> errors[] = 'bad input for '.$field;
				}
			}
		}
		return $data;
	}


	public function rewriteArray(array $input) {

		$data = array();
		$given_fields = array_keys($input);

		foreach ($given_fields as $field) {
			foreach ($input[$field] as $key => $value) {
				$data[$key][$field] = $value;
			}
		}
		return $data;
	}


	public function getErrors() {
		return $this -> errors;
	}


	public function createPublicationFromSubmit(array $data) {

		$authors = array();
		$key_terms = array();

		$data = $this -> validatePublication($data);


		if (isset($data['authors'])) {
			$authors = $this -> createAuthorsFromSubmit($data['authors']);
			unset($data['authors']);
		}
		if (isset($data['key_terms'])) {
			$key_terms = $data['key_terms'];
			unset($data['key_terms']);
		}

		if (empty($this -> errors)) {
			$publication = new Publication($data);
			$publication -> setAuthors($authors);
			$publication -> setKeyTerms($key_terms);

			$this -> publication = $publication;
			return $publication;
		}
		else {
			return false;
		}
	}


	public function createAuthorsFromSubmit(array $data) {
		$data = $this -> rewriteArray($data);

		if (!empty($data)) {
			foreach ($data as $author) {
				$author = $this -> validateAuthor($author);
				if ($author) {
					$authors[] = new Author($author);
				}
				else {
					return false;
				}
			}
			return $authors;
		}
		else {
			$this -> errors[] = 'empty or damaged array for authors';
			return false;
		}

	}


	public function storePublication(Publication $publication) {

		$authors = $publication -> getAuthors();
		$key_terms = $publication -> getKeyTerms();

		$author_ids = array();
		foreach ($authors as $author) {
			$id = $this -> storeAuthor($author);
			if ($id !== false) {
				$author_ids[] = $id;
			}
		}

		$key_term_ids = array();
		// foreach ($key_terms as $key_term) {
		// 	$id = $this -> storeKeyTerm($key_term);
		// 	if ($id !== false) {
		// 		$key_term_ids[] = $id;
		// 	}
		// }

		$data = $publication -> getData();

		$publication_id = $this -> db -> insertData('list_publications', $data);

		if (!empty($publication_id)) {

			if (!empty($author_ids)) {
				$prio = 1; // TODO: really start with 1 and go up?
				foreach ($author_ids as $author_id) {
					$data = array('publication_id' => $publication_id,
								'author_id' => $author_id, 'priority' => $prio);
					$this -> db -> insertData('rel_publ_to_authors', $data);
					$prio++;
				}
			}
			else {
				$this -> errors[] = 'author_ids was empty';
				return false;
			}

			if (!empty($key_term_ids)) {
				foreach ($key_term_ids as $key_term_id) {
					$data = array('publication_id' => $publication_id,
								'key_term_id' => $key_term_id);
					$this -> db -> insertData('rel_publ_to_key_terms', $data);
				}
			}
		}
		else {
			$this -> errors[] = 'publication_id was empty';
			return false;
		}
	}

	public function storeAuthor(Author $author) {

		$data = $author -> getData();

		$author_id = $this -> db -> insertData('list_authors', $data);

		if (!empty($author_id)) {
			return $author_id;
		}
		else {
			return false;
		}
	}

}
