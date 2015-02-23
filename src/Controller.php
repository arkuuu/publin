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

	// TODO: replace these with Request class
	private $id;
	private $by;


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
	 * TODO: comment
	 *
	 * @param $page
	 * @param $id
	 * @param $by
	 *
	 * @return string
	 */
	public function run($page, $id, $by) {

		$this->id = $id;
		$this->by = $by;

		/* Resets the inactivity timer */
		$this->auth->checkLoginStatus();

		/* Searches method to run for request */
		try {
			if (method_exists($this, $page)) {
				return $this->$page();
			}
			else {
				return $this->staticPage($page);
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


	private function staticPage($page) {

		$view = new View($page);

		return $view->display();
	}


	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function browse() {

		$model = new BrowseModel($this->db);
		$model->handle($this->by, $this->id);
		$view = new BrowseView($model);

		return $view->display();
	}


	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function author() {

		$controller = new AuthorController($this->db);

		return $controller->run($this->id);
	}


	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function publication() {

		$model = new PublicationModel($this->db);
		$publication = $model->fetch(true, array('id' => $this->id));
		$view = new PublicationView($publication[0]);

		return $view->display();
	}


	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function keyword() {

		$controller = new KeywordController($this->db);

		return $controller->run($this->id);
	}


	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function journal() {

		$controller = new JournalController($this->db);

		return $controller->run($this->id);
	}


	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function publisher() {

		$controller = new PublisherController($this->db);

		return $controller->run($this->id);
	}


	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function study_field() {

		$model = new StudyFieldModel($this->db);
		$study_field = $model->fetch(true, array('id' => $this->id));
		$view = new StudyFieldView($study_field[0]);

		return $view->display();
	}


	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function submit() {

		if ($this->auth->checkLoginStatus()) {
			$controller = new SubmitController($this->db);

			return $controller->run();
		}
		else {
			return $this->login();
		}
	}


	private function login() {

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
	private function manage() {

		if ($this->auth->checkLoginStatus()) {
			$controller = new ManageController($this->db);

			return $controller->run();
		}
		else {
			return $this->login();
		}
	}


	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function logout() {

		if ($this->auth->checkLoginStatus()) {
			$this->auth->logout();
		}
		$view = new View('login');

		return $view->display();
	}
}
