<?php


namespace publin\src;

/**
 * Class RoleRepository
 *
 * @package publin\src
 */
class RoleRepository extends Repository {


    public function reset()
    {
        parent::reset();
        $this->select = 'SELECT self.*';
        $this->from = 'FROM `roles` self';

        return $this;
    }


    /**
     * @param        $column
     * @param        $comparator
     * @param        $value
     * @param null   $function
     * @param string $table
     *
     * @return $this
     */
	public function where($column, $comparator, $value, $function = null, $table = 'self') {

		if ($column === 'user_id') {
			$table = 'users_roles';
			$this->join($table, 'role_id', '=', 'id');
		}

		parent::where($column, $comparator, $value, $function, $table);

        return $this;
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
				$role->setPermissions($repo->where('role_id', '=', $role->getId())->order('name', 'ASC')->find());
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

        $result = parent::findSingle();

		if ($result) {
			$role = new Role($result);

			if ($full === true) {
				$repo = new PermissionRepository($this->db);
				$role->setPermissions($repo->where('role_id', '=', $role->getId())->order('name', 'ASC')->find());
			}

			return $role;
		}
		else {
			return false;
		}
	}
}
