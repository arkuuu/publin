<?php

namespace publin\src;

class ManageController {

	private $db;


	public function __construct($db) {

		$this->db = $db;
	}


	public function run() {

		// TODO: input safety!!
		// TODO: check for user permission to this!

		if (isset($_GET['m'])) {
			$mode = $_GET['m'];

			if ($mode == 'rmp') {
				if (isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['rid']) && is_numeric($_GET['rid'])) {
					$permission_id = $_GET['id'];
					$role_id = $_GET['rid'];

					$model = new RoleModel($this->db);
					$success = $model->removePermission($role_id, $permission_id);
					var_dump($success);
				}
			}

			else if ($mode == 'rmr') {
				if (isset($_GET['id']) && is_numeric($_GET['id'])) {
					$role_id = $_GET['id'];

					$model = new RoleModel($this->db);
					$success = $model->delete($role_id);
					var_dump($success);
				}
			}
			if ($mode == 'rmur') {
				if (isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['uid']) && is_numeric($_GET['uid'])) {
					$role_id = $_GET['id'];
					$user_id = $_GET['uid'];

					$model = new UserModel($this->db);
					$success = $model->removeRole($user_id, $role_id);
					var_dump($success);
				}
			}
		}

		else if (isset($_POST['role_id']) && is_numeric($_POST['role_id']) && isset($_POST['permission_id']) &&
			is_numeric($_POST['permission_id'])
		) {
			$role_id = $_POST['role_id'];
			$permission_id = $_POST['permission_id'];

			$model = new RoleModel($this->db);
			$success = $model->addPermission($role_id, $permission_id);
			var_dump($success);
		}

		else if (isset($_POST['user_role_id']) && is_numeric($_POST['user_role_id']) && isset
			($_POST['user_id']) &&
			is_numeric($_POST['user_id'])
		) {
			$user_id = $_POST['user_id'];
			$role_id = $_POST['user_role_id'];

			$model = new UserModel($this->db);
			$success = $model->addRole($user_id, $role_id);
			var_dump($success);
		}

		else if (!empty($_POST['role_name'])) {
			$role_name = $_POST['role_name'];
			$role = new Role(array('name' => $role_name));
			$model = new RoleModel($this->db);
			$success = $model->store($role);
			var_dump($success);

		}

		else if (isset($_POST['role_perm'])) {
			$model = new ManageModel($this->db);
			$model->updatePermissions($_POST['role_perm']);
		}

		$model = new ManageModel($this->db);
		$view = new ManageView($model);

		return $view->display();
	}
}
