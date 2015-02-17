<?php


namespace publin\src;

class KeyTermView extends ViewWithPublications {

	public function __construct(KeyTerm $key_term) {

		parent::__construct($key_term, 'keyterm');
	}
}
