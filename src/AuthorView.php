<?php

namespace publin\src;

use Exception;

/**
 * View for author page
 *
 * TODO: comment
 */
class AuthorView extends View {

	/**
	 * @var    Author
	 */
	private $author;


	/**
	 * Constructs the author view.
	 *
	 * @param Author $author
	 */
	public function __construct(Author $author) {

		parent::__construct('author');
		$this->author = $author;
	}


	/**
	 * Shows the page title.
	 *
	 * @return    string
	 */
	public function showPageTitle() {

		return $this->showName();
	}


	/**
	 * Shows the author's name.
	 *
	 * @return string
	 * @throws Exception
	 */
	public function showName() {

		$name = $this->author->getName();

		if ($name) {
			return $name;
		}
		else {
			throw new Exception('the author with id '.$this->author->getId().' has no name');
		}
	}


	/**
	 * Shows the author's website.
	 *
	 * @return    string
	 */
	public function showWebsite() {

		$website = $this->author->getWebsite();

		if ($website) {
			return '<a href="http://'.$website.'" target="_blank">'.$website.'</a>';
		}
		else {
			return false;
		}

	}


	/**
	 * Shows the author's contact info.
	 *
	 * @return    string
	 */
	public function showContact() {

		return $this->author->getContact();
	}


	/**
	 * Shows the author's text.
	 *
	 * @return    string
	 */
	public function showText() {

		return $this->author->getText();
	}


	/**
	 * Shows the author's publications.
	 *
	 * @return string
	 */
	public function showPublications() {

		$string = '';

		foreach ($this->author->getPublications() as $publication) {
			$string .= '<li>'.$this->showCitation($publication).'</li>'."\n";
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
	 * @return    string
	 */
	public function showBibLinks() {

		$string = '';

		foreach (BibLink::getServices() as $service) {
			$string .= '<li><a href="'.BibLink::getAuthorsLink($this->author, $service).'" target="_blank">'.$service.'</a></li>';
		}

		return $string;
	}

}
