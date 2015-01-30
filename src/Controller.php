<?php

// TODO: use __autoload!
require_once 'BrowseModel.php';
require_once 'AuthorModel.php';
require_once 'PublicationModel.php';
require_once 'SubmitModel.php';
require_once 'SubmitController.php';
require_once 'BrowseView.php';
require_once 'AuthorView.php';
require_once 'PublicationView.php';
require_once 'SubmitView.php';
require_once 'GenericView.php';
require_once 'Auth.php';

/**
 * Controls everything.
 *
 * TODO: comment
 */
class Controller {

	private $view;
	private $model;
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

		$this -> db = new Database();
		$this -> auth = new Auth($this -> db);
	}


	/**
	 * Displays the page.
	 *
	 * TODO: comment
	 *
	 * @return	string
	 */
	public function run($page, $id, $by) {

		$db = $this -> db;
		// print_r($_SESSION);


		try {
			switch ($page) {
				case 'browse':
					$model = new BrowseModel($db);
					$model -> handle($by, $id);
					$view = new BrowseView($model);
					return $view -> display();
					break;

				case 'author':
					$model = new AuthorModel($db);
					$author = $model -> fetch(true, array('id' => $id));
					$view = new AuthorView($author[0]);
					return $view -> display();
					break;
				
				case 'publication':
					$model = new PublicationModel($db);
					$publication = $model -> fetch(true, array('id' => $id));
					$view = new PublicationView($publication[0]);
					return $view -> display();
					break;

				case 'submit':
					// $model = new SubmitModel();
					// $view = new SubmitView($model, 'form');
					$controller = new SubmitController($db);
					return $controller -> run();
					break;

				case 'login':
					if (!empty($_POST['username']) && !empty($_POST['password'])) {
						if ($this -> auth -> validateLogin($_POST['username'], $_POST['password'])) {
							// header();
							print_r('success');
						}
						else {
							print_r('incorrect login');
						}
					}
					$view = new GenericView('login');
					return $view -> display();

					break;

				case 'logout':
					if ($this -> auth -> checkLoginStatus()) {
						$this -> auth -> logout();
					}
					$view = new GenericView('login');
					return $view -> display();
					break;

				default:
					$view = new GenericView($page);
					return $view -> display();
					break;
			}

			// return $view -> display();	
		}
		catch (Exception $e) {
			ob_end_clean();
			return 'Error: '.$e -> getMessage().'<br/>File: '.$e -> getFile();
		}

		
	}
	
}
