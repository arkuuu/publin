<?php

class Author {

	private $id;
	private $user_id;
	private $last_name;
	private $first_name;
	private $academic_title;
	private $website;
	private $contact;
	private $text;
	private $publications;


	public function __construct(array $data) {

		foreach ($data as $key => $value) {
			$this -> $key = $value;
		}
	}


	/**
	 * Returns the id.
	 *
	 * @return int
	 */
	public function getId() {
		return $this -> id;
	}


	/**
	 * Returns the user id or 0, if there is no user id.
	 *
	 * @return int
	 */
	public function getUserId() {
		if (isset($this -> user_id)) {
			return $this -> user_id;
		}
		else {
			return 0;
		}
	}


	/**
	 * Returns the full name, consisting of academic title, first name and last name.
	 *
	 * @return string
	 */
	public function getName() {
		if (empty($this -> academic_title)) {
			return $this -> first_name.' '.$this -> last_name;
		}
		else {
			return $this -> academic_title.' '.$this -> first_name.' '.$this -> last_name;
		}
	}


	/**
	 * Returns the last name.
	 *
	 * @return string
	 */
	public function getLastName() {
		return $this -> last_name;
	}


	/**
	 * Returns the first name.
	 *
	 * @return string
	 */
	public function getFirstName() {
		return $this -> first_name;
	}


	/**
	 * Returns the academic title.
	 *
	 * @return string
	 */
	public function getAcademicTitle() {
		return $this -> academic_title;
	}


	/**
	 * Returns the website.
	 *
	 * @return string
	 */
	public function getWebsite() {
		return $this -> website;
	}


	/**
	 * Returns the contact info.
	 *
	 * @return string
	 */
	public function getContact() {
		return $this -> contact;
	}


	/**
	 * Returns the author's text.
	 *
	 * @return string
	 */
	public function getText() {
		return $this -> text;
	}


	/**
	 * Returns an array with publications of this authors.
	 * The array consists of Publication objects.
	 *
	 * @return	array
	 */
	public function getPublications() {
		return $this -> publications;
	}


	/**
	 * Adds the publications to the author.
	 *
	 * @param	array	$publications	array with Publications objects
	 *
	 * @return	void
	 */
	public function setPublications(array $publications) {
		$this -> publications = $publications;
	}

}
