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

		return get_object_vars($this);
	}


	public function getId() {

		return $this->id;
	}


	public function getName() {

		return $this->name;
	}
}
