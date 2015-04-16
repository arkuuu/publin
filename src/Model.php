<?php


namespace publin\src;

/**
 * Class Model
 *
 * @package publin\src
 */
class Model {

	/**
	 * @var Database
	 */
	protected $db;


	/**
	 * @param Database $db
	 */
	public function __construct(Database $db) {

		$this->db = $db;
	}
}
