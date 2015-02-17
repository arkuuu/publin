<?php

namespace publin\src;

use InvalidArgumentException;
use RuntimeException;

class JournalModel {


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
	 * @return Journal[]
	 */
	public function fetch($mode, array $filter = array()) {

		$journals = array();

		$data = $this->db->fetchJournals($filter);
		$this->num = $this->db->getNumRows();

		foreach ($data as $key => $value) {
			$journal = new Journal($value);

			if ($mode) {
				$model = new PublicationModel($this->db);
				$publications = $model->fetch(false, array('journal_id' => $journal->getId()));
				$journal->setPublications($publications);
			}

			$journals[] = $journal;
		}

		return $journals;
	}


	public function validate(array $input) {

		$errors = array();

		// validation
		return $errors;
	}


	public function store(Journal $journal) {

		$data = $journal->getData();

		return $this->db->insertData('list_journals', $data);
	}


	public function update($id, array $data) {
	}


	public function delete($id) {

		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}
		$where = array('id' => $id);
		$rows = $this->db->deleteData('list_journals', $where);

		// TODO: how to get rid of these?
		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error while deleting journal '.$id.': '.$this->db->error);
		}
	}
}
