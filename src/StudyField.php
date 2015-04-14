<?php

namespace publin\src;

class StudyField extends Entity {

	protected $id;
	protected $name;
	protected $description;


	public function __construct(array $data) {

		foreach ($data as $property => $value) {
			if (property_exists($this, $property)) {
				$this->$property = $value;
			}
		}
	}


	public function getData() {

		return get_object_vars($this);
	}


	public function getId() {

		return $this->id;
	}


	public function getName() {

		return $this->name;
	}


	public function getDescription() {

		return $this->description;
	}
}
