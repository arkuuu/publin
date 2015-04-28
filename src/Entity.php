<?php


namespace publin\src;

/**
 * Class Entity
 *
 * @package publin\src
 */
class Entity {

	/**
	 * @param array $data
	 */
	public function __construct(array $data) {

		foreach ($data as $property => $value) {
			if (property_exists($this, $property)) {
				$this->$property = $value;
			}
		}
	}


	/**
	 * @return array
	 */
	public function getData() {

		return get_object_vars($this);
	}
}
