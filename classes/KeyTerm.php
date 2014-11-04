<?php

/**
 * Handles key term data.
 *
 * TODO: comment
 */
class KeyTerm {
	
	/**
	 * @var	string
	 */
	private $id;

	/**
	 * @var	string
	 */
	private $name;



	/**
	 * Constructs an KeyTerm object.
	 *
	 * @param	array	$data	key term data from database
	 */
	public function __construct($data) {
		$this -> id = $data['id'];
		$this -> name = $data['name'];
	}


	/**
	 * Returns the id.
	 *
	 * @return int
	 */
	public function getId() {
		return $this -> id;
	}


	/**
	 * Returns the name.
	 *
	 * @return string
	 */
	public function getName() {
		return $this -> name;
	}
	
}
