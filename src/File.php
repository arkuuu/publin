<?php


namespace publin\src;

class File {

	private $id;
	private $name;
	private $title;
	private $full_text;
	private $restricted;
	private $hidden;


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


	public function getName() {

		return $this->name;
	}


	public function getTitle() {

		return $this->title;
	}


	public function isFullText() {

		if ($this->full_text) {
			return true;
		}
		else {
			return false;
		}
	}

	public function isRestricted() {

		if ($this->restricted) {
			return true;
		}
		else {
			return false;
		}
	}


	public function isHidden() {

		if ($this->hidden) {
			return true;
		}
		else {
			return false;
		}
	}
}
