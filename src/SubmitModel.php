<?php

require_once 'FormatHandler.php';

class SubmitModel extends Model {
	
	private $required_fields = array(
			'all' => array('type', 'study_field', 'date_published', 'title', 'authors'),
			'article' => array('journal'),
			'book' => array('publisher'),
			'incollection' => array('booktitle'),
			'inproceedings' => array('booktitle'),
			'masterthesis' => array('institution'),
			'misc' => array('howpublished'),
			'phdthesis' => array('institution'),
			'techreport' =>	array('institution'),
			'unpublished' => array(),);

	private $optional_fields = array(
			'all' => array('key_terms', 'abstract'),
			'article' => array('publisher', 'volume', 'number', 'pages'),
			'book' => array('volume', 'series', 'edition'),
			'incollection' => array('publisher', 'pages'),
			'inproceedings' => array('publisher', 'pages'),
			'masterthesis' => array(),
			'misc' => array(),
			'phdthesis' => array(),
			'techreport' =>	array('number'),
			'unpublished' => array(),);

	private $errors = array();
	private $publication;



	public function __construct() {
		parent::__construct();
	}

	public function getErrors() {
		return $this -> errors;
	}

	public function getPublication() {
		return $this -> publication;
	}
	

	public function formatPost(array $post) {

		$result = array();

		foreach ($post as $key => $value) {

			if ($key == 'authors' && !empty($value)) {
				$value = $this -> rewriteArray($value);
				$value = array_filter($value);			
				if ($value) {
					 $result['authors'] = $value;
				}
			}
			if (($key == 'key_terms' || $key == 'pages') && !empty($value)) {
				$value = array_filter($value);			
				if ($value) {
					 $result[$key] = $value;
				}
			}
			else if (!empty($value)) {
				$result[$key] = $value;
			}
		}

		return $result;
	}


	private function rewriteArray(array $input) {

		$result = array();
		$given_fields = array_keys($input);

		foreach ($given_fields as $field) {
			foreach ($input[$field] as $key => $value) {
				if (!empty($value)) {
					$result[$key][$field] = $value;
				}
			}
		}
		return $result;
	}


	public function formatImport($string, $import_format) {

		$handler = new FormatHandler($import_format);
		$result = $handler -> import($string);

		return $result;
	}


	private function validateInput($key, $input) {

		$fields = array(
			'type' => 'text',
			'study_field' => 'text',
			'date_published' => 'date',
			'title' => 'text',
			'journal' => 'text',
			'booktitle' => 'text',
			'publisher' => 'text',
			'edition' => 'text',
			'institution' => 'text',
			'howpublished' => 'text',
			'pages' => 'array',
			'from' => 'number',
			'to' => 'number',
			'volume' => 'number',
			'number' => 'number',
			'series' => 'number',
			'given' => 'text',
			'family' => 'text',
			'key_terms' => 'array',
			'abstract' => 'text',
			'name' => 'text');

		$result = false;

		if (array_key_exists($key, $fields)) {

			switch ($fields[$key]) {

				case 'number':
					if ((is_numeric($input) && $input >= 0)) {
						$result = (int)$input;
					}
					break;
				
				case 'text':				
					if (is_string($input)) {						
						$input = trim($input);
						$input = stripslashes($input);
						$input = htmlspecialchars($input);

						if (!empty($input)) {
							$result = $input;
						}
					}
					break;

				case 'date':
					// TODO: test for date
					if (!empty($input)) {
						$result = $input;
					}
					break;

				default:
					throw new Exception('no content type defined for '.$key);
					break;
			}
		}
		else {
			throw new Exception('unknown field '.$key);
		}

		if ($result === false) {
			// $this -> errors[] = 'value for '.$key.' is incorrect';
		}
		return $result;
	}


