<?php


namespace publin\src;

class ManageModel {

	private $db;


	public function __construct(Database $db) {

		$this->db = $db;
	}


	public function getPermissions() {

		// TODO: create and use PermissionModel
		$query = 'SELECT `id`, `name` FROM list_permissions ORDER BY `name` ASC;';

		return $this->db->getData($query);
	}


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


	public function getRoles() {

		$model = new RoleModel($this->db);

		return $model->fetch(true);
	}


	public function getUsers() {

		$model = new UserModel($this->db);

		return $model->fetch(true);
	}
}
