<?php


namespace publin\src;

class ManageModel {

	private $old_db;
	private $db;


	public function __construct(Database $db) {

		$this->old_db = $db;
		$this->db = new PDODatabase();
	}


	public function getPermissions() {

		// TODO: create and use PermissionModel
		$query = 'SELECT `id`, `name` FROM list_permissions ORDER BY `name` ASC;';

		return $this->old_db->getData($query);
	}


	public function updatePermissions(array $input) {

		$roles = $this->getRoles();
		$model = new RoleModel($this->old_db);

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

		$repo = new RoleRepository($this->db);

		return $repo->select()->order('name', 'ASC')->find(true);
	}


	public function getUsers() {

		$repo = new UserRepository($this->db);

		return $repo->select()->order('name', 'ASC')->find(true);
	}
}
