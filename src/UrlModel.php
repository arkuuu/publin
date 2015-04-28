<?php


namespace publin\src;

use InvalidArgumentException;

/**
 * Class UrlModel
 *
 * @package publin\src
 */
class UrlModel extends Model {


	/**
	 * @param Url $url
	 * @param     $publication_id
	 *
	 * @return string
	 * @throws exceptions\DBDuplicateEntryException
	 * @throws exceptions\DBForeignKeyException
	 */
	public function store(Url $url, $publication_id) {

		if (!is_numeric($publication_id)) {
			throw new InvalidArgumentException('publication id must be numeric');
		}

		$query = 'INSERT INTO `urls` (`publication_id`, `name`, `url`) VALUES (:publication_id, :name, :url);';
		$this->db->prepare($query);
		$this->db->bindValue(':publication_id', $publication_id);
		$this->db->bindValue(':name', $url->getName());
		$this->db->bindValue(':url', $url->getUrl());
		$this->db->execute();

		return $this->db->lastInsertId();
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
			throw new InvalidArgumentException('id must be numeric');
		}

		$query = 'DELETE FROM `urls` WHERE `id` = :id;';
		$this->db->prepare($query);
		$this->db->bindValue(':id', (int)$id);
		$this->db->execute();

		return $this->db->rowCount();
	}


	/**
	 * @return Validator
	 */
	public function getValidator() {

		$validator = new Validator();

		$validator->addRule('name', 'text', true, 'Name is invalid');
		$validator->addRule('url', 'url', true, 'Url is required but invalid');

		return $validator;
	}
}
