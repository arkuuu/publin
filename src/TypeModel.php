<?php

namespace publin\src;

use InvalidArgumentException;
use RuntimeException;

class TypeModel {

	private $old_db;
	private $db;
	private $num;


	public function __construct(Database $db) {

		$this->old_db = $db;
		$this->db = new PDODatabase();
	}


	/**
	 * @param array $filter
	 *
	 * @return Type[]
	 */
	public function fetch(array $filter = array()) {

		$types = array();

		$data = $this->old_db->fetchTypes($filter);
		$this->num = $this->old_db->getNumRows();

		foreach ($data as $key => $value) {
			$types[] = new Type($value);
		}

		return $types;
	}


	public function fetchPublications($type_id) {

		$repo = new PublicationRepository($this->db);

		return $repo->select()->where('type_id', '=', $type_id)->order('date_published', 'DESC')->find();
	}


	public function store(Type $type) {

		$data = $type->getData();
		foreach ($data as $property => $value) {
			if (empty($value) || is_array($value)) {
				unset($data[$property]);
			}
		}

		return $this->old_db->insertData('list_types', $data);
	}


	public function update($id, array $data) {
	}


	public function delete($id) {

		//TODO: this only works when no foreign key constraints fail
		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}
		$where = array('id' => $id);
		$rows = $this->old_db->deleteData('list_types', $where);

		// TODO: how to get rid of these?
		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error while deleting type '.$id.': '.$this->old_db->error);
		}
	}


	public function getValidator() {

		$validator = new Validator();
		$validator->addRule('name', 'text', true, 'Name is required but invalid');

		return $validator;
	}
}
