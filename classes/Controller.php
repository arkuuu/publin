<?php

// TODO: use __autoload!
require_once 'BrowseModel.php';
require_once 'AuthorModel.php';
require_once 'PublicationModel.php';
require_once 'SubmitModel.php';
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
	private $template = 'default';



	/**
	 * Constructs the controller and the needed Model and View.
	 *
	 * TODO: change parameters to one array with all parameters
	 *
	 * @param	string	$page	The page that was requested
	 * @param	int		$id		The given Id parameter
	 * @param	string	$by		The given by parameter
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

		switch ($page) {
			case 'browse':
				$this -> model = new BrowseModel($by, $id);
				$this -> view = new BrowseView($this -> model, $this -> template);
				break;

			case 'author':
				$this -> model = new AuthorModel($id);
				$this -> view = new AuthorView($this -> model, $this -> template);
				break;
			
			case 'publication':
				$this -> model = new PublicationModel($id);
				$this -> view = new PublicationView($this -> model, $this -> template);
				break;

			case 'submit':
				$this -> model = new SubmitModel();
				$this -> view = new SubmitView($this -> model, $this -> template);
				break;

			default:
				$this -> view = new GenericView($page, $this -> template);
				break;
		}

		return $this -> view -> display();
	}
	
}
