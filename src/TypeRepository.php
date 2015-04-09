<?php


namespace publin\src;

class TypeRepository extends Repository {


	public function select() {

		$this->select = 'SELECT self.*';
		$this->from = 'FROM `types` self';

		return $this;
	}


	/**
	 * @return Type[]
	 */
	public function find() {

		$result = parent::find();
		$types = array();

		foreach ($result as $row) {
			$types[] = new Type($row);
		}

		return $types;
	}


	/**
	 * @return Type
	 */
	public function findSingle() {

		if ($result = parent::findSingle()) {
			return new Type($result);
		}
		else {
			return false;
		}
	}
}
