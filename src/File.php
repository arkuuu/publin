<?php


namespace publin\src;

class File {

	private $id;
	private $name;
	private $restricted;


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


	public function isRestricted() {

		if ($this->restricted) {
			return true;
		}
		else {
			return false;
		}
	}
}
