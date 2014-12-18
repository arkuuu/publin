<?php

/**
 * Parent class for all 'real' objects.
 *
 * TODO: comment
 */
abstract class Object {

	/**
	 * @var	array
	 */
	protected $data;



	/**
	 * Constructs an object.
	 *
	 * @param	array	$data	type data from database
	 */
	public function __construct(array $data) {

		// TODO: input validation
		$this -> data = $data;
	}


	/**
	 * Returns the id.
	 *
	 * @return int
	 */
	public function getId() {
		return (int)$this -> data['id'];
	}


	/**
	 * Returns the name.
	 *
	 * @return string
	 */
	public function getName() {
		return $this -> data['name'];
	}

}
