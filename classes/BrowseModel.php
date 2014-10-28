<?php

require_once 'Author.php';
require_once 'KeyTerm.php';
require_once 'StudyField.php';
require_once 'Publication.php';

class BrowseModel {

	private $db;
	private $allowed_types = array('author', 'key_term', 'study_field');	// TODO: use this!
	private $browse_type;
	private $browse_list = array();
	private $browse_result = array();
	private $browse_num;


	public function __construct($type, $id, Database $db) {

		$this -> db = $db;
		$this -> browse_type = $type;

		switch ($this -> browse_type) {

			case 'author':

				$this -> browse_list = $this -> fetchAuthors();
				$this -> browse_num = $this -> db -> getNumData();
				break;

			case 'key_term':

				if ($id > 0) {
					$this -> fetchPublications(array('key_term_id' => $id));
					$this -> browse_num = $this -> db -> getNumData();
					if ($this -> browse_num > 0) {
						$this -> fetchPublicationsAuthors();
					}
				}
				else {
					$this -> browse_list = $this -> fetchKeyTerms();
					$this -> browse_num = $this -> db -> getNumData();
				}
				break;
			
			case 'study_field':
				
				if ($id > 0) {
					$this -> fetchPublications(array('study_field_id' => $id));
					$this -> browse_num = $this -> db -> getNumData();
					if ($this -> browse_num > 0) {
						$this -> fetchPublicationsAuthors();
					}
				}
				else {
					$this -> browse_list = $this -> fetchStudyFields();
					$this -> browse_num = $this -> db -> getNumData();
				}
				break;

			// case 'year':
			// 	if ($id > 0) {

			// 		$this -> browse_list = $this -> fetchMonths();
			// 		$this -> fetchPublications(array('year' => $id));
			// 		$this -> browse_num = $this -> db -> getNumData();
			// 		if ($this -> browse_num > 0) {
			// 			$this -> fetchPublicationsAuthors();
			// 		}
			// 	}
			// 	else {
			// 		$this -> browse_list = $this -> fetchYears();
			// 		$this -> browse_num = $this -> db -> getNumData();
			// 	}
			// 	break;

			// case 'year:month':
			// 	if ($id > 0) {
			// 		$year = strtok($id, ':');
			// 		$month = strtok(':');

			// 		if (empty($month)) {
			// 			$this -> browse_list = $this -> fetchMonths();
			// 			$this -> fetchPublications(array('year' => $id));
			// 		}
			// 		else {
			// 			$this -> fetchPublications(array('year' => $year, 'month' => $month));
			// 		}

			// 		$this -> browse_num = $this -> db -> getNumData();
			// 		if ($this -> browse_num > 0) {
			// 			$this -> fetchPublicationsAuthors();
			// 		}
			// 	}
			// 	break;
			default:
				# code...
				break;
		}
	}


	public function getBrowseType() {
		return $this -> browse_type;
	}


	public function getBrowseList() {
		return $this -> browse_list;
	}


	public function getBrowseResult() {
		return $this -> browse_result;
	}


	public function getBrowseNum() {
		return $this -> browse_num;
	}


	public function isBrowseResult() {
		if (empty($this -> browse_result)) {
			return false;
		}
		else {
			return true;
		}
	}

	private function fetchAuthors() {
		$data = $this -> db -> fetchAuthors();

		foreach ($data as $key => $value) {
			$authors[] = new Author($value);
		}

		return $authors;
	}


	private function fetchKeyTerms() {
		$data = $this -> db -> fetchKeyTerms();

		foreach ($data as $key => $value) {
			$key_terms[] = new KeyTerm($value);
		}

		return $key_terms;
	}


	private function fetchStudyFields() {
		$data = $this -> db -> fetchStudyFields();

		foreach ($data as $key => $value) {
			$study_fields[] = new StudyField($value);
		}

		return $study_fields;
	}


	private function fetchYears() {
		$data = $this -> db -> fetchYears();

		return $data;

	}


	private function fetchMonths() {
		$data = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);

		return $data;
	}


	private function fetchPublications(array $filter) {
		$data = $this -> db -> fetchPublications($filter);

		foreach ($data as $key => $value) {
			$this -> browse_result[] = new Publication($value);
		}
	}


	private function fetchPublicationsAuthors() {

		foreach ($this -> browse_result as $publication) {
			$authors = array();
			$data = $this -> db -> fetchAuthorsOfPublication($publication -> getId());

			foreach ($data as $key => $value) {
				$authors[] = new Author($value);
			}

			$publication -> setAuthors($authors);
		}
	}

}
