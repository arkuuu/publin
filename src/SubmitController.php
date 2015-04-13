<?php

namespace publin\src;

use BadMethodCallException;
use Exception;
use publin\src\exceptions\DBDuplicateEntryException;
use publin\src\exceptions\PermissionRequiredException;

class SubmitController {

	private $db;
	private $auth;
	private $model;
	private $errors;


	public function __construct(PDODatabase $db, Auth $auth) {

		$this->db = $db;
		$this->auth = $auth;
		$this->model = new SubmitModel($db);
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
			$_SESSION['input_raw'] = $input;

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

		$publication_model = new PublicationModel($this->db);
		$validator = $publication_model->getValidator($input['type']);
		if ($validator->validate($input)) {
			$data = $validator->getSanitizedResult();
			$publication = new Publication($data);
		}
		else {
			$this->errors = array_merge($this->errors, $validator->getErrors());
		}

		$authors = array();
		$author_model = new AuthorModel($this->db);

		if (!empty($input['authors'])) {
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
		$keyword_model = new KeywordModel($this->db);
		if (!empty($input['keywords'])) {
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

		if (empty($this->errors) && isset($publication)) {

			//$this->db->beginTransaction(); TODO there is a deadlock when enabling transactions

			try {
				$publication_id = $publication_model->store($publication);

				$priority = 1;
				foreach ($authors as $author) {
					$author_id = $author_model->store($author);
					$publication_model->addAuthor($publication_id, $author_id, $priority);
					$priority++;
				}

				foreach ($keywords as $keyword) {
					$keyword_id = $keyword_model->store($keyword);
					$publication_model->addKeyword($publication_id, $keyword_id);
				}
				//$this->db->commitTransaction();
			}
			catch (DBDuplicateEntryException $e) {
				//$this->db->cancelTransaction();
				// TODO make single error messages for each case
				$this->errors[] = 'A publication with this name already exists or you tried to add the same author or keyword to this publication twice';

				return false;
			}
			catch (Exception $e) {
				//$this->db->cancelTransaction();
				throw $e;
			}

			$this->clearForm();

			Controller::redirect(Request::createUrl(array('p' => 'publication', 'id' => $publication_id)));

			return true;
		}
		else {

			return false;
		}
	}


	public function clearForm() {

		unset($_SESSION['input']);
		if (isset($_SESSION['input_raw'])) {
			unset($_SESSION['input_raw']);
		}

		return true;
	}
}
