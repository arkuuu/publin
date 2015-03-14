<?php


namespace publin\src;

use Exception;
use InvalidArgumentException;
use publin\src\exceptions\PermissionRequiredException;

class PublicationController {

	private $db;
	private $auth;
	private $model;


	public function __construct(Database $db, Auth $auth) {

		$this->db = $db;
		$this->auth = $auth;
		$this->model = new PublicationModel($db);
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
			$method = $request->post('action');
			if (method_exists($this, $method)) {
				$this->$method($request);
			}
		}

		$publications = $this->model->fetch(true, array('id' => $request->get('id')));

		if ($request->get('m') === 'file') {
			$this->download($request);
		}

		if ($request->get('m') === 'edit') {
			$view = new PublicationView($publications[0], true);
		}
		else {
			$view = new PublicationView($publications[0]);
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
	private function download(Request $request) {

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
			return false;
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
			print_r('ERROR');

			return false;
		}
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool|mixed
	 */
	private function addKeyword(Request $request) {

		$keyword_model = new KeywordModel($this->db);
		$validator = $keyword_model->getValidator();

		if ($validator->validate($request->post())) {
			$data = $validator->getSanitizedResult();
			$keyword = new Keyword($data);
			$keyword_id = $keyword_model->store($keyword);

			if ($keyword_id && $request->get('id')) {
				// TODO: priority?
				return $this->model->addKeyword($request->get('id'), $keyword_id, 0);
			}
			else {
				print_r('ERROR');

				return false;
			}
		}
		else {
			print_r($validator->getErrors());

			return false;
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
			print_r('ERROR');

			return false;
		}
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool|mixed
	 */
	private function addAuthor(Request $request) {

		$author_model = new AuthorModel($this->db);
		$validator = $author_model->getValidator();

		if ($validator->validate($request->post())) {
			$data = $validator->getSanitizedResult();
			$author = new Author($data);
			$author_id = $author_model->store($author);

			if ($author_id && $request->get('id')) {
				// TODO: priority?
				return $this->model->addAuthor($request->get('id'), $author_id, 0);
			}
			else {
				print_r('ERROR');

				return false;
			}
		}
		else {
			print_r($validator->getErrors());

			return false;
		}
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool
	 */
	private function edit(Request $request) {

		if ($request->post() && $request->post('type')) {
			$validator = $this->model->getValidator($request->post('type'));

			if ($validator->validate($request->post())) {
				$input = $validator->getSanitizedResult();
				$success = $this->model->update($request->get('id'), $input);
				print_r($success);

				return true;
			}
			else {
				print_r($validator->getErrors());

				return false;
			}
		}
		else {
			return false;
		}
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool
	 */
	private function delete(Request $request) {

		if ($request->post('delete') == 'yes' && $request->get('id')) {
			return $this->model->delete($request->get('id'));
		}
		else {
			return false;
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

			return $file_model->delete($file->getId());
		}
		else {
			throw new InvalidArgumentException();
		}
	}


	private function addFile(Request $request) {
	}
}
