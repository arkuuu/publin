<?php

namespace publin\src;

use InvalidArgumentException;
use RuntimeException;

class KeywordModel {

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
	 * @return Keyword[]
	 */
	public function fetch(array $filter = array()) {

		$keywords = array();

		$data = $this->db->fetchKeywords($filter);
		$this->num = $this->db->getNumRows();

		foreach ($data as $key => $value) {
			$keywords[] = new Keyword($value);
		}

		return $keywords;
	}


	public function fetchPublications($keyword_id) {

		$model = new PublicationModel($this->db);

		return $model->findByKeyword($keyword_id);
	}


	public function store(Keyword $keyword) {

		$data = $keyword->getData();
		foreach ($data as $property => $value) {
			if (empty($value) || is_array($value)) {
				unset($data[$property]);
			}
		}

		return $this->db->insertData('list_keywords', $data);
	}


	public function update($id, array $data) {

		return $this->db->updateData('list_keywords', array('id' => $id), $data);
	}


	public function delete($id) {

		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}

		// Deletes the relations from any publication to this keyword
		$where = array('keyword_id' => $id);
		$this->db->deleteData('rel_publication_keywords', $where);

		// Deletes the keyword itself
		$where = array('id' => $id);
		$rows = $this->db->deleteData('list_keywords', $where);

		// TODO: how to get rid of these?
		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error while deleting keyword '.$id.': '.$this->db->error);
		}
	}


	public function getValidator() {

		$validator = new Validator();
		$validator->addRule('name', 'text', true, 'Name is required but invalid');

		return $validator;
	}
}
