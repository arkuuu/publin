<?php


namespace publin\src;

class FileRepository extends QueryBuilder {

	public function select() {

		$this->select = 'SELECT self.*';
		$this->from = 'FROM `files` self';

		return $this;
	}


	public function go() {

		$result = parent::go();
		$files = array();

		foreach ($result as $row) {
			$files[] = new File($row);
		}

		return $files;
	}
}
