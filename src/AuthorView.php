<?php

namespace publin\src;

/**
 * View for author page
 *
 * TODO: comment
 */
class AuthorView extends ViewWithPublications {

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

		parent::__construct($author, 'author');
		$this->author = $author;
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
