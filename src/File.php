<?php


namespace publin\src;

/**
 * Class File
 *
 * @package publin\src
 */
class File extends Entity {

	protected $id;
	protected $name;
	protected $title;
	protected $full_text;
	protected $restricted;
	protected $hidden;


	/**
	 * @return string|null
	 */
	public function getId() {

		return $this->id;
	}


	/**
	 * @return string|null
	 */
	public function getName() {

		return $this->name;
	}


	/**
	 * @return string|null
	 */
	public function getTitle() {

		return $this->title;
	}


	/**
	 * @return bool
	 */
	public function isFullText() {

		if ($this->full_text) {
			return true;
		}
		else {
			return false;
		}
	}


	/**
	 * @return bool
	 */
	public function isRestricted() {

		if ($this->restricted) {
			return true;
		}
		else {
			return false;
		}
	}


	/**
	 * @return bool
	 */
	public function isHidden() {

		if ($this->hidden) {
			return true;
		}
		else {
			return false;
		}
	}
}
