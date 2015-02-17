<?php

namespace publin\src;

use InvalidArgumentException;
use RuntimeException;

class PublisherModel {


	private $db;
	private $num;


	public function __construct(Database $db) {

		$this->db = $db;
	}


	public function getNum() {

		return $this->num;
	}


	/**
	 * @param array $filter
	 *
	 * @return Publisher[]
	 */
	public function fetch($mode, array $filter = array()) {

		$publishers = array();

		$data = $this->db->fetchPublishers($filter);
		$this->num = $this->db->getNumRows();

		foreach ($data as $key => $value) {
			$publisher = new Publisher($value);

			if ($mode) {
				$model = new PublicationModel($this->db);
				$publications = $model->fetch(false, array('publisher_id' => $publisher->getId()));
				$publisher->setPublications($publications);
			}

			$publishers[] = $publisher;
		}

		return $publishers;
	}


	public function validate(array $input) {

		$errors = array();

		// validation
		return $errors;
	}


	public function store(Publisher $publisher) {

		$data = $publisher->getData();

		return $this->db->insertData('list_publishers', $data);
	}


	public function update($id, array $data) {
	}


	public function delete($id) {

		//TODO: this only works when no foreign key constraints fail
		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}
		$where = array('id' => $id);
		$rows = $this->db->deleteData('list_publishers', $where);

		// TODO: how to get rid of these?
		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error while deleting publisher '.$id.': '.$this->db->error);
		}
	}
}
