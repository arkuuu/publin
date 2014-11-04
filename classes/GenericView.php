<?php

/**
 * View for all static pages
 *
 * TODO: comment
 */
class GenericView extends View {

	/**
	 * @var	string
	 */
	private $page;

	/**
	 * The path to the template file
	 * @var	string
	 */
	private $template;



	/**
	 * Constructs the generic view.
	 *
	 * @param	string		$page		The page (=template file)
	 * @param	string		$template	The template folder
	 */
	public function __construct($page, $template = 'dev') {

		$this -> page = $page;
		$this -> template = './templates/'.$template.'/'.$page.'.html';
	}


	/**
	 * Returns the content of the template file using parent method.
	 *
	 * @return	string
	 */
	public function getContent() {
		return parent::getContent($this -> template);
	}


	/**
	 * Shows the page title.
	 *
	 * @return	string
	 */
	public function viewPageTitle() {
		return $this -> page;
	}

}
