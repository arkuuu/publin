<?php


namespace publin\src;

class TypeRepository extends QueryBuilder {


	public function select() {

		$this->select = 'SELECT self.*';
		$this->from = 'FROM `list_types` self';

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

		$result = parent::findSingle();

		return new Type($result);
	}
}
