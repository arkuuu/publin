<?php

namespace publin\src;

use BadMethodCallException;
use publin\src\exceptions\PermissionRequiredException;

class SubmitController {

	private $old_db;
	private $db;
	private $auth;
	private $model;
	private $errors;


	public function __construct(Database $db, Auth $auth) {

		$this->old_db = $db;
		$this->db = new PDODatabase();
		$this->auth = $auth;
		$this->model = new SubmitModel($this->db);
		$this->errors = array();

		if (!isset($_SESSION)) {
			session_start();
		}
	}


	/**
	 * @param Request $request
	 *
	 * @return string
	 * @throws \Exception
	 * @throws exceptions\NotFoundException
	 */
	public function run(Request $request) {

		if (!$this->auth->checkPermission(Auth::SUBMIT_PUBLICATION)) {
			throw new PermissionRequiredException(Auth::SUBMIT_PUBLICATION);
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

		if ($request->get('m') === 'form') {
			$view = new SubmitView($this->model, 'form', $this->errors);
		}
		else if ($request->get('m') === 'import') {
			$view = new SubmitView($this->model, 'import', $this->errors);
		}
		else {
			unset($_SESSION['input']);
			$view = new SubmitView($this->model, 'start', $this->errors);
		}

		return $view->display();
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool
	 */
	private function import(Request $request) {

		$input = Validator::sanitizeText($request->post('input'));
		$format = Validator::sanitizeText($request->post('format'));

		if ($input && $format) {
			$_SESSION['input'] = FormatHandler::import($input, $format);

			return true;
		}
		else {
			$this->errors[] = 'No input to import given';

			return false;
		}
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool
	 * @throws \Exception
	 */
	private function submit(Request $request) {

		$input = $this->model->formatPost($request->post());
		$_SESSION['input'] = $input;

		if (empty($input['type'])) {
			$this->errors[] = 'Publication type required';

			return false;
		}

		$authors = array();
		if (!empty($input['authors'])) {
			$author_model = new AuthorModel($this->db);
			$validator = $author_model->getValidator();
			foreach ($input['authors'] as $input_author) {
				if ($validator->validate($input_author)) {
					$data = $validator->getSanitizedResult();
					$authors[] = new Author($data);
				}
				else {
					$this->errors = array_merge($this->errors, $validator->getErrors());
				}
			}
		}
		if (empty($authors)) {
			$this->errors[] = 'At least one author is required';
		}

		$keywords = array();
		if (!empty($input['keywords'])) {
			$keyword_model = new KeywordModel($this->db);
			$validator = $keyword_model->getValidator();
			foreach ($input['keywords'] as $input_keyword) {
				if ($validator->validate(array('name' => $input_keyword))) {
					$data = $validator->getSanitizedResult();
					$keywords[] = new Keyword($data);
				}
				else {
					$this->errors = array_merge($this->errors, $validator->getErrors());
				}
			}
		}

		$publication_model = new PublicationModel($this->old_db);
		$validator = $publication_model->getValidator($input['type']);
		if ($validator->validate($input)) {
			$data = $validator->getSanitizedResult();
			$publication = new Publication($data, $authors, $keywords);
		}
		else {
			$this->errors = array_merge($this->errors, $validator->getErrors());
		}

		if (empty($this->errors) && isset($publication)) {
			$publication_id = $publication_model->store($publication);
			unset($_SESSION['input']);

			Controller::redirect('?publication&id='.$publication_id);

			return true;
		}
		else {

			return false;
		}
	}
}
