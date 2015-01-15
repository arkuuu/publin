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

/**
 * Controls everything.
 *
 * TODO: comment
 */
class Controller {

	private $view;
	private $model;



	/**
	 * Constructs the controller and the needed Model and View.
	 *
	 * TODO: change parameters to one array with all parameters
	 *
	 *
	 */
	public function __construct() {
		mb_internal_encoding('utf8');
	}


	/**
	 * Displays the page.
	 *
	 * TODO: comment
	 *
	 * @return	string
	 */
	public function run($page, $id, $by) {

		try {
			switch ($page) {
				case 'browse':
					$model = new BrowseModel($by, $id);
					$view = new BrowseView($model);
					return $view -> display();
					break;

				case 'author':
					$model = new AuthorModel($id);
					$view = new AuthorView($model);
					return $view -> display();
					break;
				
				case 'publication':
					$model = new PublicationModel($id);
					$view = new PublicationView($model -> getPublication());
					return $view -> display();
					break;

				case 'submit':
					// $model = new SubmitModel();
					// $view = new SubmitView($model, 'form');
					$controller = new SubmitController();
					return $controller -> run();
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
