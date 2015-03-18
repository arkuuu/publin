<?php


namespace publin\src;

use BadMethodCallException;
use Exception;
use publin\src\exceptions\FileHandlerException;
use publin\src\exceptions\PermissionRequiredException;
use UnexpectedValueException;

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

		$file_id = Validator::sanitizeNumber($request->get('file_id'));
		if (!$file_id) {
			throw new UnexpectedValueException;
		}

		$file_model = new FileModel($this->db);
		$file = $file_model->fetchById($file_id);

		if ($file->isHidden() && !$this->auth->checkPermission(Auth::ACCESS_HIDDEN_FILES)) {
			throw new PermissionRequiredException(Auth::ACCESS_HIDDEN_FILES);
		}
		if ($file->isRestricted() && !$this->auth->checkPermission(Auth::ACCESS_RESTRICTED_FILES)) {
			throw new PermissionRequiredException(Auth::ACCESS_RESTRICTED_FILES);
		}

		FileHandler::download($file->getName(), $file->getTitle());

		return true;
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool
	 */
	private function removeKeyword(Request $request) {

		$id = Validator::sanitizeNumber($request->get('id'));
		$keyword_id = Validator::sanitizeNumber($request->post('keyword_id'));
		if (!$id || !$keyword_id) {
			throw new UnexpectedValueException;
		}

		return $this->model->removeKeyword($id, $keyword_id);
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool|mixed
	 */
	private function addKeyword(Request $request) {

		$id = Validator::sanitizeNumber($request->get('id'));
		if (!$id) {
			throw new UnexpectedValueException;
		}

		$keyword_model = new KeywordModel($this->db);
		$validator = $keyword_model->getValidator();

		if ($validator->validate($request->post())) {
			$data = $validator->getSanitizedResult();
			$keyword = new Keyword($data);
			$keyword_id = $keyword_model->store($keyword);
			$this->model->addKeyword($id, $keyword_id, 0);

			return true;
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
	private function removeAuthor(Request $request) {

		$id = Validator::sanitizeNumber($request->get('id'));
		$author_id = Validator::sanitizeNumber($request->post('author_id'));
		if (!$id || !$author_id) {
			throw new UnexpectedValueException;
		}

		return $this->model->removeAuthor($id, $author_id);
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool|mixed
	 */
	private function addAuthor(Request $request) {

		$id = Validator::sanitizeNumber($request->get('id'));
		if (!$id) {
			throw new UnexpectedValueException;
		}

		$author_model = new AuthorModel($this->db);
		$validator = $author_model->getValidator();

		if ($validator->validate($request->post())) {
			$data = $validator->getSanitizedResult();
			$author = new Author($data);
			$author_id = $author_model->store($author);
			// TODO: priority?
			$this->model->addAuthor($id, $author_id, 0);

			return true;
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
	private function edit(Request $request) {

		$id = Validator::sanitizeNumber($request->get('id'));
		$type = Validator::sanitizeText($request->post('type'));
		if (!$id || !$type) {
			throw new UnexpectedValueException;
		}

		$validator = $this->model->getValidator($type);

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


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool
	 */
	private function delete(Request $request) {

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
	 * @return bool
	 * @throws FileHandlerException
	 */
	private function addFile(Request $request) {

		$id = Validator::sanitizeNumber($request->get('id'));
		$file_data = isset($_FILES['file']) ? (array)$_FILES['file'] : false;
		if (!$id || !$file_data) {
			throw new UnexpectedValueException;
		}

		$file_model = new FileModel($this->db);
		$validator = $file_model->getValidator();

		if ($validator->validate($request->post())) {
			try {
				$file_name = FileHandler::upload($file_data);
				$data = $validator->getSanitizedResult();
				$data['name'] = $file_name;
				$file = new File($data);
				$file_model->store($file, $id);

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


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool
	 */
	private function removeFile(Request $request) {

		$file_id = Validator::sanitizeNumber($request->get('file_id'));
		if (!$file_id) {
			throw new UnexpectedValueException;
		}

		$file_model = new FileModel($this->db);
		$file = $file_model->fetchById($file_id);
		FileHandler::delete($file->getName());

		return $file_model->delete($file_id);
	}
}
