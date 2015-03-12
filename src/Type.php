<?php

namespace publin\src;

/**
 * Handles type data.
 *
 * TODO: comment
 */
class Type extends ObjectWithPublications {

	protected $id;
	protected $name;


	public function getData() {

		$data = array();
		foreach (get_class_vars($this) as $property => $value) {
			$data[$property] = $value;
		}

		return $data;
	}


	public function getId() {

		return $this->id;
	}


	public function getName() {

		return $this->name;
	}
}
