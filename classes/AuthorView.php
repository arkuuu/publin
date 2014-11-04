<?php

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
	 * The path to the template file
	 * @var	string
	 */
	private $template;



	/**
	 * Constructs the author view.
	 *
	 * @param	AuthorModel		$model		The author model
	 * @param	string			$template	The template folder
	 */
	public function __construct(AuthorModel $model, $template = 'dev') {
		
		$this -> author = $model -> getAuthor();
		$this -> template = './templates/'.$template.'/author.html';
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
	public function showPublications() {

		$string = '';
		$url = '?p=publication&amp;id=';

		foreach ($this -> author -> getPublications() as $publ) {
			$string .= '<li><a href="'.$url.$publ -> getId().'">'.$publ -> getTitle().'</a> by ';
			foreach ($publ -> getAuthors() as $author) {
				$string .= $author -> getName().', ';
			}

			$string .= $publ -> getMonth().'.'.$publ -> getYear().'</li>'."\n";
		}

		if (!empty($string)) {
			return $string;
		}
		else {
			return 'No publications assigned';
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
