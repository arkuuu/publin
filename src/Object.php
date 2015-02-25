<?php

namespace publin\src;

use InvalidArgumentException;

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
	 * @param string $field
	 *
	 * @return array|bool|string
	 */
	public function getData($field = '') {

		if (!empty($field)) {
			if (array_key_exists($field, $this->data)) {
				if (!empty($this->data[$field])) {
					return $this->data[$field];
				}
				else {
					return false;
				}
			}
			else {
				// TODO: remove this in production use
				throw new InvalidArgumentException('field "'.$field.'" does not exist in this class');
			}
		}
		else {
			return $this->data;
		}
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
