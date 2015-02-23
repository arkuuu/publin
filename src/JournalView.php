<?php


namespace publin\src;

class JournalView extends ViewWithPublications {

	/**
	 * @var bool
	 */
	private $edit_mode;
	private $journal;


	public function __construct(Journal $journal, $edit_mode = false) {

		parent::__construct($journal, 'journal');
		$this->journal = $journal;
		$this->edit_mode = $edit_mode;
	}


	public function isEditMode() {

		return $this->edit_mode;
	}


	public function showLinkToSelf($mode = '') {

		$url = '?p=journal&amp;id=';
		$mode_url = '&amp;m='.$mode;

		if (empty($mode)) {
			return $url.$this->journal->getId();
		}
		else {
			return $url.$this->journal->getId().$mode_url;
		}
	}
}
