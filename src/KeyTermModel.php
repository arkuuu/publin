<?php

require_once 'KeyTerm.php';


class KeyTermModel {


	private $db;
	private $num;

	
	public function __construct(Database $db) {
		$this -> db = $db;
	}

	
	public function getNum() {
		return $this -> num;
	}


	public function fetch(array $filter = array()) {

		$key_terms = array();

		$data = $this -> db -> fetchKeyTerms($filter);
		$this -> num = $this -> db -> getNumRows();

		foreach ($data as $key => $value) {
			$key_terms[] = new KeyTerm($value);
		}

		return $key_terms;
	}


	public function validate(array $input) {

		$errors = array();
		// validation
		return $errors;
	}


	public function create(array $data) {

		// validation here?
		$key_term = new KeyTerm($data);
		return $key_term;
	}


	public function store(KeyTerm $key_term) {

		$data = $key_term -> getData();
		$id = $this -> db -> insertData('list_key_terms', $data);

		if (!empty($id)) {
			return $id;
		}
		else {
			throw new Exception('Error while inserting key term to DB');
			
		}
	}

}
