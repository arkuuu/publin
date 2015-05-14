<?php


namespace publin\src;

/**
 * Class RoleRepository
 *
 * @package publin\src
 */
class RoleRepository extends Repository {


	/**
	 * @return $this
	 */
	public function select() {

		$this->select = 'SELECT self.*';
		$this->from = 'FROM `roles` self';

		return $this;
	}


	/**
	 * @param      $column
	 * @param      $comparator
	 * @param      $value
	 * @param null $function
	 *
	 * @return $this
	 */
	public function where($column, $comparator, $value, $function = null) {

		if ($column === 'user_id') {
			$table = 'users_roles';
			$this->join($table, 'role_id', '=', 'id');
		}
		else {
			$table = 'self';
		}

		return parent::where($column, $comparator, $value, $function, $table);
	}


	/**
	 * @param bool $full
	 *
	 * @return Role[]
	 */
	public function find($full = false) {

		$result = parent::find();
		$roles = array();

		foreach ($result as $row) {
			$role = new Role($row);

			if ($full === true) {
				$repo = new PermissionRepository($this->db);
				$role->setPermissions($repo->select()->where('role_id', '=', $role->getId())->order('name', 'ASC')->find());
			}
			$roles[] = $role;
		}

		return $roles;
	}


	/**
	 * @param bool $full
	 *
	 * @return Role|false
	 */
	public function findSingle($full = false) {

		if ($result = parent::findSingle()) {
			$role = new Role($result);

			if ($full === true) {
				$repo = new PermissionRepository($this->db);
				$role->setPermissions($repo->select()->where('role_id', '=', $role->getId())->order('name', 'ASC')->find());
			}

			return $role;
		}
		else {
			return false;
		}
	}
}
