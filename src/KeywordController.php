<?php


namespace publin\src;

use BadMethodCallException;
use InvalidArgumentException;
use publin\src\exceptions\PermissionRequiredException;

class KeywordController {

	private $db;
	private $auth;
	private $model;
	private $errors;


	public function __construct(Database $db, Auth $auth) {

		$this->db = $db;
		$this->auth = $auth;
		$this->model = new KeywordModel($db);
		$this->errors = array();
	}


	/**
	 * @param Request $request
	 *
	 * @return string
	 * @throws PermissionRequiredException
	 * @throws \Exception
	 * @throws exceptions\LoginRequiredException
	 * @throws exceptions\NotFoundException
	 */
	public function run(Request $request) {

		if ($request->post('action')) {
			if (!$this->auth->checkPermission(Auth::EDIT_KEYWORD)) {
				throw new PermissionRequiredException(Auth::EDIT_KEYWORD);
			}
			$method = $request->post('action');
			if (method_exists($this, $method)) {
				$this->$method($request);
			}
			else {
				throw new BadMethodCallException;
			}
		}

		$keywords = $this->model->fetch(true, array('id' => $request->get('id')));

		if ($request->get('m') === 'edit') {
			$view = new KeywordView($keywords[0], $this->errors, true);
		}
		else {
			$view = new KeywordView($keywords[0], $this->errors);
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
