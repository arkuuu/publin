<?php

namespace publin\src;

use Exception;
use publin\src\exceptions\LoginRequiredException;
use publin\src\exceptions\NotFoundException;
use publin\src\exceptions\PermissionRequiredException;

/**
 * Controls everything.
 *
 * TODO: comment
 */
class Controller {

	const BASE_URL = '/publin/';

	private $db;
	private $auth;


	/**
	 * Constructs the controller and the needed Model and View.
	 *
	 */
	public function __construct() {

		mb_internal_encoding('utf8');

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
			$method = $request->get('p');
			if (method_exists($this, $method)) {
				return $this->$method($request);
			}
			else {
				return $this->staticPage($request);
			}
		}
		catch (LoginRequiredException $e) {
			$this->redirect('?p=login', $request->getUrl());

			return 'Redirecting to login page';
		}
		catch (PermissionRequiredException $e) {
			return 'Permission '.htmlspecialchars($e->getMessage()).' is required to do this';
		}
		catch (NotFoundException $e) {
			return '404 - Sorry, something missing here: '.htmlspecialchars($e->getMessage());
		}
//		catch (Exception $e) {
//
//			/* Deactivates output buffering if active */
//			if (ob_get_contents()) {
//				ob_end_clean();
//			}
//
//			return 'Sorry, there is an uncaught Exception: '.htmlspecialchars($e->getMessage());
//		}
	}


	/**
	 * @param Request $request
	 *
	 * @return string
	 * @throws Exception
	 * @throws NotFoundException
	 */
	private function staticPage(Request $request) {

		if ($request->get('p')) {
			$view = new View($request->get('p'));
		}
		else {
			$view = new View('start');
		}

		return $view->display();
	}


	public static function redirect($destination, $referrer = '') {

		if (!isset($_SESSION)) {
			session_start();
		}
		$_SESSION['referrer'] = $referrer;

		header('Location: '.self::BASE_URL.$destination, true);
		exit();
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
		$model->handle($request->get('by'), $request->get('id'));
		$view = new BrowseView($model);

		return $view->display();
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return string
	 */
	private function author(Request $request) {

		$controller = new AuthorController($this->db, $this->auth);

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

		$controller = new PublicationController($this->db, $this->auth);

		return $controller->run($request);
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return string
	 */
	private function keyword(Request $request) {

		$controller = new KeywordController($this->db, $this->auth);

		return $controller->run($request);
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return string
	 * @throws Exception
	 * @throws NotFoundException
	 */
	private function study_field(Request $request) {

		$controller = new StudyFieldController($this->db);

		return $controller->run($request);
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return string
	 * @throws LoginRequiredException
	 */
	private function submit(Request $request) {

		$controller = new SubmitController($this->db, $this->auth);

		return $controller->run($request);
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return string
	 * @throws Exception
	 * @throws NotFoundException
	 */
	private function login(Request $request) {

		$errors = array();

		if ($request->post('username') && $request->post('password')) {
			$username = Validator::sanitizeText($request->post('username'));
			$password = Validator::sanitizeText($request->post('password'));
			if ($this->auth->login($username, $password)) {

				$destination = !empty($_SESSION['referrer']) ? $_SESSION['referrer'] : '?p=start';
				$this->redirect($destination, $request->getUrl());
			}
			else {
				$errors[] = 'Invalid user name or password';
			}
		}
		$view = new View('login', $errors);

		return $view->display();
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return string
	 * @throws LoginRequiredException
	 */
	private function manage(Request $request) {

		$controller = new ManageController($this->db, $this->auth);

		return $controller->run($request);
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return string
	 */
	private function user(Request $request) {

		$controller = new UserController($this->db, $this->auth);

		return $controller->run($request);
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
