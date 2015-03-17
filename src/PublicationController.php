<?php


namespace publin\src;

use BadMethodCallException;
use Exception;
use InvalidArgumentException;
use publin\src\exceptions\FileHandlerException;
use publin\src\exceptions\PermissionRequiredException;

class PublicationController {

	private $db;
	private $auth;
	private $model;
	private $errors;


	public function __construct(Database $db, Auth $auth) {

		$this->db = $db;
		$this->auth = $auth;
		$this->model = new PublicationModel($db);
		$this->errors = array();
	}


	/**
	 * @param Request $request
	 *
	 * @return string
	 * @throws Exception
	 * @throws exceptions\NotFoundException
	 */
	public function run(Request $request) {

		if ($request->post('action')) {
			if (!$this->auth->checkPermission(Auth::EDIT_PUBLICATION)) {
				throw new PermissionRequiredException(Auth::EDIT_PUBLICATION);
			}
			$method = $request->post('action');
			if (method_exists($this, $method)) {
				$this->$method($request);
			}
			else {
				throw new BadMethodCallException;
			}
		}

		$publications = $this->model->fetch(true, array('id' => $request->get('id')));

		if ($request->get('m') === 'file') {
			$this->download($request);
		}

		if ($request->get('m') === 'edit') {
			$view = new PublicationView($publications[0], $this->errors, true);
		}
		else {
			$view = new PublicationView($publications[0], $this->errors);
		}

		return $view->display();
	}


	/**
	 * @param Request $request
	 *
	 * @return bool
	 * @throws PermissionRequiredException
	 * @throws exceptions\FileHandlerException
	 */
	public function download(Request $request) {

		if ($request->get('file')) {
			$file_model = new FileModel($this->db);
			$file = $file_model->fetchById($request->get('file'));

			if (!$file->isRestricted() || $this->auth->checkPermission(Auth::ACCESS_RESTRICTED_FILES)) {
				FileHandler::download($file->getName(), $file->getTitle());

				return true;
			}
			else {
				throw new PermissionRequiredException(Auth::ACCESS_RESTRICTED_FILES);
			}
		}
		else {
			throw new InvalidArgumentException;
		}
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool
	 */
	private function removeKeyword(Request $request) {

		if ($request->post('keyword_id') && $request->get('id')) {

			return $this->model->removeKeyword($request->get('id'), $request->post('keyword_id'));
		}
		else {
			throw new InvalidArgumentException;
		}
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool|mixed
	 */
	private function addKeyword(Request $request) {

		if ($request->get('id')) {

			$keyword_model = new KeywordModel($this->db);
			$validator = $keyword_model->getValidator();

			if ($validator->validate($request->post())) {
				$data = $validator->getSanitizedResult();
				$keyword = new Keyword($data);
				$keyword_id = $keyword_model->store($keyword);
				$this->model->addKeyword($request->get('id'), $keyword_id, 0);

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


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool
	 */
	private function removeAuthor(Request $request) {

		if ($request->post('author_id') && $request->get('id')) {

			return $this->model->removeAuthor($request->get('id'), $request->post('author_id'));
		}
		else {
			throw new InvalidArgumentException;
		}
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool|mixed
	 */
	private function addAuthor(Request $request) {

		if ($request->get('id')) {

			$author_model = new AuthorModel($this->db);
			$validator = $author_model->getValidator();

			if ($validator->validate($request->post())) {
				$data = $validator->getSanitizedResult();
				$author = new Author($data);
				$author_id = $author_model->store($author);
				// TODO: priority?
				$this->model->addAuthor($request->get('id'), $author_id, 0);

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


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool
	 */
	private function edit(Request $request) {

		if ($request->get('id') && $request->post('type')) {
			$validator = $this->model->getValidator($request->post('type'));

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
	 * @return bool
	 * @throws FileHandlerException
	 */
	private function addFile(Request $request) {

		if ($request->get('id') && isset($_FILES['file'])) {
			$file_model = new FileModel($this->db);
			$validator = $file_model->getValidator();

			if ($validator->validate($request->post())) {

				try {
					$file_name = FileHandler::upload($_FILES['file']);
					$data = $validator->getSanitizedResult();
					$data['name'] = $file_name;
					$file = new File($data);
					$file_model->store($file, $request->get('id'));

					return true;
				}
				catch (FileHandlerException $e) {
					$this->errors[] = $e->getMessage();

					return false;
				}
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


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool
	 */
	private function removeFile(Request $request) {

		if ($request->post('file_id')) {
			$file_model = new FileModel($this->db);
			$file = $file_model->fetchById($request->post('file_id'));
			FileHandler::delete($file->getName());

			return $file_model->delete($request->post('file_id'));
		}
		else {
			throw new InvalidArgumentException;
		}
	}
}
