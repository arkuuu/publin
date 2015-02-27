<?php

namespace publin\src;

use Exception;
use publin\src\exceptions\NotFoundException;

/**
 * Controls everything.
 *
 * TODO: comment
 */
class Controller {

	private $auth;
	private $db;


	/**
	 * Constructs the controller and the needed Model and View.
	 *
	 * TODO: change parameters to one array with all parameters
	 *
	 *
	 */
	public function __construct() {

		mb_internal_encoding('utf8');
		header('Content-Type: text/html; charset=UTF-8');

		$this->db = new Database();
		// TODO: catch exception here
		$this->auth = new Auth($this->db);
	}


	/**
	 * Displays the page.
	 *
	 * @param Request $request
	 *
	 * @return string
	 */
	public function run(Request $request) {

		/* Resets the inactivity timer */
		$this->auth->checkLoginStatus();

		/* Searches method to run for request */
		try {
			$handler = $request->page;
			if (method_exists($this, $handler)) {
				return $this->$handler($request);
			}
			else {
				return $this->staticPage($request);
			}
		}
		catch (NotFoundException $e) {
			// TODO: header(..)
			return '404 - Sorry, something missing here: '.$e->getMessage();
		}
		catch (Exception $e) {

			/* Deactivates output buffering if active */
			if (ob_get_contents()) {
				ob_end_clean();
			}

			return 'Sorry, there is an uncaught Exception: '.$e->getMessage();
		}
	}


	/**
	 * @param Request $request
	 *
	 * @return string
	 * @throws Exception
	 * @throws NotFoundException
	 */
	private function staticPage(Request $request) {

		$view = new View($request->page);

		return $view->display();
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return string
	 * @throws Exception
	 * @throws NotFoundException
	 */
	private function browse(Request $request) {

		$model = new BrowseModel($this->db);
		$model->handle($request->by, $request->id);
		$view = new BrowseView($model);

		return $view->display();
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return string
	 */
	private function author(Request $request) {

		$controller = new AuthorController($this->db);

		return $controller->run($request);
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return string
	 * @throws Exception
	 * @throws NotFoundException
	 */
	private function publication(Request $request) {

		$model = new PublicationModel($this->db);
		$publication = $model->fetch(true, array('id' => $request->id));
		$view = new PublicationView($publication[0]);

		return $view->display();
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return string
	 */
	private function keyword(Request $request) {

		$controller = new KeywordController($this->db);

		return $controller->run($request);
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return string
	 */
	private function journal(Request $request) {

		$controller = new JournalController($this->db);

		return $controller->run($request);
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return string
	 */
	private function publisher(Request $request) {

		$controller = new PublisherController($this->db);

		return $controller->run($request->id);
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return string
	 * @throws Exception
	 * @throws NotFoundException
	 */
	private function study_field(Request $request) {

		$model = new StudyFieldModel($this->db);
		$study_field = $model->fetch(true, array('id' => $request->id));
		$view = new StudyFieldView($study_field[0]);

		return $view->display();
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return string
	 */
	private function submit(Request $request) {

		if ($this->auth->checkLoginStatus()) {
			$controller = new SubmitController($this->db);

			return $controller->run();
		}
		else {
			return $this->login($request);
		}
	}


	/**
	 * @param Request $request
	 *
	 * @return string
	 * @throws Exception
	 * @throws NotFoundException
	 */
	private function login(Request $request) {

		// TODO: redirect if already logged in
		if (!empty($_POST['username']) && !empty($_POST['password'])) {
			if ($this->auth->login($_POST['username'], $_POST['password'])) {
				// header();
				print_r('success');
			}
			else {
				print_r('incorrect login');
			}
		}
		$view = new View('login');

		return $view->display();
	}


	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function manage(Request $request) {

		if ($this->auth->checkLoginStatus()) {
			$controller = new ManageController($this->db);

			return $controller->run($request);
		}
		else {
			return $this->login($request);
		}
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return string
	 * @throws Exception
	 * @throws NotFoundException
	 */
	private function logout(Request $request) {

		if ($this->auth->checkLoginStatus()) {
			$this->auth->logout();
		}
		$view = new View('login');

		return $view->display();
	}
}
