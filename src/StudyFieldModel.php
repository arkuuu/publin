<?php

namespace publin\src;

use InvalidArgumentException;

class StudyFieldModel extends Model {


	private $db;


	public function __construct(Database $db) {

		$this->$db = $db;
	}


	public function store(StudyField $study_field) {

		$query = 'INSERT INTO `study_fields` (`name`, `description`) VALUES (:name, :description);';
		$this->db->prepare($query);
		$this->db->bindValue(':name', $study_field->getName());
		$this->db->bindValue(':description', $study_field->getDescription());
		$this->db->execute();

		return $this->db->lastInsertId();
	}


	public function update($id, array $data) {
	}


	public function delete($id) {

		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}

		$query = 'DELETE FROM `study_fields` WHERE `id` = :id;';
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
