<?php

namespace publin\src;

class TypeModel {

	private $db;


	public function __construct(PDODatabase $db) {

		$this->db = $db;
	}


	public function store(Type $type) {

		/*$data = $type->getData();
		foreach ($data as $property => $value) {
			if (empty($value) || is_array($value)) {
				unset($data[$property]);
			}
		}

		return $this->old_db->insertData('types', $data);*/

		$query = 'INSERT INTO `types` (`name`, `description`) VALUES (:name, :description);';
		$this->db->prepare($query);
		$this->db->bindValue(':name', $type->getName());
		$this->db->bindValue(':description', $type->getDescription());
		$this->db->execute();

		return $this->db->lastInsertId();
	}


	public function update($id, array $data) {
	}


	public function delete($id) {

		/*//TODO: this only works when no foreign key constraints fail
		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}
		$where = array('id' => $id);
		$rows = $this->old_db->deleteData('types', $where);

		// TODO: how to get rid of these?
		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error while deleting type '.$id.': '.$this->old_db->error);
		}*/
		$query = 'DELETE FROM `types` WHERE `id` = :id;';
		$this->db->prepare($query);
		$this->db->bindValue(':id', (int)$id);
		$this->db->execute();

		return $this->db->rowCount();
	}


	public function getValidator() {

		$validator = new Validator();
		$validator->addRule('name', 'text', true, 'Name is required but invalid');

		return $validator;
	}
}
