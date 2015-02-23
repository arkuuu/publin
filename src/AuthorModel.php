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

			if ($mode) {
				/* Gets the authors' publications */
				$model = new PublicationModel($this->db);
				$publications = $model->fetch(false, array('author_id' => $value['id']));

				$model = new KeywordModel($this->db);
				$keywords = $model->fetch(false, array('author_id' => $value['id']));
			}
			else {
				$publications = array();
				$keywords = array();
			}

			$authors[] = new Author($value, $publications, $keywords);
		}

		return $authors;
	}


	public function validate(array $input) {
	}


	public function store(Author $author) {

		$data = $author->getData();

		return $this->db->insertData('list_authors', $data);
	}


	public function update($id, array $data) {

		return $this->db->updateData('list_authors', array('id' => $id), $data);
	}


	public function delete($id) {

		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}
	}
}
