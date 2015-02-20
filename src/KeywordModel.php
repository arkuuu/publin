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
	 * @param       $mode
	 * @param array $filter
	 *
	 * @return Keyword[]
	 */
	public function fetch($mode, array $filter = array()) {

		$keywords = array();

		$data = $this->db->fetchKeywords($filter);
		$this->num = $this->db->getNumRows();

		foreach ($data as $key => $value) {
			$keyword = new Keyword($value);

			if ($mode) {
				$model = new PublicationModel($this->db);
				$publications = $model->fetch(false, array('keyword_id' => $keyword->getId()));
				$keyword->setPublications($publications);
			}

			$keywords[] = $keyword;
		}

		return $keywords;
	}


	public function store(Keyword $keyword) {

		$data = $keyword->getData();

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
}
