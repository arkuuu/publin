<?php


namespace publin\src;

class RoleRepository extends QueryBuilder {


	public function select() {

		$this->select = 'SELECT self.*';
		$this->from = 'FROM `list_roles` self';

		return $this;
	}


	public function where($column, $comparator, $value, $function = null) {

		if ($column === 'user_id') {
			$table = 'rel_user_roles';
			$this->join($table, 'role_id', '=', 'id');
		}
		else {
			$table = 'self';
		}

		return parent::where($column, $comparator, $value, $function, $table);
	}


	/**
	 * @return Role[]
	 */
	public function find() {

		$result = parent::find();
		$roles = array();

		foreach ($result as $row) {
			$roles[] = new Role($row);
		}

		return $roles;
	}


	/**
	 * @return Role
	 */
	public function findSingle() {

		$result = parent::findSingle();

		return new Role($result);
	}
}
