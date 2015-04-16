<?php


namespace publin\src;

class Permission extends Entity {

	protected $id;
	protected $name;


	public function getId() {

		return $this->id;
	}


	public function getName() {

		return $this->name;
	}
}
