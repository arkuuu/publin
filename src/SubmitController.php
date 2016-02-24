<?php

namespace publin\src;

use BadMethodCallException;
use Exception;
use publin\src\exceptions\DBDuplicateEntryException;
use publin\src\exceptions\PermissionRequiredException;

/**
 * Class SubmitController
 *
 * @package publin\src
 */
class SubmitController extends Controller {

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
		else if ($request->get('m') === 'bulkimport') {
			$view = new SubmitView($this->model, 'bulkimport', $this->errors);
		}		
		else {
			unset($_SESSION['input']);
			unset($_SESSION['input_rest']);
			unset($_SESSION['input_raw']);
			unset($_SESSION['bulkimport_msg']);
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

		$format = Validator::sanitizeText($request->post('format'));
		$bulkimport = Validator::sanitizeBoolean($request->post('bulkimport'));
		$input = $request->post('input');

		if ($input && $format) {
			try {
				$entries = FormatHandler::import($input, $format);
				if ($bulkimport) {
					$this->bulkimport($entries);
					return true;
				}
				$_SESSION['input_raw'] = $input;
				$_SESSION['input_format'] = $format;

				$this->setInputAndRestInSession($entries);

				return true;
			} catch (Exception $e) {
				$this->errors[] = $e->getMessage();
			}
		}
		else {
			$this->errors[] = 'No input to import given';
		}
		
		return false;
	}
	

	private function setInputAndRestInSession(array $entries) {

		// unfold the first element of the array as the one which will be dealt with first...
		$first_entry = array();
		foreach ($entries[0] as $key => $value) {
			$first_entry[$key] = $value;
		}
		unset($entries[0]);
		$_SESSION['input_rest'] = array_values($entries);

		$_SESSION['input'] = $first_entry;
	}


	/**
	 * 
	 * @param array $entries
	 */
	private function bulkimport(array $entries) {
		$messages = [];
	
		foreach ($entries as $key => $entry) {

			// Skip entry, if it has no title
			if (empty($entry['title'])) {
				$messages[] = '[skipped] Publication has no title';
				continue;
			}
			// String for output
			$title = substr($entry['title'], 0, 40);
			
			// Check publication already exists
			$query = 'SELECT id FROM `publications` WHERE `title` LIKE :title;';
			$this->db->prepare($query);
			$this->db->bindValue(':title', $entry['title']);
			$this->db->execute();
			$id = $this->db->fetchColumn();			
			
			if ($id) {
				// TODO: Store citations (!)
				$messages[] = '[skipped] "'.$title.'" (already exists)';
				continue;
			}

			try {
				$entry['study_field'] = 'Computer Science';
				if ($this->store_publication($entry)) {
					$messages[] = '[stored] "'.$title.'"';					
				} else {
					$messages[] = '[failed] "'.$title.'" ('.implode(" ",$this->errors).')';
					$this->errors = [];
				}
			} catch (Exception $e) {
				// TODO, later $e->getMessage()
				$messages[] = '[error] "'.$title.'" ('.$e.')';
			}
		}

		$_SESSION['bulkimport_msg'] = $messages;
	}

	/**
	 * 
	 * @param type $input
	 * @return boolean
	 * @throws DBDuplicateEntryException
	 * @throws Exception
	 */
	private function store_publication($input) {
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
			return false;
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

		$citations = array();
		if (!empty($input['citations'])) {
			// Get the IDs of the publications by using their titles
			foreach ($input['citations'] as $input_citation_title) {
				$query = 'SELECT `id` FROM `publications` WHERE `title` LIKE :title;';
				$this->db->prepare($query);
				$this->db->bindValue(':title', $input_citation_title);
				$this->db->execute();
				$input_citation = $this->db->fetchColumn();
				if ($input_citation) {
					$citations[] = $input_citation;
				}
			}
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

		$url_model = new UrlModel($this->db);
		if (!empty($input['url'])) {
			$validator = $url_model->getValidator();
			$url_array = array('name' => 'External', 'url' => $input['url']);
			if ($validator->validate($url_array)) {
				$url_data = $validator->getSanitizedResult();
				$url = new Url($url_data);
			}
			else {
				$this->errors = array_merge($this->errors, $validator->getErrors());
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
				
				foreach ($citations as $citation_id) {
					$publication_model->addCitation($publication_id, $citation_id);
				}

				foreach ($keywords as $keyword) {
					$keyword_id = $keyword_model->store($keyword);
					$publication_model->addKeyword($publication_id, $keyword_id);
				}

				if (!empty($url)) {
					$url_model->store($url, $publication_id);
				}
				//$this->db->commitTransaction();
				return true;
			}
			catch (DBDuplicateEntryException $e) {
				throw $e;
			}
			catch (Exception $e) {
				//$this->db->cancelTransaction();
				throw $e;
			}
		}
		return false;
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
		
		$result = false;
		try {
			$result = $this->store_publication($input);
		} catch (DBDuplicateEntryException $e) {
			//$this->db->cancelTransaction();
			// TODO make single error messages for each case
			$this->errors[] = 'A publication with this name already exists or you tried to add the same author or keyword to this publication twice';

			return false;
		}

		if (empty($this->errors) && $result) {

			if ($this->next()) {
				return true;
			}

			$this->clearForm();

			$this->redirect(Request::createUrl(array('p' => 'browse', 'by' => 'recent')));

			return true;
		}
		else {

			return false;
		}
	}


	private function next() {

		if (isset($_SESSION['input_rest'])) {
			if (count($_SESSION['input_rest']) > 0) {
				$this->setInputAndRestInSession($_SESSION['input_rest']);

				return true;
			}
		}

		return false;
	}


	/**
	 * @return bool
	 */
	public function clearForm() {

		unset($_SESSION['input']);
		if (isset($_SESSION['input_raw'])) {
			unset($_SESSION['input_raw']);
		}

		return true;
	}
}
