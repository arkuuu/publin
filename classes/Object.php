<?php

/**
 * Parent class for all 'real' objects.
 *
 * TODO: comment
 */
abstract class Object {

	/**
	 * @var	string
	 */
	protected $id;

	/**
	 * @var	string
	 */
	protected $name;



	/**
	 * Constructs an object.
	 *
	 * @param	array	$data	type data from database
	 */
	public function __construct(array $data) {

		foreach ($data as $key => $value) {
			if (property_exists($this, $key)) {
				$this -> $key = $value;
			}
			else {
				// TODO: print error to log
			}
		}
	}


	/**
	 * Returns the id.
	 *
	 * @return int
	 */
	public function getId() {
		return (int)$this -> id;
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
