<?php

require_once 'Object.php';

/**
 * Handles author data.
 *
 * TODO: comment
 */
class Author extends Object {

	/**
	 * Array with publications of this author
	 * @var	array
	 */
	private $publications;



	/**
	 * Returns the user id or 0, if there is no user id.
	 *
	 * @return 	int
	 */
	public function getUserId() {
		if (isset($this -> data['user_id'])) {
			return (int)$this -> data['user_id'];
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
		if (empty($this -> data['academic_title'])) {
			return $this -> data['first_name'].' '
					.$this -> data['last_name'];
		}
		else {
			return $this -> data['academic_title'].' '
					.$this -> data['first_name'].' '
					.$this -> data['last_name'];
		}
	}


	/**
	 * Returns the last name.
	 *
	 * @return 	string
	 */
	public function getLastName() {
		return $this -> data['last_name'];
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

			$first_names = preg_split("/\s+/", $this -> data['first_name']);
			$string = '';
			foreach ($first_names as $name) {
				$string .= mb_substr($name, 0, 1).'.';
			}

			return $string;
		}
		else {
			return $this -> data['first_name'];
		}
	}


	/**
	 * Returns the academic title.
	 *
	 * @return 	string
	 */
	public function getAcademicTitle() {
		return $this -> data['academic_title'];
	}


	/**
	 * Returns the website.
	 *
	 * @return 	string
	 */
	public function getWebsite() {
		return $this -> data['website'];
	}


	/**
	 * Returns the contact info.
	 *
	 * @return 	string
	 */
	public function getContact() {
		return $this -> data['contact'];
	}


	/**
	 * Returns the author's text.
	 *
	 * @return 	string
	 */
	public function getText() {
		return $this -> data['text'];
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
