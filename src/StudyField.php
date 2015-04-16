<?php

namespace publin\src;

class StudyField extends Entity {

	protected $id;
	protected $name;
	protected $description;


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
