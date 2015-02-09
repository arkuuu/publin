<?php


namespace publin\src;

class ManageModel {

	private $db;


	public function __construct(Database $db) {

		$this->db = $db;
	}


	public function getPermissions() {

		$query = 'SELECT `id`, `name` FROM list_permissions ORDER BY `name` ASC;';
		$permissions = $this->db->getData($query);

		return $permissions;
	}


	public function updatePermissions(array $input) {

		$roles = $this->getRoles();
		foreach ($roles as $role) {
			$old_permissions = $role->getPermissions();
			$old = array();

			foreach ($old_permissions as $old_permission) {
				$old[] = $old_permission['id'];
			}

			if (isset($input[$role->getId()])) {
				$new_permissions = $input[$role->getId()];
				$new = array_keys($new_permissions);

				$to_delete = array_diff($old, $new);
				$to_add = array_diff($new, $old);
			}
			else {
				$to_add = array();
				$to_delete = $old;
			}

			$model = new RoleModel($this->db);
			foreach ($to_add as $id) {
				$success = $model->addPermission($role->getId(), $id);
				var_dump($success);
			}
			foreach ($to_delete as $id) {
				$success = $model->removePermission($role->getId(), $id);
				var_dump($success);
			}
		}


	}


	public function getRoles() {

		$model = new RoleModel($this->db);
		$roles = $model->fetch(true);

		return $roles;
	}


	public function getUsers() {

		$model = new UserModel($this->db);
		$users = $model->fetch(true);

		return $users;
	}
}
