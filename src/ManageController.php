<?php

namespace publin\src;

use Exception;

class ManageController {

	private $db;


	public function __construct($db) {

		$this->db = $db;
	}


	public function run(Request $request) {

		// TODO: input safety!!
		// TODO: use Request
		// TODO: check for user permission to this!

		try {
			if ($request->get('m')) {
				$mode = $request->get('m');

				if ($mode == 'rmp' && $request->get('id') && $request->get('rid')) {
					$success = $this->removePermissionFromRole($request->get('rid'), $request->get('id'));
				}

				else if ($mode == 'rmr' && $request->get('id')) {
					$success = $this->deleteRole($request->get('id'));
				}
				if ($mode == 'rmur' && $request->get('id') && $request->get('uid')) {
					$success = $this->removeRoleFromUser($request->get('uid'), $request->get('id'));
				}
			}

			else if ($request->post('role_id') && $request->post('permission_id')) {
				$success = $this->addPermissionToRole($request->post('role_id'), $request->post('permission_id'));
			}

			else if ($request->post('role_id') && $request->post('user_id')) {
				$success = $this->addRoleToUser($request->post('user_id'), $request->post('role_id'));
			}

			else if ($request->post('role_name')) {
				$success = $this->newRole($request->post('role_name'));
			}

			else if ($request->post('role_perm')) {
				$success = $this->updateRolePermissions($request->post('role_perm'));
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
