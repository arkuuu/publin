<?php

namespace publin\src;

use InvalidArgumentException;
use PDOException;

class KeywordModel {

	private $old_db;
	private $db;


	public function __construct(PDODatabase $db) {

		$this->old_db = new Database();
		$this->db = $db;
	}



	public function store(Keyword $keyword) {

		$query = 'INSERT INTO `keywords` (`name`) VALUES (:name) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id);';
		$this->db->prepare($query);
		$this->db->bindValue(':name', $keyword->getName());
		$this->db->execute();

		return $this->db->lastInsertId();
	}


	public function update($id, array $data) {

		return $this->old_db->updateData('keywords', array('id' => $id), $data);
	}


	public function delete($id) {

		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}

		$this->db->beginTransaction();

		try {
			$query = 'DELETE FROM `publications_keywords` WHERE `keyword_id` = :keyword_id;';
			$this->db->prepare($query);
			$this->db->bindValue(':keyword_id', (int)$id);
			$this->db->execute();

			$query = 'DELETE FROM `keywords` WHERE `id` = :id;';
			$this->db->prepare($query);
			$this->db->bindValue(':id', (int)$id);
			$this->db->execute();
			$row_count = $this->db->rowCount();

			$this->db->commitTransaction();

			return $row_count;
		}
		catch (PDOException $e) {
			$this->db->cancelTransaction();
			throw $e;
		}
	}


	public function getValidator() {

		$validator = new Validator();
		$validator->addRule('name', 'text', true, 'Name is required but invalid');

		return $validator;
	}
}
