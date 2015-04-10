<?php

namespace publin\src;

use BadMethodCallException;
use publin\src\exceptions\DBDuplicateEntryException;
use publin\src\exceptions\DBForeignKeyException;
use publin\src\exceptions\PermissionRequiredException;
use UnexpectedValueException;

class ManageController {

	private $db;
	private $auth;
	private $errors;


	public function __construct(PDODatabase $db, Auth $auth) {

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
	private function deleteRole(Request $request) {

		$role_id = Validator::sanitizeNumber($request->post('role_id'));
		if (!$role_id) {
			throw new UnexpectedValueException;
		}

		$model = new RoleModel($this->db);

		try {
			return $model->delete($role_id);
		}
		catch (DBForeignKeyException $e) {
			$this->errors[] = 'This role is assigned to a user or permission and cannot be deleted.';

			return false;
		}
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
			$role = new Role($data);

			try {
				return $role_model->store($role);
			}
			catch (DBDuplicateEntryException $e) {
				$this->errors[] = 'This role name is already in use, please choose another one';

				return false;
			}
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


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool|mixed
	 */
	private function registerUser(Request $request) {

		$user_model = new UserModel($this->db);
		$validator = $user_model->getValidator();

		if ($validator->validate($request->post())) {
			$data = $validator->getSanitizedResult();
			$user = new User($data);

			try {
				$user_model->store($user);
				$password = $this->auth->generatePassword();
				print_r('new pw is '.$password); // TODO remove this
				$this->auth->setPassword($user->getName(), $password);

				$subject = 'An account at '.Config::ROOT_URL.' was created for you!';
				$message = 'Greetings,'."\n\n".$this->auth->getCurrentUser()->getName().' created an account for you at '.Request::createUrl().'.'."\n\n";
				$message .= 'Username: '.$user->getName()."\n";
				$message .= 'Mail: '.$user->getMail()."\n";
				$message .= 'Temporary password: '.$password."\n";
				$message .= "\n".'Please change your password soon at '.Request::createUrl(array('p' => 'user')).'!';

				if (MailHandler::sendMail($user->getMail(), $subject, $message)) {
					return true;
				}
				else {
					$this->errors[] = 'The mail to the user could not be sent';
				}
			}
			catch (DBDuplicateEntryException $e) {
				$this->errors[] = 'This username or email is already in use, please choose another one';

				return false;
			}
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
	private function sendNewPassword(Request $request) {

		$user_id = Validator::sanitizeText($request->post('user_id'));
		if (!$user_id) {
			throw new UnexpectedValueException();
		}

		$repo = new UserRepository($this->db);
		$user = $repo->select()->where('id', '=', $user_id)->findSingle();

		$password = $this->auth->generatePassword();
		print_r('new pw is '.$password); // TODO remove this

		$this->auth->setPassword($user->getName(), $password);

		$subject = 'Your password at '.Request::createUrl().' was reset!';
		$message = 'Greetings,'."\n\n".$this->auth->getCurrentUser()->getName().' has reset your password for you at '.Request::createUrl().'.'."\n\n";
		$message .= 'The new temporary password is: '.$password."\n";
		$message .= "\n".'Please change your password soon at '.Request::createUrl(array('p' => 'user')).'!';

		if (MailHandler::sendMail($user->getMail(), $subject, $message)) {
			return true;
		}
		else {
			$this->errors[] = 'The mail to the user could not be sent';
		}

	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool
	 */
	private function deleteUser(Request $request) {

		$user_id = Validator::sanitizeNumber($request->post('user_id'));
		if (!$user_id) {
			throw new UnexpectedValueException;
		}

		$model = new UserModel($this->db);

		return $model->delete($user_id);
	}
}
