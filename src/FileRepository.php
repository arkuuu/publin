<?php


namespace publin\src;

class FileRepository extends QueryBuilder {

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
	 * @return File
	 */
	public function findSingle() {

		$result = parent::findSingle();

		return new File($result);
	}
}
