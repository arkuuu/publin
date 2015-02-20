<?php


namespace publin\src;

class KeyTermView extends ViewWithPublications {

	/**
	 * @var KeyTerm
	 */
	private $keyword;
	/**
	 * @var bool
	 */
	private $edit_mode;


	public function __construct(KeyTerm $keyword, $edit_mode = false) {

		parent::__construct($keyword, 'keyterm');
		$this->keyword = $keyword;
		$this->edit_mode = $edit_mode;
	}


	public function isEditMode() {

		return $this->edit_mode;
	}


	public function showLinkToSelf($mode = '') {

		$url = '?p=key_term&amp;id=';
		$mode_url = '&amp;m='.$mode;

		if (empty($mode)) {
			return $url.$this->keyword->getId();
		}
		else {
			return $url.$this->keyword->getId().$mode_url;
		}
	}
}
