<?php


namespace publin\src;

class SearchView extends View {

	public function __construct() {

		parent::__construct('search');
	}


	public function showTitle() {

		return 'Search';
	}
}
