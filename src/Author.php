<?php

namespace publin\src;

/**
 * Handles author data.
 *
 * TODO: comment
 */
class Author extends Object {

	/**
	 * Array with publications of this author
	 *
	 * @var    array
	 */
	private $publications;


	/**
	 * Returns the user id or 0, if there is no user id.
	 *
	 * @return    int
	 */
	public function getUserId() {

		return $this->getData('user_id');
	}


	/**
	 * Returns the full name, consisting of academic title, first name and last name.
	 *
	 * @return    string
	 */
	public function getName() {

		// $academic_title = $this -> getData('academic_title');
		$given = $this->getData('given');
		$family = $this->getData('family');

		if ($given && $family) {
			return $given.' '.$family;
		}
		else {
			return false;
		}
	}


	/**
	 * Returns the last name.
	 *
	 * @return    string
	 */
	public function getLastName() {

		return $this->getData('family');
	}


	/**
	 * Returns the first name.
	 *
	 * @param    $short        boolean        Set true for first letters only (optional)
	 *
	 * @return    string
	 */
	public function getFirstName($short = false) {

		$given = $this->getData('given');

		if ($given && $short) {
			$names = preg_split("/\s+/", $given);
			$string = '';
			foreach ($names as $name) {
				$string .= mb_substr($name, 0, 1).'.';
			}

			return $string;
		}
		else if ($given) {
			return $given;
		}
		else {
			return false;
		}
	}


	/**
	 * Returns the academic title.
	 *
	 * @return    string
	 */
	public function getAcademicTitle() {

		return $this->getData('academic_title');
	}


	/**
	 * Returns the website.
	 *
	 * @return    string
	 */
	public function getWebsite() {

		return $this->getData('website');
	}


	/**
	 * Returns the contact info.
	 *
	 * @return    string
	 */
	public function getContact() {

		return $this->getData('contact');
	}


	/**
	 * Returns the author's text.
	 *
	 * @return    string
	 */
	public function getText() {

		return $this->getData('text');
	}


	/**
	 * Returns an array with publications of this authors.
	 *
	 * @return    array
	 */
	public function getPublications() {

		return $this->publications;
	}


	/**
	 * Adds the publications to the author.
	 *
	 * @param    array $publications array with Publications objects
	 *
	 * @return    void
	 */
	public function setPublications(array $publications) {

		// TODO: input validation
		$this->publications = $publications;
	}

}
