<?php


namespace publin\src;

class Entity {

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

}
