<?php

namespace publin\src;

use InvalidArgumentException;
use PDOException;

/**
 * Class KeywordModel
 *
 * @package publin\src
 */
class KeywordModel extends Model {


	/**
	 * @param Keyword $keyword
	 *
	 * @return string
	 * @throws exceptions\DBDuplicateEntryException
	 * @throws exceptions\DBForeignKeyException
	 */
	public function store(Keyword $keyword) {

		$query = 'INSERT INTO `keywords` (`name`) VALUES (:name) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id);';
		$this->db->prepare($query);
		$this->db->bindValue(':name', $keyword->getName());
		$this->db->execute();

		return $this->db->lastInsertId();
	}


	/**
	 * @param       $id
	 * @param array $data
	 *
	 * @return int
	 */
	public function update($id, array $data) {

		$old_db = new OldDatabase();

		return $old_db->updateData('keywords', array('id' => $id), $data);
	}


	/**
	 * @param $id
	 *
	 * @return int
	 * @throws exceptions\DBDuplicateEntryException
	 * @throws exceptions\DBForeignKeyException
	 */
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


	/**
	 * @return Validator
	 */
	public function getValidator() {

		$validator = new Validator();
		$validator->addRule('name', 'text', true, 'Name is required but invalid');

		return $validator;
	}
}
