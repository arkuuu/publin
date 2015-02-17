<?php


namespace publin\src;

class JournalView extends ViewWithPublications {

	public function __construct(Journal $journal) {

		parent::__construct($journal, 'journal');
	}
}
