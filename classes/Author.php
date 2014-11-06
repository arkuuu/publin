<?php

/**
 * Handles author data.
 *
 * TODO: comment
 */
class Author {

	/**
	 * @var	string
	 */
	private $id;

	/**
	 * Id of the user this author belongs to
	 * @var	string
	 */
	private $user_id;

	/**
	 * @var	string
	 */
	private $last_name;

	/**
	 * @var	string
	 */
	private $first_name;

	/**
	 * @var	string
	 */
	private $academic_title;

	/**
	 * @var	string
	 */
	private $website;

	/**
	 * @var	string
	 */
	private $contact;

	/**
	 * @var	string
	 */
	private $text;

	/**
	 * Array with publications of this author
	 * @var	array
	 */
	private $publications;



	/**
	 * Constructs an author object.
	 *
	 * @param	array	$data	author data from database
	 */
	public function __construct(array $data) {

		foreach ($data as $key => $value) {
			if (property_exists($this, $key)) {
				$this -> $key = $value;
			}
			else {
				// TODO: print error to log
			}
		}
	}


	/**
	 * Returns the id.
	 *
	 * @return 	int
	 */
	public function getId() {
		return (int)$this -> id;
	}


	/**
	 * Returns the user id or 0, if there is no user id.
	 *
	 * @return 	int
	 */
	public function getUserId() {
		if (isset($this -> user_id)) {
			return (int)$this -> user_id;
		}
		else {
			return 0;
		}
	}


	/**
	 * Returns the full name, consisting of academic title, first name and last name.
	 *
	 * @return 	string
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
	 * @return 	string
	 */
	public function getLastName() {
		return $this -> last_name;
	}


	/**
	 * Returns the first name.
	 *
	 * @param	$short		boolean		Set true for first letters only (optional)
	 *
	 * @return 	string
	 */
	public function getFirstName($short = false) {

		if ($short === true) {

			$first_names = preg_split("/\s+/", $this -> first_name);
			$string = '';
			foreach ($first_names as $name) {
				$string .= mb_substr($name, 0, 1).'.';
			}

			return $string;
		}
		else {
			return $this -> first_name;
		}
	}


	/**
	 * Returns the academic title.
	 *
	 * @return 	string
	 */
	public function getAcademicTitle() {
		return $this -> academic_title;
	}


	/**
	 * Returns the website.
	 *
	 * @return 	string
	 */
	public function getWebsite() {
		return $this -> website;
	}


	/**
	 * Returns the contact info.
	 *
	 * @return 	string
	 */
	public function getContact() {
		return $this -> contact;
	}


	/**
	 * Returns the author's text.
	 *
	 * @return 	string
	 */
	public function getText() {
		return $this -> text;
	}


	/**
	 * Returns an array with publications of this authors.
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
