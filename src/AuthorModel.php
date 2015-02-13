<?php

namespace publin\src;

use InvalidArgumentException;

class AuthorModel {


	private $db;
	private $num;


	public function __construct(Database $db) {

		$this->db = $db;
	}


	public function getNum() {

		return $this->num;
	}


	/**
	 * @param       $mode
	 * @param array $filter
	 *
	 * @return Author[]
	 */
	public function fetch($mode, array $filter = array()) {

		$authors = array();

		/* Gets the authors */
		$data = $this->db->fetchAuthors($filter);
		$this->num = $this->db->getNumRows();

		foreach ($data as $key => $value) {
			$author = new Author($value);

			if ($mode) {
				/* Gets the authors' publications */
				$model = new PublicationModel($this->db);
				$publications = $model->fetch(false, array('author_id' => $author->getId()));
				$author->setPublications($publications);
			}

			$authors[] = $author;
		}

		return $authors;
	}


	public function validate(array $input) {

		$validator = new Validator();
		$validator->addRule('given', 'text', true, 'Given name is required but invalid');
		$validator->addRule('family', 'text', true, 'Family name is required but invalid');
		$validator->addRule('website', 'url', false, 'Website URL is invalid');
		$validator->addRule('contact', 'text', false, 'Contact info is invalid');
		$validator->addRule('text', 'text', false, 'Text is invalid');

		if ($validator->validate($input)) {
			return $validator->getSanitizedResult();
		}
		else {
			return false;
		}
	}


	public function store(Author $author) {

		// validation here?
		$data = $author->getData();

		return $this->db->insertData('list_authors', $data);
	}


	public function update($id, array $data) {
	}


	public function delete($id) {

		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}
	}
}
