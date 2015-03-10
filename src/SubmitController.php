<?php

namespace publin\src;

class SubmitController {

	private $db;
	private $model;
	private $errors;


	public function __construct($db) {

		$this->db = $db;
		$this->model = new SubmitModel($this->db);

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

		if ($request->post('action')) {
			$method = $request->post('action');
			if (method_exists($this, $method)) {
				$this->$method($request);
			}
		}

		if ($request->get('m') === 'form') {
			$view = new SubmitView($this->model, 'form');
		}
		else if ($request->get('m') === 'import') {
			$view = new SubmitView($this->model, 'import');
		}
		else {
			unset($_SESSION['input']);
			$view = new SubmitView($this->model, 'start');
		}

		return $view->display();
	}


	public function import(Request $request) {

		if ($request->post('format') && $request->post('input')) {
			$_SESSION['input'] = FormatHandler::import($request->post('input'), $request->post('format'));

			return true;
		}
		else {
			return false;
		}
	}


	public function submit(Request $request) {

		if ($request->post()) {
			$input = $this->model->formatPost($request->post());
			$_SESSION['input'] = $input;

			if (!empty($input['type'])) {

				$authors = array();
				$author_model = new AuthorModel($this->db);
				$validator = $author_model->getValidator();
				foreach ($input['authors'] as $input_author) {
					if ($validator->validate($input_author)) {
						$data = $validator->getSanitizedResult();
						$authors[] = new Author($data);
					}
					else {
						$this->errors[] = $validator->getErrors();
					}
				}

				$keywords = array();
				$keyword_model = new KeywordModel($this->db);
				$validator = $keyword_model->getValidator();
				foreach ($input['keywords'] as $input_keyword) {
					if ($validator->validate(array('name' => $input_keyword))) {
						$data = $validator->getSanitizedResult();
						$keywords[] = new Keyword($data);
					}
					else {
						$this->errors[] = $validator->getErrors();
					}
				}

				$publication_model = new PublicationModel($this->db);
				$validator = $publication_model->getValidator($input['type']);
				if ($validator->validate($input)) {
					$data = $validator->getSanitizedResult();
					$publication = new Publication($data, $authors, $keywords);
				}
				else {
					$this->errors[] = $validator->getErrors();
				}

				if (empty($this->errors) && isset($publication)) {
					$publication_model->store($publication);
					print_r('SUCCESS');

					unset($_SESSION['input']);

					// TODO: header(...)
					return true;
				}
				else {
					print_r($this->errors);
					print_r('FAILURE');

					return false;
				}
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
}
