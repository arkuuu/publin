<?php

require_once 'StudyField.php';
require_once 'Type.php';
require_once 'Model.php';

/**
 * Model for browse page
 *
 * TODO: comment
 */
class BrowseModel extends Model {

	/**
	 * @var	string
	 */
	private $browse_type;

	/**
	 * @var	array
	 */
	private $list = array();

	/**
	 * @var	array
	 */
	private $result = array();

	/**
	 * @var	boolean
	 */
	private $is_result = false;



	/**
	 * Constructs the model and gets all data needed.
	 *
	 * @param	string		$type	Type of browsing
	 * @param	int			$id		Id of browsing
	 * @param	Database	$db		Database connection
	 */
	public function __construct($type, $id, Database $db) {

		parent::__construct($db);
		$this -> browse_type = $type;

		switch ($this -> browse_type) {

			case 'recent':
				$this -> is_result = true;
				$this -> result = $this -> createPublications(false, array('limit' => '0,10'));
				break;

			case 'author':
				$this -> list = $this -> createAuthors(false);
				break;

			case 'key_term':
				if ($id > 0) {
					$this -> is_result = true;
					$this -> result = $this -> createPublications(false, array('key_term_id' => $id));
				}
				else {
					$this -> list = $this -> createKeyTerms();
				}
				break;
			
			case 'study_field':				
				if ($id > 0) {
					$this -> is_result = true;
					$this -> result = $this -> createPublications(false, array('study_field_id' => $id));
				}
				else {
					$this -> list = $this -> createStudyFields();
					$this -> num = $this -> getNum();
				}
				break;

			case 'type':
				if ($id > 0) {
					$this -> is_result = true;
					$this -> result = $this -> createPublications(false, array('type_id' => $id));
				}
				else {
					$this -> list = $this -> createTypes();
				}
				break;

			case 'journal':
				if ($id > 0) {
					$this -> is_result = true;
					$this -> result = $this -> createPublications(false, array('journal_id' => $id));
				}
				else {
					$this -> list = $this -> createJournals();
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
	 * Returns the browse list.
	 *
	 * @return	array
	 */
	public function getBrowseList() {
		return $this -> list;
	}


	/**
	 * Returns the browse results.
	 *
	 * @return	array
	 */
	public function getBrowseResult() {
		return $this -> result;
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
	 * Returns an array with all years from the database.
	 *
	 * @return	array
	 */
	private function fetchYears() {
		$data = $this -> db -> fetchYears();
		$this -> num = $this -> db -> getNumRows();

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

}
