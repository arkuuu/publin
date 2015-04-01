<?php

namespace publin\src;

use InvalidArgumentException;
use RuntimeException;

class KeywordModel {

	private $old_db;
	private $db;
	private $num;


	public function __construct(Database $db) {

		$this->old_db = $db;
		$this->db = new PDODatabase();
	}


	public function getNum() {

		return $this->num;
	}


	public function store(Keyword $keyword) {

		$data = $keyword->getData();
		foreach ($data as $property => $value) {
			if (empty($value) || is_array($value)) {
				unset($data[$property]);
			}
		}

		return $this->old_db->insertData('list_keywords', $data);
	}


	public function update($id, array $data) {

		return $this->old_db->updateData('list_keywords', array('id' => $id), $data);
	}


	public function delete($id) {

		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}

		// Deletes the relations from any publication to this keyword
		$where = array('keyword_id' => $id);
		$this->old_db->deleteData('rel_publication_keywords', $where);

		// Deletes the keyword itself
		$where = array('id' => $id);
		$rows = $this->old_db->deleteData('list_keywords', $where);

		// TODO: how to get rid of these?
		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error while deleting keyword '.$id.': '.$this->old_db->error);
		}
	}


	public function getValidator() {

		$validator = new Validator();
		$validator->addRule('name', 'text', true, 'Name is required but invalid');

		return $validator;
	}
}
