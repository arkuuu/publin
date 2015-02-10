<?php

namespace publin\src;

use Exception;

class ManageController {

	private $db;


	public function __construct($db) {

		$this->db = $db;
	}


	public function run() {

		// TODO: input safety!!
		// TODO: check for user permission to this!

		try {
			if (isset($_GET['m'])) {
				$mode = $_GET['m'];

				if ($mode == 'rmp' && isset($_GET['id']) && isset($_GET['rid'])) {
					$success = $this->removePermissionFromRole($_GET['rid'], $_GET['id']);
				}

				else if ($mode == 'rmr' && isset($_GET['id'])) {
					$success = $this->deleteRole($_GET['id']);
				}
				if ($mode == 'rmur' && isset($_GET['id']) && isset($_GET['uid'])) {
					$success = $this->removeRoleFromUser($_GET['uid'], $_GET['id']);
				}
			}

			else if (isset($_POST['role_id']) && isset($_POST['permission_id'])) {
				$success = $this->addPermissionToRole($_POST['role_id'], $_POST['permission_id']);
			}

			else if (isset($_POST['role_id']) && isset($_POST['user_id'])) {
				$success = $this->addRoleToUser($_POST['user_id'], $_POST['role_id']);
			}

			else if (!empty($_POST['role_name'])) {
				$success = $this->newRole($_POST['role_name']);
			}

			else if (isset($_POST['role_perm'])) {
				$success = $this->updateRolePermissions($_POST['role_perm']);
			}
			else {
				$success = null;
			}
		} catch (Exception $e) {
			print_r($e->getMessage());
			$success = false;
		}

		var_dump($success);
		$model = new ManageModel($this->db);
		$view = new ManageView($model, $success);

		return $view->display();
	}


	public function removePermissionFromRole($role_id, $permission_id) {

		$model = new RoleModel($this->db);

		return $model->removePermission($role_id, $permission_id);
	}


	public function deleteRole($role_id) {

		$model = new RoleModel($this->db);

		return $model->delete($role_id);
	}


	public function removeRoleFromUser($user_id, $role_id) {

		$model = new UserModel($this->db);

		return $model->removeRole($user_id, $role_id);
	}


	public function addPermissionToRole($role_id, $permission_id) {

		$model = new RoleModel($this->db);

		return $model->addPermission($role_id, $permission_id);
	}


	public function addRoleToUser($user_id, $role_id) {

		$model = new UserModel($this->db);

		return $model->addRole($user_id, $role_id);
	}


	public function newRole($role_name) {

		$model = new RoleModel($this->db);
		$role = new Role(array('name' => $role_name));

		return $model->store($role);
	}


	public function updateRolePermissions(array $role_permissions) {

		$model = new ManageModel($this->db);

		return $model->updatePermissions($role_permissions);
	}
}
