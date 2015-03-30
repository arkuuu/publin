<?php


namespace publin\src;

class SearchController {

	private $db;
	private $model;


	public function __construct(Database $db) {

		$this->db = $db;
		$this->model = new SearchModel($db);
	}


	public function run(Request $request) {

		$view = new SearchView();

		return $view->display();
	}
}
