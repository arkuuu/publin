<?php

namespace publin\src;

use InvalidArgumentException;
use RuntimeException;

class KeyTermModel {

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
	 * @return KeyTerm[]
	 */
	public function fetch($mode, array $filter = array()) {

		$key_terms = array();

		$data = $this->db->fetchKeyTerms($filter);
		$this->num = $this->db->getNumRows();

		foreach ($data as $key => $value) {
			$key_term = new KeyTerm($value);

			if ($mode) {
				$model = new PublicationModel($this->db);
				$publications = $model->fetch(false, array('key_term_id' => $key_term->getId()));
				$key_term->setPublications($publications);
			}

			$key_terms[] = $key_term;
		}

		return $key_terms;
	}


	public function store(KeyTerm $key_term) {

		$data = $key_term->getData();

		return $this->db->insertData('list_key_terms', $data);
	}


	public function update($id, array $data) {

		return $this->db->updateData('list_key_terms', array('id' => $id), $data);
	}


	public function delete($id) {

		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}

		// Deletes the relations from any publication to this keyword
		$where = array('key_term_id' => $id);
		$this->db->deleteData('rel_publ_to_key_terms', $where);

		// Deletes the keyword itself
		$where = array('id' => $id);
		$rows = $this->db->deleteData('list_key_terms', $where);

		// TODO: how to get rid of these?
		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error while deleting key term '.$id.': '.$this->db->error);
		}
	}
}
