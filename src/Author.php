<?php

namespace publin\src;

/**
 * Class Author
 *
 * @package publin\src
 */
class Author {

	protected $id;
	protected $academic_title;
	protected $family;
	protected $given;
	protected $website;
	protected $contact;
	protected $about;


	public function __construct(array $data) {

		foreach ($data as $property => $value) {
			if (property_exists($this, $property)) {
				$this->$property = $value;
			}
		}
	}


	public function getId() {

		return $this->id;
	}


	public function getData() {

		return get_object_vars($this);
	}


	/**
	 * Returns the full name, consisting of academic title, first name and last name.
	 *
	 * @return    string
	 */
	public function getName() {

		if ($this->given && $this->family) {
			return $this->given.' '.$this->family;
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

		return $this->family;
	}


	/**
	 * Returns the first name.
	 *
	 * @param    $short        boolean        Set true for first letters only (optional)
	 *
	 * @return    string
	 */
	public function getFirstName($short = false) {

		if ($this->given && $short) {
			$names = preg_split("/\s+/", $this->given);
			$string = '';
			foreach ($names as $name) {
				$string .= mb_substr($name, 0, 1).'.';
			}

			return $string;
		}
		else if ($this->given) {
			return $this->given;
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

		return $this->website;
	}


	/**
	 * Returns the contact info.
	 *
	 * @return    string
	 */
	public function getContact() {

		return $this->contact;
	}


	/**
	 * Returns the author's text.
	 *
	 * @return    string
	 */
	public function getAbout() {

		return $this->about;
	}
}
