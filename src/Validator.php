<?php


namespace publin\src;

use InvalidArgumentException;

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

			if (empty($input[$field]) && $rule['required'] == true) {
				$this->errors[] = $rule['error_msg'];
			}
			else if (isset($input[$field])) {

				$temp = $input[$field];

				// TODO: refactor these into single methods?
				switch ($rule['type']) {

					case 'number':
						if ($this->sanitizeNumber($temp)) {
							$result[$field] = $this->sanitizeNumber($temp);
						}
						else if ($rule['required'] == true) {
							$this->errors[] = $rule['error_msg'];
						}
						else {
							$result[$field] = null;
						}
						break;

					case 'text':
						if ($this->sanitizeText($temp)) {
							$result[$field] = $this->sanitizeText($temp);
						}
						else if ($rule['required'] == true) {
							$this->errors[] = $rule['error_msg'];
						}
						else {
							$result[$field] = '';
						}
						break;

					case 'date':
						if ($this->sanitizeDate($temp)) {
							$result[$field] = $this->sanitizeDate($temp);
						}
						else if ($rule['required'] == true) {
							$this->errors[] = $rule['error_msg'];
						}
						else {
							$result[$field] = '';
						}
						break;

					case 'url':
						if ($this->sanitizeUrl($temp)) {
							$result[$field] = $this->sanitizeUrl($temp);
						}
						else if ($rule['required'] == true) {
							$this->errors[] = $rule['error_msg'];
						}
						else {
							$result[$field] = '';
						}
						break;

					case 'email':
						if ($this->sanitizeEmail($temp)) {
							$result[$field] = $this->sanitizeEmail($temp);
						}
						else if ($rule['required'] == true) {
							$this->errors[] = $rule['error_msg'];
						}
						else {
							$result[$field] = '';
						}
						break;

					default:
						throw new InvalidArgumentException('unknown validation rule '.$rule['type']);
						break;
				}
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


	public static function sanitizeNumber($number) {

		if (is_string($number)) {
			$number = trim($number);
		}
		if (is_numeric($number) && $number >= 0) {
			return $number;
		}
		else {
			return false;
		}
	}


	public static function sanitizeText($text) {

		if (is_string($text)) {
			$text = trim($text);
			$text = stripslashes($text);
			$text = htmlspecialchars($text);

			return $text;
		}
		else {
			return false;
		}
	}


	public static function sanitizeDate($date) {

		if (is_string($date)) {
			$date = trim($date);

			// TODO
			return $date;
		}
		else {
			return false;
		}
	}


	public static function sanitizeUrl($url) {

		if (is_string($url)) {
			$url = trim($url);

			// TODO
			return $url;
		}
		else {
			return false;
		}
	}


	public static function sanitizeEmail($email) {

		if (is_string($email)) {
			$email = trim($email);

			// TODO
			return $email;
		}
		else {
			return false;
		}
	}
}
