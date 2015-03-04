<?php

namespace publin\src;

use InvalidArgumentException;
use RuntimeException;

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

		$where = array('id' => $id);
		$rows = $this->db->deleteData('list_authors', $where);
		// TODO try/catch block

		// TODO: how to get rid of these?
		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error while deleting author '.$id.': '.$this->db->error);
		}
	}
}
