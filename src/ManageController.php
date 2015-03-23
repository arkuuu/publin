<?php

namespace publin\src;

use BadMethodCallException;
use publin\src\exceptions\PermissionRequiredException;
use UnexpectedValueException;

class ManageController {

	private $db;
	private $auth;
	private $errors;


	public function __construct(Database $db, Auth $auth) {

		$this->db = $db;
		$this->auth = $auth;
		$this->errors = array();
	}


	public function run(Request $request) {

		if (!$this->auth->checkPermission(Auth::MANAGE)) {
			throw new PermissionRequiredException(Auth::MANAGE);
		}

		if ($request->post('action')) {
			$method = $request->post('action');
			if (method_exists($this, $method)) {
				$this->$method($request);
			}
			else {
				throw new BadMethodCallException;
			}
		}

		$model = new ManageModel($this->db);
		$view = new ManageView($model, $this->errors);

		return $view->display();
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool
	 */
//	private function removePermissionFromRole(Request $request) {
//
//		$role_id = Validator::sanitizeNumber($request->post('role_id'));
//		$permission_id = Validator::sanitizeNumber($request->post('permission_id'));
//		if (!$role_id || !$permission_id) {
//			throw new UnexpectedValueException;
//		}
//
//		$model = new RoleModel($this->db);
//
//		return $model->removePermission($role_id, $permission_id);
//	}

	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool
	 */
	private function deleteRole(Request $request) {

		$role_id = Validator::sanitizeNumber($request->post('role_id'));
		if (!$role_id) {
			throw new UnexpectedValueException;
		}

		$model = new RoleModel($this->db);

		return $model->delete($role_id);
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool
	 */
	private function removeRoleFromUser(Request $request) {

		$user_id = Validator::sanitizeNumber($request->post('user_id'));
		$role_id = Validator::sanitizeNumber($request->post('role_id'));
		if (!$user_id || !$role_id) {
			throw new UnexpectedValueException;
		}
		$model = new UserModel($this->db);

		return $model->removeRole($user_id, $role_id);
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return mixed
	 */
//	private function addPermissionToRole(Request $request) {
//
//		$role_id = Validator::sanitizeNumber($request->post('role_id'));
//		$permission_id = Validator::sanitizeNumber($request->post('permission_id'));
//		if (!$role_id || !$permission_id) {
//			throw new UnexpectedValueException;
//		}
//
//		$model = new RoleModel($this->db);
//
//		return $model->addPermission($role_id, $permission_id);
//	}

	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return mixed
	 */
	private function addRoleToUser(Request $request) {

		$user_id = Validator::sanitizeNumber($request->post('user_id'));
		$role_id = Validator::sanitizeNumber($request->post('role_id'));
		if (!$user_id || !$role_id) {
			throw new UnexpectedValueException;
		}

		$model = new UserModel($this->db);

		return $model->addRole($user_id, $role_id);
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return mixed
	 */
	private function addRole(Request $request) {

		$role_model = new RoleModel($this->db);
		$validator = $role_model->getValidator();

		if ($validator->validate($request->post())) {
			$data = $validator->getSanitizedResult();
			$keyword = new Role($data);

			return $role_model->store($keyword);
		}
		else {
			$this->errors = array_merge($this->errors, $validator->getErrors());

			return false;
		}
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool
	 */
	private function updatePermissions(Request $request) {

		$permissions = $request->post('permissions');
		if (!is_array($permissions)) {
			throw new UnexpectedValueException;
		}

		$model = new ManageModel($this->db);

		return $model->updatePermissions($permissions);
	}
}