	public function createNewPublication(array $input) {

		$data = array();
		$authors = array();
		$key_terms = array();

		if (empty($input['type'])
			|| !in_array($input['type'], array_keys($this -> required_fields))) {
			$this -> errors[] = 'No or unknown type given';
			return false;
		}
		
		$required_fields = array_merge($this -> required_fields['all'],
										$this -> required_fields[$input['type']]);
		$optional_fields = array_merge($this -> optional_fields['all'],
										$this -> optional_fields[$input['type']]);	

		$fields = array_merge($required_fields, $optional_fields);	

		foreach ($fields as $field) {
			if (!empty($input[$field])) {

				/* validates authors and creates objects */
				if ($field == 'authors') {
					foreach ($input[$field] as $author_input) {
						$author = $this -> createNewAuthor($author_input);
						if ($author) {
							$authors[] = $author;
						}
					}
				}

				/* validates key terms and creates objects */
				else if ($field == 'key_terms') {
					foreach ($input[$field] as $key_term_input) {
						$key_term = $this -> createNewKeyTerm($key_term_input);
						if ($key_term) {
							$key_terms[] = $key_term;
						}
					}
				}

				/* reformats pages array into separate fields */
				else if ($field == 'pages') {
					$from = false;
					$to = false;

					if (isset($input['pages']['from']) && isset($input['pages']['to'])) {
						$from = $this -> validateInput('from', $input['pages']['from']);
						$to = $this -> validateInput('to', $input['pages']['to']);
					}

					if ($from && $to && ($from <= $to)) {
						$data['pages_from'] = $from;
						$data['pages_to'] = $to;
					}
					else {
						$this -> errors[] = 'Invalid input for pages';
					}
				}

				/* validates all other fields */
				else {
					$value = $this -> validateInput($field, $input[$field]);
					if ($value) {
						$data[$field] = $value;
					}
					else {
						$this -> errors[] = 'Invalid input for '.$field;
					}
				}
			}
			else if (in_array($field, $required_fields)) {
				$this -> errors[] = 'Missing input for '.$field.' required';
			}
		}

		if (empty($this -> errors)) {
			$publication = new Publication($data);
			$publication -> setAuthors($authors);
			$publication -> setKeyTerms($key_terms);

			print_r($publication);
			$this -> publication = $publication;
			return $publication;
		}
		else {
			return false;
		}
	}


	public function createNewAuthor(array $input) {

		$family = false;
		$given = false;

		if (isset($input['family'])) {
			$family = $this -> validateInput('family', $input['family']);
		}
		if (isset($input['given'])) {
			$given = $this -> validateInput('given', $input['given']);
		}


		if ($given && $family) {
			return new Author(array('given' => $given, 'family' => $family));
		}
		else if ($given) {
			$this -> errors[] = 'The author '.$given.' needs a family name';
			return false;
		}
		else if ($family) {
			$this -> errors[] = 'The author '.$family.' needs a given name';
			return false;
		}
		else {
			$this -> errors[] = 'An author needs a given and a family name';
			return false;
		}
	}


	public function createNewKeyTerm($input) {

		$name = $this -> validateInput('name', $input);

		if ($name) {
			return new KeyTerm(array('name' => $name));
		}
		else {
			$this -> errors[] = 'Invalid input for key term '.$input;
			return false;
		}
	}


	public function storePublication(Publication $publication) {

		$data = $publication -> getData();
		$authors = $publication -> getAuthors();
		$key_terms = $publication -> getKeyTerms();

		$author_ids = array();
		foreach ($authors as $author) {
			$author_ids[] = $this -> storeObject($author);
		}

		$key_term_ids = array();
		foreach ($key_terms as $key_term) {
			$key_term_ids[] = $this -> storeObject($key_term);
		}
		// TODO: do this for type and study field!
		if (isset($data['type'])) {
			$data['type_id'] = $this -> storeObject(new Type(array('name' => $data['type'])));
			unset($data['type']);
		}
		if (isset($data['study_field'])) {
			$data['study_field_id'] = $this -> storeObject(new StudyField(array('name' => $data['study_field'])));
			unset($data['study_field']);
		}
		if (isset($data['journal'])) {
			$data['journal_id'] = $this -> storeObject(new Journal(array('name' => $data['journal'])));
			unset($data['journal']);
		}
		if (isset($data['publisher'])) {
			$data['publisher_id'] = $this -> storeObject(new Publisher(array('name' => $data['publisher'])));
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


	public function storeObject(Object $object) {

		$tables = array(
					'Type' => 'list_types',
					'StudyField' => 'list_study_fields',
					'Author' => 'list_authors',
					'KeyTerm' => 'list_key_terms',
					'Journal' => 'list_journals',
					'Publisher' => 'list_publishers',);

		$data = $object -> getData();
		$class = get_class($object);
		$table = isset($tables[$class]) ? $tables[$class] : false;

		$object_id = $this -> db -> insertData($table, $data);

		if (!empty($object_id)) {
			return $object_id;
		}
		else {
			throw new Exception('Error while inserting '.$class.' to DB');
			
		}
	}


}
