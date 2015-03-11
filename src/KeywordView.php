<?php


namespace publin\src;

class KeywordView extends ViewWithPublications {

	/**
	 * @var Keyword
	 */
	private $keyword;
	/**
	 * @var bool
	 */
	private $edit_mode;


	public function __construct(Keyword $keyword, $edit_mode = false) {

		parent::__construct($keyword, 'keyword');
		$this->keyword = $keyword;
		$this->edit_mode = $edit_mode;
	}


	public function isEditMode() {

		return $this->edit_mode;
	}


	public function showLinkToSelf($mode = '') {

		$url = '?p=keyword&id=';
		$mode_url = '&m='.$mode;

		if (empty($mode)) {
			return $this->html($url.$this->keyword->getId());
		}
		else {
			return $this->html($url.$this->keyword->getId().$mode_url);
		}
	}
}
