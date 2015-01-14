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
		return $this -> getData('user_id');
	}


	/**
	 * Returns the full name, consisting of academic title, first name and last name.
	 *
	 * @return 	string
	 */
	public function getName() {
		$academic_title = $this -> getData('academic_title');
		$first_name = $this -> getData('first_name');
		$last_name = $this -> getData('last_name');

		if ($academic_title && $first_name && $last_name) {
			return $academic_title.' '.$first_name.' '.$last_name;
		}
		else if ($first_name && $last_name) {
			return $first_name.' '.$last_name;
		}
		else {
			return false;
		}
	}


	/**
	 * Returns the last name.
	 *
	 * @return 	string
	 */
	public function getLastName() {
		return $this -> getData('last_name');
	}


	/**
	 * Returns the first name.
	 *
	 * @param	$short		boolean		Set true for first letters only (optional)
	 *
	 * @return 	string
	 */
	public function getFirstName($short = false) {

		if ($first_name = $this -> getData('first_name')
				&& $short) {
			$first_names = preg_split("/\s+/", $this -> data['first_name']);
			$string = '';
			foreach ($first_names as $name) {
				$string .= mb_substr($name, 0, 1).'.';
			}
			return $string;
		}
		else if ($first_name = $this -> getData('first_name')) {
			return $first_name;
		}
		else {
			return false;
		}
	}


	/**
	 * Returns the academic title.
	 *
	 * @return 	string
	 */
	public function getAcademicTitle() {
		return $this -> getData('academic_title');
	}


	/**
	 * Returns the website.
	 *
	 * @return 	string
	 */
	public function getWebsite() {
		return $this -> getData('website');
	}


	/**
	 * Returns the contact info.
	 *
	 * @return 	string
	 */
	public function getContact() {
		return $this -> getData('contact');
	}


	/**
	 * Returns the author's text.
	 *
	 * @return 	string
	 */
	public function getText() {
		return $this -> getData('text');
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
		// TODO: input validation
		$this -> publications = $publications;
	}

}
