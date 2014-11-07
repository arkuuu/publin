<?php

require_once 'Author.php';
require_once 'KeyTerm.php';
require_once 'StudyField.php';
require_once 'Type.php';
require_once 'Publication.php';

/**
 * Model for browse page
 *
 * TODO: comment
 */
class BrowseModel {

	/**
	 * @var	Database
	 */
	private $db;

	/**
	 * @var	string
	 */
	private $browse_type;

	/**
	 * @var	array
	 */
	private $browse_list = array();

	/**
	 * @var	array
	 */
	private $browse_result = array();

	/**
	 * @var	boolean
	 */
	private $is_result = false;

	/**
	 * @var	int
	 */
	private $browse_num;



	/**
	 * Constructs the model and gets all data needed.
	 *
	 * @param	string		$type	Type of browsing
	 * @param	int			$id		Id of browsing
	 * @param	Database	$db		Database connection
	 */
	public function __construct($type, $id, Database $db) {

		$this -> db = $db;
		$this -> browse_type = $type;

		switch ($this -> browse_type) {

			case 'recent':
				$this -> is_result = true;
				$this -> fetchPublications(array('limit' => '0,10'));
				$this -> browse_num = $this -> db -> getNumData();
				if ($this -> browse_num > 0) {
					$this -> fetchAuthorsOfPublications();
				}
				break;

			case 'author':
				$this -> browse_list = $this -> fetchAuthors();
				$this -> browse_num = $this -> db -> getNumData();
				break;

			case 'key_term':
				if ($id > 0) {
					$this -> is_result = true;
					$this -> fetchPublications(array('key_term_id' => $id));
					$this -> browse_num = $this -> db -> getNumData();
					if ($this -> browse_num > 0) {
						$this -> fetchAuthorsOfPublications();
					}
				}
				else {
					$this -> browse_list = $this -> fetchKeyTerms();
					$this -> browse_num = $this -> db -> getNumData();
				}
				break;
			
			case 'study_field':				
				if ($id > 0) {
					$this -> is_result = true;
					$this -> fetchPublications(array('study_field_id' => $id));
					$this -> browse_num = $this -> db -> getNumData();
					if ($this -> browse_num > 0) {
						$this -> fetchAuthorsOfPublications();
					}
				}
				else {
					$this -> browse_list = $this -> fetchStudyFields();
					$this -> browse_num = $this -> db -> getNumData();
				}
				break;

			case 'type':
				if ($id > 0) {
					$this -> is_result = true;
					$this -> fetchPublications(array('type_id' => $id));
					$this -> browse_num = $this -> db -> getNumData();
					if ($this -> browse_num > 0) {
						$this -> fetchAuthorsOfPublications();
					}
				}
				else {
					$this -> browse_list = $this -> fetchTypes();
					$this -> browse_num = $this -> db -> getNumData();
				}
				break;

			// case 'year':
			// 	if ($id > 0) {

			// 		$this -> browse_list = $this -> fetchMonths();
			// 		$this -> fetchPublications(array('year' => $id));
			// 		$this -> browse_num = $this -> db -> getNumData();
			// 		if ($this -> browse_num > 0) {
			// 			$this -> fetchAuthorsOfPublications();
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
			// 			$this -> fetchAuthorsOfPublications();
			// 		}
			// 	}
			// 	break;

			default:
				// TODO: what happens when browse type is invalid?
				break;
		}
	}

	/**
	 * Returns the browse type.
	 *
	 * @return	string
	 */
	public function getBrowseType() {
		return $this -> browse_type;
	}


	/**
	 * Returns the number of found entries.
	 *
	 * @return	int
	 */
	public function getBrowseNum() {
		return $this -> browse_num;
	}


	/**
	 * Returns the browse list.
	 *
	 * @return	array
	 */
	public function getBrowseList() {
		return $this -> browse_list;
	}


	/**
	 * Returns the browse results.
	 *
	 * @return	array
	 */
	public function getBrowseResult() {
		return $this -> browse_result;
	}


	/**
	 * Returns true if there is a browse result.
	 *
	 * This is used to determine whether the result list or the browse list should be shown,
	 * so this returns true even if the browse result is empty.
	 *
	 * @return	boolean
	 */
	public function isBrowseResult() {
		return $this -> is_result;
	}


	/**
	 * Returns an array with all Author objects from database.
	 *
	 * @return	array
	 */
	private function fetchAuthors() {
		$data = $this -> db -> fetchAuthors();

		foreach ($data as $key => $value) {
			$authors[] = new Author($value);
		}

		return $authors;
	}


	/**
	 * Returns an array with all KeyTerm objects from database.
	 *
	 * @return	array
	 */
	private function fetchKeyTerms() {
		$data = $this -> db -> fetchKeyTerms();

		foreach ($data as $key => $value) {
			$key_terms[] = new KeyTerm($value);
		}

		return $key_terms;
	}


	/**
	 * Returns an array with all StudyField objects from database.
	 *
	 * @return	array
	 */
	private function fetchStudyFields() {
		$data = $this -> db -> fetchStudyFields();

		foreach ($data as $key => $value) {
			$study_fields[] = new StudyField($value);
		}

		return $study_fields;
	}


	/**
	 * Returns an array with all Type objects from database.
	 *
	 * @return	array
	 */
	private function fetchTypes() {
		$data = $this -> db -> fetchTypes();

		foreach ($data as $key => $value) {
			$types[] = new Type($value);
		}

		return $types;
	}


	/**
	 * Returns an array with all years from the database.
	 *
	 * @return	array
	 */
	private function fetchYears() {
		$data = $this -> db -> fetchYears();

		return $data;

	}


	/**
	 * Returns an array with all months.
	 *
	 * @return	array
	 */
	private function fetchMonths() {
		$data = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);

		return $data;
	}


	/**
	 * Fetches all Publications matching the filter.
	 *
	 * Adds every Publication object to the browse_result array.
	 *
	 * @param	array	$filter		The filter
	 *
	 * @return	void
	 */
	private function fetchPublications(array $filter) {
		$data = $this -> db -> fetchPublications($filter);

		foreach ($data as $key => $value) {
			$this -> browse_result[] = new Publication($value);
		}
	}


	/**
	 * Fetches the authors to all publications.
	 *
	 * Adds the authors to every Publication object in the browse_result array.
	 *
	 * @return	void
	 */
	private function fetchAuthorsOfPublications() {

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
