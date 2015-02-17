<?php

namespace publin\src;

/**
 * Parent class for all 'real' objects.
 *
 * TODO: comment
 */
class Object {

	/**
	 * @var    array
	 */
	protected $data;


	/**
	 * Constructs an object.
	 *
	 * @param    array $data type data from database
	 */
	public function __construct(array $data) {

		$this->data = $data;
	}


	/**
	 * Returns the id.
	 *
	 * @return int
	 */
	public function getId() {

		return $this->getData('id');
	}


	/**
	 * @param null $field
	 *
	 * @return array|bool
	 */
	public function getData($field = null) {

		// TODO: throw exception when trying to access unset field!
		if (isset($field)) {
			if (!empty($this->data[$field])) {
				return $this->data[$field];
			}
			else {
				return false;
			}
		}

		return $this->data;
	}


	/**
	 * @param array $data
	 */
	public function setData(array $data) {

		foreach ($data as $key => $value) {
			if (isset($this->data[$key]) && $key != 'id') {
				$this->data[$key] = $value;
				// TODO: return true or false!
			}
		}
	}


	/**
	 * Returns the name.
	 *
	 * @return string
	 */
	public function getName() {

		return $this->getData('name');
	}

}
