<?php

class Author {

	private $db;
	private $id;
	private $user_id;
	private $last_name;
	private $first_name;
	private $academic_title;
	private $publications;
	private $webpage;
	private $contact;
	private $text;


	public function __construct(array $data, Database $db) {

		$this -> db = $db;

		$this -> id = (int)$data['id'];
		$this -> user_id = (int)$data['user_id'];
		$this -> last_name = $data['last_name'];
		$this -> first_name = $data['first_name'];
		$this -> academic_title = $data['academic_title'];
		$this -> webpage = $data['webpage'];
		$this -> contact = $data['contact'];
		$this -> text = $data['text'];

		// TODO: require less data in constructor, get missing data from database if needed
	}

	public function getId() {
		return $this -> id;
	}


	public function getUserId() {
		return $this -> user_id;
	}


	public function getName() {

		return $this -> academic_title.' '.$this -> first_name.' '.$this -> last_name;
	}


	public function getLastName() {
		return $this -> last_name;
	}


	public function getFirstName() {
		return $this -> first_name;
	}


	public function getAcademicTitle() {
		return $this -> academic_title;
	}


	public function getPublications() {

		if (!isset($this -> publications)) {
			$data = $this -> db -> getPublications(array('author_id' => $this -> id));
			$this -> publications = $data;
		}

		return $this -> publications;
	}


	public function getWebpage() {
		return $this -> webpage;
	}


	public function getContact() {
		return $this -> contact;
	}


	public function getText() {
		return $this -> text;
	}


}





?>