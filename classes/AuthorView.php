<?php

require_once 'View.php';
require_once 'Citation.php';
require_once 'BibLink.php';

/**
 * View for author page
 *
 * TODO: comment
 */
class AuthorView extends View {

	/**
	 * @var	Author
	 */
	private $author;



	/**
	 * Constructs the author view.
	 *
	 * @param	AuthorModel		$model		The author model
	 * @param	string			$template	The template folder
	 */
	public function __construct(AuthorModel $model, $template = 'dev') {

		parent::__construct($template.'/author.html');		
		$this -> author = $model -> getAuthor();
	}


	/**
	 * Shows the page title.
	 *
	 * @return	string
	 */
	public function showPageTitle() {
		return $this -> author -> getName();
	}


	/**
	 * Shows the author's name.
	 *
	 * @return	string
	 */
	public function showName() {
		return $this -> author -> getName();
	}


	/**
	 * Shows the author's website.
	 *
	 * @return	string
	 */
	public function showWebsite() {
		return '<a href="http://'.$this -> author -> getWebsite().'" target="_blank">'.$this -> author -> getWebsite().'</a>';
	}


	/**
	 * Shows the author's contact info.
	 *
	 * @return	string
	 */
	public function showContact() {
		return $this -> author -> getContact();
	}


	/**
	 * Shows the author's text.
	 *
	 * @return	string
	 */
	public function showText() {
		return $this -> author -> getText();
	}


	/**
	 * Shows the author's publications.
	 *
	 * @return	string
	 */
	public function showPublications($style = '') {

		$string = '';
		$url = '?p=publication&amp;id=';

		foreach ($this -> author -> getPublications() as $publication) {
			$string .= '<li>'.Citation::getCitation($publication, $style)
					.' - <a href="'.$url.$publication -> getId().'">show</a></li>'."\n";
		}

		if (!empty($string)) {
			return $string;
		}
		else {
			return '<li>no publications found</li>';
		}
	}


	/**
	 * Shows links to other bibliographic indexes for this author.
	 *
	 * @return	string
	 */
	public function showBibLinks() {

		$string = '';

		foreach (BibLink::getServices() as $service) {
			$string .= '<li><a href="'.BibLink::getAuthorsLink($this -> author, $service).'" target="_blank">'.$service.'</a></li>';
		}
		return $string;
	}

}
