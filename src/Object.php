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

	// TODO: doc
	public function getData($field = null) {
		if (isset($field)) {
			if (!empty($this -> data[$field])) {
				return $this -> data[$field];
			}
			else {
				return false;
			}
		}
		return $this -> data;
	}

	public function setData(array $data) {

		foreach ($data as $key => $value) {
			if (isset($this -> data[$key]) && $key != 'id') {
				$this -> data[$key] = $value;
			}
		}
	}

	/**
	 * Returns the id.
	 *
	 * @return int
	 */
	public function getId() {
		return $this -> getData('id');
	}


	/**
	 * Returns the name.
	 *
	 * @return string
	 */
	public function getName() {
		return $this -> getData('name');
	}

}
