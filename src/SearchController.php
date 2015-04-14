<?php


namespace publin\src;

use UnexpectedValueException;

class SearchController extends Controller {

	private $db;
	private $result;
	private $errors;


	public function __construct(PDODatabase $db) {

		$this->db = $db;
		$this->result = array();
		$this->errors = array();
	}


	public function run(Request $request) {

		if ($request->get('type') === 'publication') {
			$this->searchPublications($request);
		}
		else if ($request->get('type') === 'author') {
			$this->searchAuthors($request);
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




	public function searchAuthors(Request $request) {

		$field = Validator::sanitizeText($request->get('field'));
		$search = Validator::sanitizeText($request->get('search'));
		if (!$search) {
			$this->errors[] = 'Your search input is invalid';

			return false;
		}

		$search_words = explode(' ', $search);

		$repo = new AuthorRepository($this->db);
		$repo->select();

		switch (true) {
			case $field === 'given':
				foreach ($search_words as $word) {
					$repo->where('given', 'LIKE', '%'.$word.'%');
				}
				break;
			case $field === 'family':
				foreach ($search_words as $word) {
					$repo->where('family', 'LIKE', '%'.$word.'%');
				}
				break;
			case $field === 'about':
				foreach ($search_words as $word) {
					$repo->where('about', 'LIKE', '%'.$word.'%');
				}
				break;
			default:
				throw new UnexpectedValueException;
		}

		$this->result = $repo->order('family', 'ASC')->find();

		return true;
	}
}
