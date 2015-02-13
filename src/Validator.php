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


	public function resetResult() {

		$this->result = array();
		$this->errors = array();
	}


	public function validate(array $input) {

		$result = array();

		foreach ($this->rules as $field => $rule) {

			if (empty($input[$field]) && $rule['required'] == true) {
				$this->errors[] = $rule['error_msg'];
			}
			else if (!empty($input[$field])) {

				$temp = $input[$field];

				// TODO: refactor these into single methods?
				switch ($rule['type']) {

					case 'number':
						if ((is_numeric($temp) && $temp >= 0)) {
							$result[$field] = (int)$temp;
						}
						else {
							$this->errors[] = $rule['error_msg'];
						}
						break;

					case 'text':
						if (is_string($temp)) {
							$temp = trim($temp);
							$temp = stripslashes($temp);
							$temp = htmlspecialchars($temp);

							if (!empty($temp)) {
								$result[$field] = $temp;
							}
							else {
								$this->errors[] = $rule['error_msg'];
							}
						}
						break;

					case 'date':
						// TODO: test for date
						if (!empty($temp)) {
							$result[$field] = $temp;
						}
						else {
							$this->errors[] = $rule['error_msg'];
						}
						break;

					case 'url':
						// TODO: test for url
						if (!empty($temp)) {
							$result[$field] = $temp;
						}
						else {
							$this->errors[] = $rule['error_msg'];
						}
						break;

					default:
						throw new InvalidArgumentException('unknown validation rule '.$rule['type']);
						break;
				}
				$result[$field] = $input[$field];
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
}
