<?php

require_once 'Model.php';
require_once 'Publication.php';
require_once 'Author.php';
require_once 'Journal.php';
require_once 'Publisher.php';
require_once 'KeyTerm.php';

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
			'last_name' => 'text',
			'name' => 'text');

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

	private $matches = array();

	private $publication;



	public function __construct() {

		parent::__construct();
	}

	public function getPublication() {
		return $this -> publication;
	}

	public function getErrors() {
		return $this -> errors;
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


	private function validatePublication(array $input) {

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


	private function validateAuthor(array $input) {

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








	public function createPublicationFromSubmit(array $data) {

		$authors = array();
		$key_terms = array();

		$data = $this -> validatePublication($data);


		if (isset($data['authors'])) {
			$authors = $this -> createAuthorsFromSubmit($data['authors']);
			unset($data['authors']);
		}
		if (isset($data['key_terms'])) {
			$key_terms = $this -> createKeyTermsFromSubmit($data['key_terms']);
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

	private function rewriteArray(array $input) {

		$data = array();
		$given_fields = array_keys($input);

		foreach ($given_fields as $field) {
			foreach ($input[$field] as $key => $value) {
				$data[$key][$field] = $value;
			}
		}
		return $data;
	}
	

	public function createKeyTermsFromSubmit(array $data) {

		if (!empty($data)) {
			foreach ($data as $key_term) {
				$key_term = $this -> validateInput('name', $key_term);
				if ($key_term) {
					$key_terms[] = new KeyTerm(array('name' => $key_term));
				}
				else {
					$this -> errors[] = 'bad input for key term';
					return false;
				}
			}
			return $key_terms;
		}
		else {
			$this -> errors[] = 'empty or damaged array for key terms';
			return false;
		}
	}


	public function storePublication(Publication $publication) {

		$data = $publication -> getData();
		$authors = $publication -> getAuthors();
		$key_terms = $publication -> getKeyTerms();

		$author_ids = array();
		foreach ($authors as $author) {
			$author_ids[] = $this -> storeAuthor($author);
		}

		$key_term_ids = array();
		foreach ($key_terms as $key_term) {
			$key_term_ids[] = $this -> storeKeyTerm($key_term);
		}

		if (!empty($data['journal'])) {
			$data['journal_id'] = $this -> storeJournal(new Journal(array('name' => $data['journal'])));
			unset($data['journal']);
		}
		if (!empty($data['publisher'])) {
			$data['publisher_id'] = $this -> storePublisher(new Publisher(array('name' => $data['publisher'])));
			unset($data['publisher']);
		}


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
				throw new Exception('Error while inserting publication to DB, no authors given');

			}

			if (!empty($key_term_ids)) {
				foreach ($key_term_ids as $key_term_id) {
					$data = array('publication_id' => $publication_id,
								'key_term_id' => $key_term_id);
					$this -> db -> insertData('rel_publ_to_key_terms', $data);
				}
			}

			return $publication_id;
		}
		else {
			throw new Exception('Error while inserting publication to DB');
			
		}
	}

	public function storeAuthor(Author $author) {

		$data = $author -> getData();

		$author_id = $this -> db -> insertData('list_authors', $data);

		if (!empty($author_id)) {
			return $author_id;
		}
		else {
			throw new Exception('Error while inserting author to DB');
		}
	}

	public function storeKeyTerm(KeyTerm $key_term) {

		$data = $key_term -> getData();
		$key_term_id = $this -> db -> insertData('list_key_terms', $data);

		if (!empty($key_term_id)) {
			return $key_term_id;
		}
		else {
			throw new Exception('Error while inserting key term to DB');
			
		}
	}

	public function storeJournal(Journal $journal) {

		$data = $journal -> getData();
		$journal_id = $this -> db -> insertData('list_journals', $data);

		if (!empty($journal_id)) {
			return $journal_id;
		}
		else {
			throw new Exception('Error while inserting journal to DB');
			
		}
	}

	public function storePublisher(Publisher $publisher) {

		$data = $publisher -> getData();
		$publisher_id = $this -> db -> insertData('list_publishers', $data);

		if (!empty($publisher_id)) {
			return $publisher_id;
		}
		else {
			throw new Exception('Error while inserting publisher to DB');
			
		}
	}

}
