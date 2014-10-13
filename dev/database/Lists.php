<?php

class Lists {
	
	private $db;

	// TODO Storing these only makes sense if any query result is ever needed
	// again during one script run. Is this even happening?
	private $types;
	private $years;
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


	/**
	 * Returns an array with all fields of study. All columns are returned.
	 *
	 * @return array
	 */
	public function getStudyFields() {

		if (!isset($this -> study_fields)) {
			$this -> study_fields = $this -> db -> getData('SELECT *
															FROM `list_study_fields`
															ORDER BY `name` ASC');
		}

		return $this -> study_fields;
	}


	/**
	 * Returns an array with all years.
	 *
	 * @return array
	 */
	public function getYears() {

		if (!isset($this -> years)) {
			$this -> years = $this -> db -> getData('SELECT `year`
													FROM `list_publications`
													GROUP BY `year`
													ORDER BY `year` DESC');
		}

		return $this -> years;
	}


	/**
	 * Returns an array with all month of a specified year.
	 *
	 * @param  string	$year	The year which months should be returned.
	 *
	 * @return array
	 */
	public function getMonths($year) {

		if (!isset($this -> months)) {
			$this -> months = $this -> db -> getData('SELECT `month`
														FROM `list_publications`
														WHERE `year`
															LIKE '.$year.'
														GROUP BY `month`
														ORDER BY `month` DESC');
		}

		return $this -> months;
	}


	// TODO:
	// public function getAuthors($filter = array());


	/**
	 * Returns an array with publications. All Columns are returned.
	 * A filter can be spezified with the optional parameter
	 *
	 * @param  array	$filter	Optional filter array
	 *
	 * @return array
	 */
	public function getPublications($filter = array()) {

		if (!isset($this -> publications)) {

			/* Checks if any filter is set */
			if (!empty($filter)) {

				/* Checks if joining one or more tables is needed */
				$join_authors = array_key_exists('author_id', $filter);
				$join_key_terms = array_key_exists('key_term_id', $filter);

				/* Creates SQL-Query if joining one or more tables is needed */
				if ($join_authors && $join_key_terms) {
					$query = 'SELECT p.* 
								FROM `list_publications` p
								JOIN `rel_publ_to_authors` ra ON (ra.`publication_id` = p.`id`)
								JOIN `rel_publ_to_key_terms` rk ON (rk.`publication_id` = p.`id`)
								WHERE ra.`author_id` LIKE "'.$filter['author_id'].'" AND
									rk.`key_term_id` LIKE "'.$filter['key_term_id'].'" AND';

					unset($filter['author_id']);
					unset($filter['key_term_id']);
				}
				else if ($join_authors) {

					$query = 'SELECT p.* 
								FROM `list_publications` p
								JOIN `rel_publ_to_authors` r ON (r.`publication_id` = p.`id`)
								WHERE r.`author_id` LIKE "'.$filter['author_id'].'" AND';

					unset($filter['author_id']);
				}
				else if ($join_key_terms) {

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