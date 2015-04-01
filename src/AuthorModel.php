<?php

namespace publin\src;

use InvalidArgumentException;
use RuntimeException;

class AuthorModel {


	private $old_db;


	public function __construct(Database $db) {

		$this->old_db = $db;
	}


	public function store(Author $author) {

		$data = $author->getData();
		foreach ($data as $property => $value) {
			if (empty($value) || is_array($value)) {
				unset($data[$property]);
			}
		}

		return $this->old_db->insertData('list_authors', $data);
	}


	public function update($id, array $data) {

		return $this->old_db->updateData('list_authors', array('id' => $id), $data);
	}


	public function delete($id) {

		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}

		$where = array('id' => $id);
		$rows = $this->old_db->deleteData('list_authors', $where);
		// TODO try/catch block

		// TODO: how to get rid of these?
		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error while deleting author '.$id.': '.$this->old_db->error);
		}
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
