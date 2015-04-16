<?php


namespace publin\src;

use InvalidArgumentException;

class FileModel extends Model {


	public function store(File $file, $publication_id) {

		if (!is_numeric($publication_id)) {
			throw new InvalidArgumentException('publication id must be numeric');
		}

		$query = 'INSERT INTO `files` (`publication_id`, `name`, `title`, `full_text`, `restricted`, `hidden`) VALUES (:publication_id, :name, :title, :full_text, :restricted, :hidden);';
		$this->db->prepare($query);
		$this->db->bindValue(':publication_id', $publication_id);
		$this->db->bindValue(':name', $file->getName());
		$this->db->bindValue(':title', $file->getTitle());
		$this->db->bindValue(':full_text', $file->isFullText());
		$this->db->bindValue(':restricted', $file->isRestricted());
		$this->db->bindValue(':hidden', $file->isHidden());
		$this->db->execute();

		return $this->db->lastInsertId();
	}


	public function delete($id) {

		if (!is_numeric($id)) {
			throw new InvalidArgumentException('id must be numeric');
		}

		$query = 'DELETE FROM `files` WHERE `id` = :id;';
		$this->db->prepare($query);
		$this->db->bindValue(':id', (int)$id);
		$this->db->execute();

		return $this->db->rowCount();
	}


	public function getValidator() {

		$validator = new Validator();

		$validator->addRule('name', 'text', false, 'File name is invalid');
		$validator->addRule('title', 'text', true, 'File title is required but invalid');
		$validator->addRule('full_text', 'boolean', false, 'Full text flag must be boolean');
		$validator->addRule('restricted', 'boolean', false, 'Restricted flag must be boolean');
		$validator->addRule('hidden', 'boolean', false, 'Hidden flag must be boolean');

		return $validator;
	}
}
