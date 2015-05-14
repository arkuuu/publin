<?php


namespace publin\src;

/**
 * Class FileRepository
 *
 * @package publin\src
 */
class FileRepository extends Repository {

	/**
	 * @return $this
	 */
	public function select() {

		$this->select = 'SELECT self.*';
		$this->from = 'FROM `files` self';

		return $this;
	}


	/**
	 * @return File[]
	 */
	public function find() {

		$result = parent::find();
		$files = array();

		foreach ($result as $row) {
			$files[] = new File($row);
		}

		return $files;
	}


	/**
	 * @return File|false
	 */
	public function findSingle() {

		if ($result = parent::findSingle()) {
			return new File($result);
		}
		else {
			return false;
		}
	}
}
