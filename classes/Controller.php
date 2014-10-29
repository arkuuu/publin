<?php

// TODO: use __autoload!
require_once 'classes/Database.php';
require_once 'classes/BrowseModel.php';
require_once 'classes/AuthorModel.php';
require_once 'classes/PublicationModel.php';
require_once 'classes/View.php';
require_once 'classes/BrowseView.php';
require_once 'classes/AuthorView.php';
require_once 'classes/PublicationView.php';
require_once 'classes/GenericView.php';

class Controller {

	private $view;
	private $model;
	private $template = 'dev';

	public function __construct($page, $id, $by) {

		$db = new Database();

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
				$this -> view = new GenericView($page);
				break;
		}
	}


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


	private function viewPageTitle() {
		return $this -> view -> viewPageTitle();
	}


	private function viewMetaTags() {
		return $this -> view -> viewMetaTags();
	}
	
}
