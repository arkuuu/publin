<?php

namespace publin\src;

class TypeModel extends Model {

	private $db;


	public function __construct(PDODatabase $db) {

		$this->db = $db;
	}


	public function store(Type $type) {

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
