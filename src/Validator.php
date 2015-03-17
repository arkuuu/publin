<?php


namespace publin\src;

use UnexpectedValueException;

class Validator {

	private $errors;
	private $rules;
	private $result;


	public function __construct() {

		$this->reset();
	}


	public function reset() {

		$this->rules = array();
		$this->result = array();
		$this->errors = array();
	}


	public function getErrors() {

		return $this->errors;
	}


	public function getSanitizedResult() {

		return $this->result;
	}


	public function addRule($field, $type, $required, $error_msg) {

		$this->rules[$field] = array('type'      => $type,
									 'required'  => $required,
									 'error_msg' => $error_msg);
	}


	public function validate(array $input) {

		$this->result = array();
		$this->errors = array();

		$result = array();

		foreach ($this->rules as $field => $rule) {

			if (isset($input[$field])) {
				$value = $input[$field];

				switch ($rule['type']) {

					case 'number':
						if ($this->sanitizeNumber($value)) {
							$result[$field] = $this->sanitizeNumber($value);
						}
						else if ($rule['required'] == true) {
							$this->errors[] = $rule['error_msg'];
						}
						else {
							$result[$field] = null;
						}
						break;

					case 'text':
						if ($this->sanitizeText($value)) {
							$result[$field] = $this->sanitizeText($value);
						}
						else if ($rule['required'] == true) {
							$this->errors[] = $rule['error_msg'];
						}
						else {
							$result[$field] = '';
						}
						break;

					case 'date':
						if ($this->sanitizeDate($value)) {
							$result[$field] = $this->sanitizeDate($value);
						}
						else if ($rule['required'] == true) {
							$this->errors[] = $rule['error_msg'];
						}
						else {
							$result[$field] = '';
						}
						break;

					case 'url':
						if ($this->sanitizeUrl($value)) {
							$result[$field] = $this->sanitizeUrl($value);
						}
						else if ($rule['required'] == true) {
							$this->errors[] = $rule['error_msg'];
						}
						else {
							$result[$field] = '';
						}
						break;

					case 'email':
						if ($this->sanitizeEmail($value)) {
							$result[$field] = $this->sanitizeEmail($value);
						}
						else if ($rule['required'] == true) {
							$this->errors[] = $rule['error_msg'];
						}
						else {
							$result[$field] = '';
						}
						break;

					case 'boolean':
						if ($this->sanitizeBoolean($value)) {
							$result[$field] = $this->sanitizeBoolean($value);
						}
						else if ($rule['required'] == true) {
							$this->errors[] = $rule['error_msg'];
						}
						else {
							$result[$field] = '';
						}
						break;

					default:
						throw new UnexpectedValueException('unknown validation rule '.$rule['type']);
						break;
				}
			}
			else if ($rule['required'] == true) {
				$this->errors[] = $rule['error_msg'];
			}
		}

		if (empty($this->errors)) {
			$this->result = $result;

			return true;
		}
		else {
			return false;
		}
	}


	public static function sanitizeNumber($input) {

		if (is_string($input)) {
			$input = trim($input);
		}
		if (is_numeric($input) && $input >= 0) {
			return (int)$input;
		}
		else {
			return false;
		}
	}


	public static function sanitizeText($input) {

		if (is_string($input)) {
			$input = trim($input);
			//$text = stripslashes($text);
			//$text = strip_tags($text); TODO: check if useful

			return $input;
		}
		else {
			return false;
		}
	}


	public static function sanitizeDate($input) {

		if (is_string($input)) {
			$input = trim($input);

			// TODO
			return $input;
		}
		else {
			return false;
		}
	}


	public static function sanitizeUrl($input) {

		if (is_string($input)) {
			$input = trim($input);

			// TODO
			return $input;
		}
		else {
			return false;
		}
	}


	public static function sanitizeEmail($input) {

		if (is_string($input)) {
			$input = trim($input);

			// TODO
			return $input;
		}
		else {
			return false;
		}
	}


	public static function sanitizeBoolean($input) {

		if (is_string($input)) {
			$input = trim($input);
			$input = strtolower($input);

			switch ($input) {
				case '1':
				case 'true':
				case 'yes':
				case 'y':
				case 'on':
					return true;
				default:
					return false;
			}
		}
		else {
			return (bool)$input;
		}
	}
}
