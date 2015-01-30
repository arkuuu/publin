<?php

require_once 'Journal.php';


class JournalModel {


	private $db;
	private $num;

	
	public function __construct(Database $db) {
		$this -> db = $db;
	}

	
	public function getNum() {
		return $this -> num;
	}


	public function fetch(array $filter = array()) {

		$journals = array();

		$data = $this -> db -> fetchJournals($filter);
		$this -> num = $this -> db -> getNumRows();

		foreach ($data as $key => $value) {
			$journals[] = new Journal($value);
		}

		return $journals;
	}


	public function validate(array $input) {

		$errors = array();
		// validation
		return $errors;
	}


	public function create(array $data) {

		// validation here?
		$journal = new Journal($data);
		return $journal;
	}


	public function store(Journal $journal) {

		$data = $journal -> getData();
		$id = $this -> db -> insertData('list_journals', $data);

		if (!empty($id)) {
			return $id;
		}
		else {
			throw new Exception('Error while inserting journal to DB');
			
		}
	}


	public function update($id, array $data) {
		
	}


	public function delete($id) {

	}

}
