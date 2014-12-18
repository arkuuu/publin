<?php

// TODO: use __autoload!
require_once 'classes/Database.php';
require_once 'classes/BrowseModel.php';
require_once 'classes/AuthorModel.php';
require_once 'classes/PublicationModel.php';
require_once 'classes/BrowseView.php';
require_once 'classes/AuthorView.php';
require_once 'classes/PublicationView.php';
require_once 'classes/GenericView.php';

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
	public function __construct($page, $id, $by) {

		$db = new Database();
		mb_internal_encoding('utf8');

		switch ($page) {
			case 'browse':
				$this -> model = new BrowseModel($by, $id, $db);
				$this -> view = new BrowseView($this -> model, $this -> template);
				break;

			case 'author':
				$this -> model = new AuthorModel($id, $db);
				$this -> view = new AuthorView($this -> model, $this -> template);
				break;
			
			case 'publication':
				$this -> model = new PublicationModel($id, $db);
				$this -> view = new PublicationView($this -> model, $this -> template);
				break;

			default:
				$this -> view = new GenericView($page, $this -> template);
				break;
		}
	}


	/**
	 * Displays the page.
	 *
	 * TODO: comment
	 *
	 * @return	string
	 */
	public function display() {
		$header = './templates/'.$this -> template.'/header.html';
		$menu = './templates/'.$this -> template.'/menu.html';
		$content = $this -> view -> getContent();
		$footer = './templates/'.$this -> template.'/footer.html';

		if (is_file($header) && is_file($menu) && is_file($footer)) {
			
			ob_start();
			include $header;
			include $menu;
			echo $content;
			include $footer;
			$output = ob_get_contents();
			ob_end_clean();

			return $output;
		}
		else {
			return 'Error: Could not find master template!';
		}
	}


	/**
	 * Shows the page title by calling the method of View class.
	 *
	 * @return	string
	 */
	private function showPageTitle() {
		return $this -> view -> showPageTitle();
	}


	/**
	 * Shows the page meta tags by calling the method of View class.
	 *
	 * @return	string
	 */
	private function showMetaTags() {
		return $this -> view -> showMetaTags();
	}
	
}
