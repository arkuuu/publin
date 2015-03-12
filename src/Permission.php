<?php


namespace publin\src;

class Permission {

	private $id;
	private $name;


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
}
