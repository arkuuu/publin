<?php


namespace publin\src;

use BadMethodCallException;
use InvalidArgumentException;
use publin\src\exceptions\PermissionRequiredException;

class AuthorController {

	private $db;
	private $auth;
	private $model;
	private $errors;


	public function __construct(Database $db, Auth $auth) {

		$this->db = $db;
		$this->auth = $auth;
		$this->model = new AuthorModel($db);
		$this->errors = array();
	}


	/**
	 * @param Request $request
	 *
	 * @return string
	 * @throws \Exception
	 * @throws exceptions\NotFoundException
	 */
	public function run(Request $request) {

		if ($request->post('action')) {
			if (!$this->auth->checkPermission(Auth::EDIT_AUTHOR)) {
				throw new PermissionRequiredException(Auth::EDIT_AUTHOR);
			}
			$method = $request->post('action');
			if (method_exists($this, $method)) {
				$this->$method($request);
			}
			else {
				throw new BadMethodCallException;
			}
		}

		$authors = $this->model->fetch(true, array('id' => $request->get('id')));

		if ($request->get('m') === 'edit') {
			$view = new AuthorView($authors[0], $this->errors, true);
		}
		else {
			$view = new AuthorView($authors[0], $this->errors);
		}

		return $view->display();
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool
	 */
	private function delete(Request $request) {

		if ($request->get('id')) {
			$confirmed = Validator::sanitizeBoolean($request->post('delete'));
			if ($confirmed) {
				$this->model->delete($request->get('id'));

				return true;
			}
			else {
				$this->errors[] = 'Please confirm the deletion';
			}
		}
		else {
			throw new InvalidArgumentException;
		}
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool|int
	 */
	private function edit(Request $request) {

		if ($request->get('id')) {
			$validator = $this->model->getValidator();

			if ($validator->validate($request->post())) {
				$input = $validator->getSanitizedResult();
				$this->model->update($request->get('id'), $input);

				return true;
			}
			else {
				$this->errors = array_merge($this->errors, $validator->getErrors());

				return false;
			}
		}
		else {
			throw new InvalidArgumentException;
		}
	}
}
