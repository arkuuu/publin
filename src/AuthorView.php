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
	 * @var bool
	 */
	private $edit_mode;


	/**
	 * Constructs the author view.
	 *
	 * @param Author $author
	 * @param bool   $edit_mode
	 */
	public function __construct(Author $author, $edit_mode = false) {

		parent::__construct($author, 'author');
		$this->author = $author;
		$this->edit_mode = $edit_mode;
	}


	public function isEditMode() {

		return $this->edit_mode;
	}


	public function showLinkToSelf($mode = '') {

		$url = '?p=author&amp;id=';
		$mode_url = '&amp;m='.$mode;

		if (empty($mode)) {
			return $url.$this->author->getId();
		}
		else {
			return $url.$this->author->getId().$mode_url;
		}
	}


	public function showGivenName() {

		return $this->author->getFirstName();
	}


	public function showFamilyName() {

		return $this->author->getLastName();
	}


	/**
	 * Shows the author's website.
	 *
	 * @return    string
	 */
	public function showWebsite() {

		return $this->author->getWebsite();
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


	public function showKeywords($separator = ', ') {

		$keywords = $this->author->getKeywords();

		if (!empty($keywords)) {

			$string = '';
			$url = '?p=keyword&amp;id=';

			foreach ($keywords as $keyword) {

				$keyword_id = $keyword->getId();
				$keyword_name = $keyword->getName();

				if ($keyword_id && $keyword_name) {
					$string .= '<a href="'.$url.$keyword_id.'">'.$keyword_name.'</a>'.$separator;
				}
				else if ($keyword_name) {
					$string .= $keyword_name.$separator;
				}
			}

			return substr($string, 0, -(strlen($separator)));
		}
		else {
			return false;
		}
	}
}
