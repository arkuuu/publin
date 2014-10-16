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

	private $metadata_complete;


	public function __construct(array $data, Database $db) {

		$this -> db = $db;

		$this -> id = (int)$data['id'];
		$this -> user_id = (int)$data['user_id'];
		$this -> last_name = $data['last_name'];
		$this -> first_name = $data['first_name'];
		$this -> academic_title = $data['academic_title'];
		$this -> metadata_complete = false;
	}


	/**
	 * Loads additional metadata from the database. Additional metadata is the data
	 * which is only needed for the author pages and thus should not be loaded everytime.
	 *
	 * @return	void
	 */
	private function getMissingData() {

		/* Gets missing meta data */
		$data = $this -> db -> getAuthors(array(
										'select' => array('webpage', 'contact', 'text'),
										'id' => $this -> id));
		$this -> webpage = $data[0]['webpage'];
		$this -> contact = $data[0]['contact'];
		$this -> text = $data[0]['text'];

		/* Determines that all metadata is loaded */
		$this -> metadata_complete = true;
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

		/* Checks if publication data was loaded already */
		if (!isset($this -> publications)) {

			/* Gets publication data */
			$data = $this -> db -> getPublications(array('author_id' => $this -> id));
			$this -> publications = $data;
		}

		return $this -> publications;
	}


	public function getWebpage() {

		/* Checks if missing metadata was loaded already */
		if (!$this -> metadata_complete) {
			$this -> getMissingData();
		}

		return $this -> webpage;
	}


	public function getContact() {

		/* Checks if missing metadata was loaded already */
		if (!$this -> metadata_complete) {
			$this -> getMissingData();
		}

		return $this -> contact;
	}


	public function getText() {

		/* Checks if missing metadata was loaded already */
		if (!$this -> metadata_complete) {
			$this -> getMissingData();
		}
		return $this -> text;
	}


}





?>