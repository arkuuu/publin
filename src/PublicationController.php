<?php


namespace publin\src;

use BadMethodCallException;
use Exception;
use publin\src\exceptions\DBDuplicateEntryException;
use publin\src\exceptions\DBForeignKeyException;
use publin\src\exceptions\FileHandlerException;
use publin\src\exceptions\FileNotFoundException;
use publin\src\exceptions\NotFoundException;
use publin\src\exceptions\PermissionRequiredException;
use UnexpectedValueException;

/**
 * Class PublicationController
 *
 * @package publin\src
 */
class PublicationController extends Controller {

	private $db;
	private $auth;
	private $model;
	private $errors;


	/**
	 * @param Database $db
	 * @param Auth     $auth
	 */
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
			$method = $request->post('action');
			if (method_exists($this, $method)) {
				$this->$method($request);
			}
			else {
				throw new BadMethodCallException;
			}
		}

		if ($request->get('file_id')) {
			$this->download($request);
			exit;
		}

		$repo = new PublicationRepository($this->db);
		$publication = $repo->where('id', '=', $request->get('id'))->findSingle(true);
		if (!$publication) {
			throw new NotFoundException('publication not found');
		}

		if ($request->get('m') === 'edit') {
			// To add a citation all publication names are required
			$repo = new PublicationRepository($this->db);
			$all_publications = $repo->order('title', 'ASC')->find();

			$view = new PublicationView($publication, $this->errors, true, $all_publications);
		}
		else {
			$view = new PublicationView($publication, $this->errors);
		}

		return $view->display();
	}


	/**
	 * @param Request $request
	 *
	 * @return bool
	 * @throws FileHandlerException
	 * @throws NotFoundException
	 * @throws PermissionRequiredException
	 */
	public function download(Request $request) {

		$file_id = Validator::sanitizeNumber($request->get('file_id'));
		if (!$file_id) {
			throw new UnexpectedValueException;
		}

		$repo = new FileRepository($this->db);
		$file = $repo->where('id', '=', $file_id)->findSingle();
		if (!$file) {
			throw new NotFoundException('file not found');
		}

		if ($file->isHidden() && !$this->auth->checkPermission(Auth::ACCESS_HIDDEN_FILES)) {
			throw new PermissionRequiredException(Auth::ACCESS_HIDDEN_FILES);
		}
		if ($file->isRestricted() && !$this->auth->checkPermission(Auth::ACCESS_RESTRICTED_FILES)) {
			throw new PermissionRequiredException(Auth::ACCESS_RESTRICTED_FILES);
		}

		try {
			FileHandler::download($file->getName(), $file->getTitle().$file->getExtension());
		}
		catch (FileNotFoundException $e) {
			throw new NotFoundException('file not found');
		}

		return true;
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool
	 * @throws PermissionRequiredException
	 * @throws exceptions\LoginRequiredException
	 */
	private function removeKeyword(Request $request) {

		if (!$this->auth->checkPermission(Auth::EDIT_PUBLICATION)) {
			throw new PermissionRequiredException(Auth::EDIT_PUBLICATION);
		}

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
	 * @throws PermissionRequiredException
	 * @throws exceptions\LoginRequiredException
	 */
	private function addKeyword(Request $request) {

		if (!$this->auth->checkPermission(Auth::EDIT_PUBLICATION)) {
			throw new PermissionRequiredException(Auth::EDIT_PUBLICATION);
		}

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
			try {
				$this->model->addKeyword($id, $keyword_id, 0);

				return true;
			}
			catch (DBDuplicateEntryException $e) {
				$this->errors[] = 'This keyword is already assigned to this publication';

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
	 * @throws PermissionRequiredException
	 * @throws exceptions\LoginRequiredException
	 */
	private function removeAuthor(Request $request) {

		if (!$this->auth->checkPermission(Auth::EDIT_PUBLICATION)) {
			throw new PermissionRequiredException(Auth::EDIT_PUBLICATION);
		}

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
	 * @return bool
	 * @throws PermissionRequiredException
	 * @throws exceptions\LoginRequiredException
	 */
	private function removeCitation(Request $request) {

		if (!$this->auth->checkPermission(Auth::EDIT_PUBLICATION)) {
			throw new PermissionRequiredException(Auth::EDIT_PUBLICATION);
		}

		$id = Validator::sanitizeNumber($request->get('id'));
		$citation_id = Validator::sanitizeNumber($request->post('citation_id'));
		if (!$id || !$citation_id) {
			throw new UnexpectedValueException;
		}

		return $this->model->removeCitation($id, $citation_id);
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool|mixed
	 * @throws PermissionRequiredException
	 * @throws exceptions\LoginRequiredException
	 */
	private function addAuthor(Request $request) {

		if (!$this->auth->checkPermission(Auth::EDIT_PUBLICATION)) {
			throw new PermissionRequiredException(Auth::EDIT_PUBLICATION);
		}

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
			try {
				$this->model->addAuthor($id, $author_id, 0);

				return true;
			}
			catch (DBDuplicateEntryException $e) {
				$this->errors[] = 'This author is already assigned to this publication';

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
	 * @return bool|mixed
	 * @throws PermissionRequiredException
	 * @throws exceptions\LoginRequiredException
	 */
	private function addCitation(Request $request) {

		if (!$this->auth->checkPermission(Auth::EDIT_PUBLICATION)) {
			throw new PermissionRequiredException(Auth::EDIT_PUBLICATION);
		}

		// Here I did not used a CitationModel validator, since this would
		// require publication_id to be present
		$id = Validator::sanitizeNumber($request->get('id'));
		if (!$id) {
			throw new UnexpectedValueException;
		}

		$validator = new Validator($this->db);
		$validator->addRule('citation_id', 'number', true, 'Citation is required but invalid');

		if ($validator->validate($request->post())) {
			$data = $validator->getSanitizedResult();
			try {
				$this->model->addCitation($id, $data['citation_id']);

				return true;
			}
			catch (DBDuplicateEntryException $e) {
				$this->errors[] = 'This citation is already assigned to this publication';

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
	 * @throws PermissionRequiredException
	 * @throws exceptions\LoginRequiredException
	 */
	private function edit(Request $request) {

		if (!$this->auth->checkPermission(Auth::EDIT_PUBLICATION)) {
			throw new PermissionRequiredException(Auth::EDIT_PUBLICATION);
		}

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
	 * @throws PermissionRequiredException
	 * @throws exceptions\LoginRequiredException
	 */
	private function delete(Request $request) {

		if (!$this->auth->checkPermission(Auth::DELETE_PUBLICATION)) {
			throw new PermissionRequiredException(Auth::DELETE_PUBLICATION);
		}

		$id = Validator::sanitizeNumber($request->get('id'));
		if (!$id) {
			throw new UnexpectedValueException;
		}

		$confirmed = Validator::sanitizeBoolean($request->post('delete'));
		if (!$confirmed) {
			$this->errors[] = 'Please confirm the deletion';

			return false;
		}

		try {
			$this->model->delete($id);
			$this->redirect(Request::createUrl(array('p' => 'browse', 'by' => 'recent')));
			exit;
		}
		catch (DBForeignKeyException $e) {
			// TODO: remove this once files are deleted automatically
			$this->errors[] = 'Please remove the files before deleting';

			return false;
		}
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool
	 * @throws PermissionRequiredException
	 * @throws exceptions\LoginRequiredException
	 */
	private function addFile(Request $request) {

		if (!$this->auth->checkPermission(Auth::EDIT_PUBLICATION)) {
			throw new PermissionRequiredException(Auth::EDIT_PUBLICATION);
		}

		$id = Validator::sanitizeNumber($request->get('id'));
		$file_data = isset($_FILES['file']) ? (array)$_FILES['file'] : false;
		if (!$id || !$file_data) {
			throw new UnexpectedValueException;
		}

		$file_model = new FileModel($this->db);
		$validator = $file_model->getValidator();

		if ($validator->validate($request->post())) {
			try {
				$file = FileHandler::upload($file_data);
				$data = $validator->getSanitizedResult();
				$data = array_merge($data, $file);
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
	 * @throws FileHandlerException
	 * @throws PermissionRequiredException
	 * @throws exceptions\LoginRequiredException
	 */
	private function removeFile(Request $request) {

		if (!$this->auth->checkPermission(Auth::EDIT_PUBLICATION)) {
			throw new PermissionRequiredException(Auth::EDIT_PUBLICATION);
		}

		$file_id = Validator::sanitizeNumber($request->post('file_id'));
		if (!$file_id) {
			throw new UnexpectedValueException;
		}

		$repo = new FileRepository($this->db);
		$file = $repo->where('id', '=', $file_id)->findSingle();
		try {
			FileHandler::delete($file->getName());
		}
		catch (FileNotFoundException $e) {
			// do nothing, as file must be already deleted
		}

		$file_model = new FileModel($this->db);

		return $file_model->delete($file_id);
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool
	 * @throws PermissionRequiredException
	 * @throws exceptions\LoginRequiredException
	 */
	private function removeUrl(Request $request) {

		if (!$this->auth->checkPermission(Auth::EDIT_PUBLICATION)) {
			throw new PermissionRequiredException(Auth::EDIT_PUBLICATION);
		}

		$url_id = Validator::sanitizeNumber($request->post('url_id'));
		if (!$url_id) {
			throw new UnexpectedValueException;
		}

		$url_model = new UrlModel($this->db);

		return $url_model->delete($url_id);
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool|mixed
	 * @throws PermissionRequiredException
	 * @throws exceptions\LoginRequiredException
	 */
	private function addUrl(Request $request) {

		if (!$this->auth->checkPermission(Auth::EDIT_PUBLICATION)) {
			throw new PermissionRequiredException(Auth::EDIT_PUBLICATION);
		}

		$id = Validator::sanitizeNumber($request->get('id'));
		if (!$id) {
			throw new UnexpectedValueException;
		}

		$url_model = new UrlModel($this->db);
		$validator = $url_model->getValidator();

		if ($validator->validate($request->post())) {
			$data = $validator->getSanitizedResult();
			$url = new Url($data);
			try {
				return $url_model->store($url, $id);
			}
			catch (DBDuplicateEntryException $e) {
				$this->errors[] = 'This url is already assigned to this publication';

				return false;
			}
		}
		else {
			$this->errors = array_merge($this->errors, $validator->getErrors());

			return false;
		}
	}
}
