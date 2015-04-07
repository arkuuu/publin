<?php


namespace publin\src;

use BadMethodCallException;
use publin\src\exceptions\PermissionRequiredException;
use UnexpectedValueException;

class AuthorController {

	private $db;
	private $auth;
	private $model;
	private $errors;


	public function __construct(PDODatabase $db, Auth $auth) {

		$this->db = $db;
		$this->auth = $auth;
		$this->model = new AuthorModel($this->db);
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
			$method = $request->post('action');
			if (method_exists($this, $method)) {
				$this->$method($request);
			}
			else {
				throw new BadMethodCallException;
			}
		}

		$repo = new AuthorRepository($this->db);
		$author = $repo->select()->where('id', '=', $request->get('id'))->findSingle();

		$repo = new PublicationRepository($this->db);
		$publications = $repo->select()->where('author_id', '=', $request->get('id'))->order('date_published', 'DESC')->find();

		if ($request->get('m') === 'edit') {
			$view = new AuthorView($author, $publications, $this->errors, true);
		}
		else {
			$view = new AuthorView($author, $publications, $this->errors);
		}

		return $view->display();
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool
	 * @throws PermissionRequiredException
	 * @throws exceptions\LoginRequiredException
	 */
	private function delete(Request $request) {

		if (!$this->auth->checkPermission(Auth::DELETE_AUTHOR)) {
			throw new PermissionRequiredException(Auth::DELETE_AUTHOR);
		}

		$id = Validator::sanitizeNumber($request->get('id'));
		if (!$id) {
			throw new UnexpectedValueException;
		}

		$confirmed = Validator::sanitizeBoolean($request->post('delete'));
		if ($confirmed) {
			$this->model->delete($id);

			return true;
		}
		else {
			$this->errors[] = 'Please confirm the deletion';

			return false;
		}
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool|int
	 * @throws PermissionRequiredException
	 * @throws exceptions\LoginRequiredException
	 */
	private function edit(Request $request) {

		if (!$this->auth->checkPermission(Auth::EDIT_AUTHOR)) {
			throw new PermissionRequiredException(Auth::EDIT_AUTHOR);
		}

		$id = Validator::sanitizeNumber($request->get('id'));
		if (!$id) {
			throw new UnexpectedValueException;
		}

		$validator = $this->model->getValidator();
		if ($validator->validate($request->post())) {
			$input = $validator->getSanitizedResult();
			$this->model->update($id, $input);

			return true;
		}
		else {
			$this->errors = array_merge($this->errors, $validator->getErrors());

			return false;
		}
	}
}
