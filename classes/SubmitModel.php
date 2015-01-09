<?php

require_once 'Model.php';

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
			'abstract' => 'text');

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
			'8' => array());

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
			'8' =>	array());

	private $errors = array();



	public function __construct() {

		parent::__construct();
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
					return false;
					break;
			}
		}
		else {
			return false;
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
		foreach ($this -> optional_fields[$input['type_id']] as $field) {

			if (!empty($input[$field])) {
				$input[$field] = $this -> validateInput($field, $input[$field]);

				if ($input[$field]) {
					$data[$field] = $input[$field];
				}
				else {
					$this -> errors[] = 'bad input for '.$field;
				}
			}
		}

		/* Checks if an error occurred */
		if (!empty($this -> errors)) {
			return false;
		}

		return $data;
	}


	public function getErrors() {
		return $this -> errors;
	}
	
}
