<?php

namespace publin\src;

use InvalidArgumentException;

class AuthorModel {


	private $old_db;
	private $db;


	public function __construct(PDODatabase $db) {

		$this->old_db = new Database();
		$this->db = $db;
	}


	public function store(Author $author) {

		$query = 'INSERT INTO `authors` (`family`, `given`, `website`, `contact`, `about`, `modified`) VALUES (:family, :given, :website, :contact, :about, NOW()) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id);';
		$this->db->prepare($query);
		$this->db->bindValue(':family', $author->getLastName());
		$this->db->bindValue(':given', $author->getFirstName());
		$this->db->bindValue(':website', $author->getWebsite());
		$this->db->bindValue(':contact', $author->getContact());
		$this->db->bindValue(':about', $author->getAbout());

		$this->db->execute();

		return $this->db->lastInsertId();
	}


	public function update($id, array $data) {

		return $this->old_db->updateData('authors', array('id' => $id), $data);
	}


	public function delete($id) {

		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}

		$query = 'DELETE FROM `authors` WHERE `id` = :id;';
		$this->db->prepare($query);
		$this->db->bindValue(':id', (int)$id);
		$this->db->execute();

		return $this->db->rowCount();
	}


	public function getValidator() {

		$validator = new Validator();
		$validator->addRule('given', 'text', true, 'Given name is required but invalid');
		$validator->addRule('family', 'text', true, 'Family name is required but invalid');
		$validator->addRule('website', 'url', false, 'Website URL is invalid');
		$validator->addRule('contact', 'text', false, 'Contact info is invalid');
		$validator->addRule('about', 'text', false, 'About text is invalid');

		return $validator;
	}
}
