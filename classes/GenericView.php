<?php

class GenericView extends View {

	private $template;
	private $page;


	public function __construct($page, $template = 'dev') {

		$this -> page = $page;
		$this -> template = './templates/'.$template.'/'.$page.'.html';
	}


	public function getContent() {
		return parent::getContent($this -> template);
	}


	public function viewPageTitle() {
		return $this -> page;
	}

}
