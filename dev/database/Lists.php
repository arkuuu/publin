<?php

class Lists {
	
	private $db;

	// TODO these only make sense of any query result is ever needed
	// again during one script run. Is this even happening?
	private $types;
	private $study_fields;
	private $publications;


	public function __construct(Database $db) {
		$this -> db = $db;
	}


	/**
	 * Returns an array with all types. All columns are returned.
	 *
	 * @return array
	 */
	public function getTypes() {

		if (!isset($this -> types)) {
			$this -> types = $this -> db -> getData('SELECT *
													FROM `list_types`
													ORDER BY `name` ASC');
		}

		return $this -> types;
	}

	/**
	 * Returns an array with all key terms. All columns are returned.
	 *
	 * @return array
	 */
	public function getKeyTerms() {

		if (!isset($this -> key_terms)) {
			$this -> key_terms = $this -> db -> getData('SELECT *
														FROM `list_key_terms`
														ORDER BY `name` ASC');
		}

		return $this -> key_terms;
	}

	
	public function getStudyFields() {

		if (!isset($this -> study_fields)) {
			$this -> study_fields = $this -> db -> getData('SELECT *
															FROM `list_study_fields`
															ORDER BY `name` ASC');
		}

		return $this -> study_fields;
	}


	// public function getYears();

	// public function getMonths();

	// public function getAuthors($filter, $value);


	// public function getPublications($filter = array()) {

	// 	if (!isset($this -> publications)) {

	// 		/* Checks if any filter is set */
	// 		if (!empty($filter)) {

	// 			/* Creates the WHERE-clause from filter array */
	// 			$where = 'WHERE';
	// 			foreach ($filter as $key => $value) {
	// 				$where .= ' `'.$key.'` LIKE "'.$value.'" AND';
	// 			}
	// 			$where = substr($where, 0, -4);

	// 			/* Gets the filtered data */
	// 			$this -> publications = $this -> db -> getData('SELECT *
	// 															FROM `list_publications`
	// 															'.$where.'
	// 															ORDER BY `date_added` DESC');
	// 		}
	// 		else {				
	// 			/* Gets all data */
	// 			$this -> publications = $this -> db -> getData('SELECT *
	// 															FROM `list_publications`
	// 															ORDER BY `date_added` DESC');	
	// 		}
	// 	}

	// 	return $this -> publications;	// TODO: maybe already return Publication objects?
	// }

	public function getPublications($filter = array()) {

		if (!isset($this -> publications)) {

			/* Checks if any filter is set */
			if (!empty($filter)) {

				// TODO: create if-case when both joins are needed!
				if (array_key_exists('author_id', $filter)) {

					$query = 'SELECT p.* 
								FROM `list_publications` p
								JOIN `rel_publ_to_authors` r ON (r.`publication_id` = p.`id`)
								WHERE r.`author_id` LIKE "'.$filter['author_id'].'" AND';

					unset($filter['author_id']);
				}
				else if (array_key_exists('key_term_id', $filter)) {

					$query = 'SELECT p.* 
								FROM `list_publications` p
								JOIN `rel_publ_to_key_terms` r ON (r.`publication_id` = p.`id`)
								WHERE r.`key_term_id` LIKE "'.$filter['key_term_id'].'" AND';

					unset($filter['key_term_id']);
				}
				else {
					$query = 'SELECT *
								FROM `list_publications` p
								WHERE ';
				}

				/* Creates the WHERE-clause from filter array */
				foreach ($filter as $key => $value) {
					$query .= ' p.`'.$key.'` LIKE "'.$value.'" AND';
				}
				$query = substr($query, 0, -3);
				$query .= 'ORDER BY `date_added` DESC';

				/* Gets the filtered data */
				$this -> publications = $this -> db -> getData($query);
			}
			else {				
				/* Gets all data */
				$this -> publications = $this -> db -> getData('SELECT *
																FROM `list_publications`
																ORDER BY `date_added` DESC');	
			}
		}

		return $this -> publications;	// TODO: maybe already return Publication objects?
	}


}

?>