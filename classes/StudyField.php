<?php

class StudyField {

	private $id;
	private $name;


	public function __construct(array $data) {
		$this -> id = $data['id'];
		$this -> name = $data['name'];
	}


	public function getId() {
		return $this -> id;
	}


	public function getName() {
		return $this -> name;
	}

}
