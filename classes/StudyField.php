<?php

/**
 * Handles study field data.
 *
 * TODO: comment
 */
class StudyField {

	/**
	 * @var	string
	 */
	private $id;

	/**
	 * @var	string
	 */
	 private $name;



	/**
	 * Constructs an StudyField object.
	 *
	 * @param	array	$data	study field data from database
	 */
	public function __construct(array $data) {
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
