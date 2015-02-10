<?php

namespace publin\src;

use InvalidArgumentException;

/**
 * Class Author
 *
 * @package publin\src
 */
class Author extends Object {

	/**
	 * Array with publications of this author
	 *
	 * @var  Publication[]
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
	 * @return Publication[]
	 */
	public function getPublications() {

		return $this->publications;
	}


	/**
	 * @param array $publications
	 *
	 * @return bool
	 */
	public function setPublications(array $publications) {

		foreach ($publications as $publication) {

			if ($publication instanceof Publication) {
				$this->publications[] = $publication;
			}
			else {
				throw new InvalidArgumentException('must be array with Publication objects');
			}
		}

		return true;
	}

}
