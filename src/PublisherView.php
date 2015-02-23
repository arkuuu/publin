<?php


namespace publin\src;

class PublisherView extends ViewWithPublications {

	/**
	 * @var bool
	 */
	private $edit_mode;

	private $publisher;


	public function __construct(Publisher $publisher, $edit_mode = false) {

		parent::__construct($publisher, 'publisher');
		$this->publisher = $publisher;
		$this->edit_mode = $edit_mode;
	}


	public function isEditMode() {

		return $this->edit_mode;
	}


	public function showLinkToSelf($mode = '') {

		$url = '?p=publisher&amp;id=';
		$mode_url = '&amp;m='.$mode;

		if (empty($mode)) {
			return $url.$this->publisher->getId();
		}
		else {
			return $url.$this->publisher->getId().$mode_url;
		}
	}
}
