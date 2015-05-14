<?php


namespace publin\src;

/**
 * Class ManageModel
 *
 * @package publin\src
 */
class ManageModel extends Model {


	/**
	 * @return Permission[]
	 */
	public function getPermissions() {

		$repo = new PermissionRepository($this->db);

		return $repo->select()->order('name', 'ASC')->find();
	}


	/**
	 * @param array $input
	 *
	 * @return bool
	 */
	public function updatePermissions(array $input) {

		$roles = $this->getRoles();
		$model = new RoleModel($this->db);

		foreach ($roles as $role) {
			if (isset($input[$role->getId()])) {
				$permissions = array_keys($input[$role->getId()]);
				$model->updatePermissions($role->getId(), $permissions);
			}
			else {
				$model->updatePermissions($role->getId(), array());
			}
		}

		return true;
	}


	/**
	 * @return Role[]
	 */
	public function getRoles() {

		$repo = new RoleRepository($this->db);

		return $repo->select()->order('name', 'ASC')->find(true);
	}


	/**
	 * @return User[]
	 */
	public function getUsers() {

		$repo = new UserRepository($this->db);

		return $repo->select()->order('name', 'ASC')->find(true);
	}
}
