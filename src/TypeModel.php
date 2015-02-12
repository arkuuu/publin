<?php

namespace publin\src;

use InvalidArgumentException;
use RuntimeException;

class TypeModel {

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
	 * @return Type[]
	 */
	public function fetch(array $filter = array()) {

		$types = array();

		$data = $this->db->fetchTypes($filter);
		$this->num = $this->db->getNumRows();

		foreach ($data as $key => $value) {
			$types[] = new Type($value);
		}

		return $types;
	}


	public function validate(array $input) {

		$errors = array();

		// validation
		return $errors;
	}


	public function store(Type $type) {

		$data = $type->getData();

		return $this->db->insertData('list_types', $data);
	}


	public function update($id, array $data) {
	}


	public function delete($id) {

		//TODO: this only works when no foreign key constraints fail
		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}
		$where = array('id' => $id);
		$rows = $this->db->deleteData('list_types', $where);

		// TODO: how to get rid of these?
		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error while deleting type '.$id.': '.$this->db->error);
		}
	}
}
