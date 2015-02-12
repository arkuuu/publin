<?php

namespace publin\src;

use InvalidArgumentException;
use RuntimeException;

class KeyTermModel {

	private $db;
	private $num;


	public function __construct(Database $db) {

		$this->db = $db;
	}


	public function getNum() {

		return $this->num;
	}


	/**
	 * @param array $filter
	 *
	 * @return KeyTerm[]
	 */
	public function fetch(array $filter = array()) {

		$key_terms = array();

		$data = $this->db->fetchKeyTerms($filter);
		$this->num = $this->db->getNumRows();

		foreach ($data as $key => $value) {
			$key_terms[] = new KeyTerm($value);
		}

		return $key_terms;
	}


	public function validate(array &$input) {

		$errors = array();

		// validation
		return $errors;
	}


	public function store(KeyTerm $key_term) {

		$data = $key_term->getData();

		return $this->db->insertData('list_key_terms', $data);
	}


	public function update($id, array $data) {
	}


	public function delete($id) {

		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}
		$where = array('id' => $id);
		$rows = $this->db->deleteData('list_key_terms', $where);

		// TODO: how to get rid of these?
		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error while deleting key term '.$id.': '.$this->db->error);
		}
	}
}
