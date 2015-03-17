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
	 * @param array  $errors
	 * @param bool   $edit_mode
	 */
	public function __construct(Author $author, array $errors, $edit_mode = false) {

		parent::__construct($author, 'author', $errors);
		$this->author = $author;
		$this->edit_mode = $edit_mode;
	}


	public function isEditMode() {

		return $this->edit_mode;
	}


	public function showLinkToSelf($mode = '') {

		$url = '?p=author&id=';
		$mode_url = '&m='.$mode;

		if (empty($mode)) {
			return $this->html($url.$this->author->getId());
		}
		else {
			return $this->html($url.$this->author->getId().$mode_url);
		}
	}


	public function showGivenName() {

		return $this->html($this->author->getFirstName());
	}


	public function showFamilyName() {

		return $this->html($this->author->getLastName());
	}


	/**
	 * Shows the author's website.
	 *
	 * @return    string
	 */
	public function showWebsite() {

		return $this->html($this->author->getWebsite());
	}


	/**
	 * Shows the author's contact info.
	 *
	 * @return    string
	 */
	public function showContact() {

		return nl2br($this->html($this->author->getContact()));
	}


	/**
	 * Shows the author's text.
	 *
	 * @return    string
	 */
	public function showAbout() {

		return nl2br($this->html($this->author->getAbout()));
	}


	/**
	 * Shows links to other bibliographic indexes for this author.
	 *
	 * @return    string
	 */
	public function showBibLinks() {

		$string = '';

		foreach (BibLink::getServices() as $service) {
			$url = BibLink::getAuthorsLink($this->author, $service);
			$string .= '<li><a href="'.$this->html($url).'" target="_blank">'.$this->html($service).'</a></li>';
		}

		return $string;
	}


	public function showEditForm() {

		return '<form action="#" method="post" accept-charset="utf-8">
	<label for="given">Given Name:</label>
	<input type="text" name="given" id="given" value="'.$this->author->getFirstName().'"/>
	<br/>
	<label for="family">Family Name:</label>
	<input type="text" name="family" id="family" value="'.$this->author->getLastName().'"/>
	<br/>
	<label for="website">Website:</label>
	<input type="text" name="website" id="website" value="'.$this->author->getWebsite().'"/>
	<br/>
	<label for="contact">Contact:</label>
	<textarea name="contact" id="contact" rows="5" cols="50">'.$this->author->getContact().'</textarea>
	<br/>
	<label for="about">About:</label>
	<textarea name="about" id="about" rows="5" cols="50">'.$this->author->getAbout().'</textarea>
	<br/>
	<input type="hidden" name="action" value="edit"/>
	<input type="submit" value="Update"/>
	<input type="reset" value="Reset changes"/>
</form>';
	}
}
