<?php

require_once 'Publisher.php';


class PublisherModel {


	private $db;
	private $num;

	
	public function __construct(Database $db) {
		$this -> db = $db;
	}

	
	public function getNum() {
		return $this -> num;
	}


	public function fetch(array $filter = array()) {

		$publishers = array();

		$data = $this -> db -> fetchPublishers($filter);
		$this -> num = $this -> db -> getNumRows();

		foreach ($data as $key => $value) {
			$publishers[] = new Publisher($value);
		}

		return $publishers;
	}


	public function validate(array $input) {

		$errors = array();
		// validation
		return $errors;
	}


	public function create(array $data) {

		// validation here?
		$publisher = new Publisher($data);
		return $publisher;
	}


	public function store(Publisher $publisher) {

		$data = $publisher -> getData();
		$id = $this -> db -> insertData('list_publishers', $data);

		if (!empty($id)) {
			return $id;
		}
		else {
			throw new Exception('Error while inserting publisher to DB');
			
		}
	}

}
