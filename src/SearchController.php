<?php


namespace publin\src;

use UnexpectedValueException;

class SearchController {

	private $db;
	private $model;
	private $result;
	private $errors;


	public function __construct(Database $db) {

		$this->db = new PDODatabase();
		$this->model = new SearchModel($db);
		$this->result = array();
		$this->errors = array();
	}


	public function run(Request $request) {

		if ($request->get('type') === 'publication') {
			$this->searchPublications($request);
		}

		$view = new SearchView($this->result, $this->errors);

		return $view->display();
	}


	public function searchPublications(Request $request) {

		$field = Validator::sanitizeText($request->get('field'));
		$search = Validator::sanitizeText($request->get('search'));
		if (!$search) {
			$this->errors[] = 'Your search input is invalid';

			return false;
		}

		$search_words = explode(' ', $search);

		$repo = new PublicationRepository($this->db);
		$repo->select();

		switch (true) {
			case $field === 'title':
				foreach ($search_words as $word) {
					$repo->where('title', 'LIKE', '%'.$word.'%');
				}
				break;
			case $field === 'booktitle':
				foreach ($search_words as $word) {
					$repo->where('booktitle', 'LIKE', '%'.$word.'%');
				}
				break;
			case $field === 'journal':
				foreach ($search_words as $word) {
					$repo->where('journal', 'LIKE', '%'.$word.'%');
				}
				break;
			case $field === 'publisher':
				foreach ($search_words as $word) {
					$repo->where('publisher', 'LIKE', '%'.$word.'%');
				}
				break;
			case $field === 'year':
				$repo->where('date_published', 'LIKE', $search, 'YEAR');
				break;
			case $field === 'abstract':
				foreach ($search_words as $word) {
					$repo->where('abstract', 'LIKE', '%'.$word.'%');
				}
				break;
			default:
				throw new UnexpectedValueException;
		}

		$this->result = $repo->order('date_published', 'DESC')->find();

		return true;
	}


	public function searchAll(Request $request) {

		return false;
	}


	public function searchAuthors(Request $request) {

		return false;
	}
}
