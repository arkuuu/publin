<?php

namespace publin\src;

/**
 * Handles key term data.
 *
 * TODO: comment
 */
class Keyword extends ObjectWithPublications {

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
