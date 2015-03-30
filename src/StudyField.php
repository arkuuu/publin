<?php

namespace publin\src;

/**
 * Handles study field data.
 *
 * TODO: comment
 */
class StudyField {

	protected $id;
	protected $name;


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
}
