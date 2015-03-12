<?php

namespace publin\src;

/**
 * Handles study field data.
 *
 * TODO: comment
 */
class StudyField extends ObjectWithPublications {

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
