<?php

class Database extends mysqli {
	
	private $host = 'localhost';
	private $readonly_user = 'readonly';
	private $readonly_password = 'readonly';
	private $writeonly_user = '';
	private $writeonly_password = '';
	private $database = 'dev';
	private $charset = 'utf8';

	private $num_data;
	private $last_data;
	private $last_query;


	/**
	 * Creates a new database connection. Uses the constructor of mysqli class.
	 * Stops the script if connection cannot be established. Sets the charset used
	 * for transmission.
	 *
	 * @return void
	 */
	public function __construct() {

		/* Calls the constructor of mysqli and creates a connection */
		parent::__construct($this -> host,
							$this -> readonly_user,
							$this -> readonly_password,
							$this -> database);


		/* Stops if the connection cannot be established */
		if ($this -> connect_errno) {
			die('NO CONNECTION TO DATABASE');	// TODO don't use die!
		}
		/* Sets the charset used for transmission */
		parent::set_charset($this -> charset);
	}


	// TODO DOC
	public function __destruct() {
		parent::close();	// TODO: really as destructor? Not a real method?
	}


	/**
	 * Returns data from query. Returns an array (rows) with arrays (columns) inside.
	 * The last fetched data can be recovered using getLastData().
	 * The last executed query can be recovered using getLastQuery().
	 * This method must not be used directly.
	 *
	 * @param	string	$query	The SQL query
	 *
	 * @return	array
	 */
	private function getData($query) {

		// TODO: Input validation!!, Exception if wrong query
		$this -> last_data = array();
		$this -> last_query = $query;

		// DEV: write query to log
		$msg = str_replace(array("\r\n", "\r", "\n"), ' ', $query);
		$msg = str_replace("\t", '', $msg);
		$file = fopen('sql.log', 'a');
		fwrite($file, '[SQL '.date('d.m.Y H:i:s').'] '
						.$msg."\n");
		fclose($file);

		/* Sends query to database */
		$result = parent::query($query);

		/* Gets number of affected rows */
		$this -> num_data = $result -> num_rows;

		/* Fetches the results */
		while ($entry = $result -> fetch_assoc()) {
			$this -> last_data[] = $entry;
		}

		return $this -> last_data;
	}


	/**
	 * Returns the last fetched data.
	 *
	 * @return	array
	 */
	public function getLastData() {
		return $this -> last_data;
	}


	/**
	 * Returns the last executed query.
	 *
	 * @return	string
	 */
	public function getLastQuery() {
		return $this -> last_query;
	}


	/**
	 * Returns the number of data entries found or affected by last query.
	 *
	 * @return	int
	 */
	public function getNumData() {
		return $this -> num_data;
	}


	/**
	 * Returns an array with all types or with a specific type if $id is set.
	 * All columns are returned.
	 *
	 * @param	int		$id		Optional id of the type
	 *
	 * @return	array
	 */
	public function getTypes($id = 0) {

		if ($id != 0) {
			$query = 'SELECT `id`, `name`
						FROM `list_types`
						WHERE `id` LIKE "'.$id.'"';
		}
		else {
			$query = 'SELECT `id`, `name`
						FROM `list_types`
						ORDER BY `name` ASC';
		}

		return $this -> getData($query);
	}


	/**
	 * Returns an array with key terms. All Columns are returned.
	 * A filter can be specified with the optional parameter $filter.
	 *
	 * @param	array	$filter		Optional filter array
	 *
	 * @return	array
	 */
	public function getKeyTerms(array $filter = array()) {

		/* Checks if any filter is set */
		if (!empty($filter)) {
			
			/* Checks of joining a table is needed */
			$join_publications = array_key_exists('publication_id', $filter);

			/* Creates SQL query if joining a table is needed */
			if ($join_publications) {
				$query = 'SELECT k.`id`, k.`name`
							FROM `list_key_terms` k
							JOIN `rel_publ_to_key_terms` r ON (r.`key_term_id` = k.`id`)
							WHERE r.`publication_id` LIKE "'.$filter['publication_id'].'" AND';

				unset($filter['publication_id']);
			}
			else {
				$query = 'SELECT k.`id`, k.`name`
							FROM `list_key_terms` k
							WHERE';
			}

			/* Creates the WHERE clause from filter array */
			foreach ($filter as $key => $value) {
				$query .= ' k.`'.$key.'` LIKE "'.$value.'" AND';
			}
			$query = substr($query, 0, -3);

			/* Sets the order of the results */
			$query .= 'ORDER BY k.`name` DESC';
		}
		else {

			/* Gets all data */
			$query = 'SELECT *
						FROM `list_key_terms`
						ORDER BY `name` ASC';
		}

		return $this -> getData($query);
	}


	/**
	 * Returns an array with all fields of study or with a specific one if $id is set.
	 * All columns are returned.
	 *
	 * @param	int		$id		optional id of the field of study
	 * 
	 * @return	array
	 */
	public function getStudyFields($id = 0) {

		if ($id != 0) {
			$query = 'SELECT `id`, `name` 
						FROM `list_study_fields`
						WHERE `id` LIKE "'.$id.'"';
		}
		else {
			$query = 'SELECT `id`, `name`
						FROM `list_study_fields`
						ORDER BY `name` ASC';
		}
		
		return $this -> getData($query);
	}


	/**
	 * Returns an array with all years.
	 *
	 * @return array
	 */
	public function getYears() {

		$query = 'SELECT `year`
					FROM `list_publications`
					GROUP BY `year`
					ORDER BY `year` DESC';

		return $this -> getData($query);
	}


	/**
	 * Returns an array with all months of a specified year.
	 *
	 * @param 	string	$year	The year which months should be returned.
	 *
	 * @return	array
	 */
	public function getMonths($year) {

		$query = 'SELECT `month`
					FROM `list_publications`
					WHERE `year` LIKE '.$year.'
					GROUP BY `month`
					ORDER BY `month` DESC';

		return $this -> getData($query);
	}


	/**
	 * Returns an array with authors. All Columns are returned.
	 * A filter can be specified with the optional parameter $filter.
	 *
	 * @param	array	$filter		Optional filter array
	 *
	 * @return	array
	 */
	public function getAuthors(array $filter = array()) {
	
		$select = 'SELECT a.`id`, a.`user_id`, a.`last_name`, a.`first_name`, a.`academic_title`';
		$from = 'FROM `list_authors` a';
		$join = '';
		$where = '';
		$order = 'ORDER BY `last_name` ASC';

		/* Checks if any filter is set */
		if (!empty($filter)) {

			/* Creates the SELECT clause */
			if (array_key_exists('select', $filter)) {
				$select = 'SELECT';

				foreach ($filter['select'] as $key => $value) {
					$select .= ' a.`'.$value.'`,';
				}
				$select = substr($select, 0, -1);
				unset($filter['select']);
			}

			/* Checks if filter is still not empty */
			if (!empty($filter)) {
				$where = 'WHERE';

				/* Creates the JOIN clause if needed */
				if (array_key_exists('publication_id', $filter)) {
					$from = 'FROM `rel_publ_to_authors` rb';	// Better SQL performance this way
					$join .= ' JOIN `list_authors` a ON (rb.`author_id` = a.`id`)';
					$where .= ' rb.`publication_id` LIKE "'.$filter['publication_id'].'" AND';
					$order = 'ORDER BY `priority` ASC';
					unset($filter['publication_id']);
				}
				
				/* Creates the WHERE clause from the rest of the filter array */
				foreach ($filter as $key => $value) {
					$where .= ' a.`'.$key.'` LIKE "'.$value.'" AND';
				}
				$where = substr($where, 0, -3);
			}
		}
		unset($filter);

		/* Combines everything to the complete query */
		$query = $select.' '.$from.' '.$join.' '.$where.' '.$order.';';

		return $this -> getData($query);	// TODO: Return Author objects instead?
	}


	/**
	 * Returns an array with publications.
	 * A filter can be specified with the optional parameter $filter.
	 *
	 * @param	array	$filter		Optional filter array
	 *
	 * @return	array
	 */
	public function getPublications(array $filter = array()) {

		$select = 'SELECT p.`id`, p.`type_id`, p.`study_field_id`, p.`title`, p.`year`, p.`month`';
		$from = 'FROM `list_publications` p';
		$join = '';
		$where = '';
		$order = 'ORDER BY `date_added` ASC';

		/* Checks if any filter is set */
		if (!empty($filter)) {

			/* Creates the SELECT clause */
			if (array_key_exists('select', $filter)) {
				$select = 'SELECT';

				foreach ($filter['select'] as $key => $value) {
					$select .= ' p.`'.$value.'`,';
				}
				$select = substr($select, 0, -1);
				unset($filter['select']);
			}

			/* Checks if filter is still not empty */
			if (!empty($filter)) {
				$where = 'WHERE';

				/* Creates the JOIN clause if needed */
				if (array_key_exists('author_id', $filter)) {
					$join .= ' JOIN `rel_publ_to_authors` ra ON (ra.`publication_id` = p.`id`)';
					$where .= ' ra.`author_id` LIKE "'.$filter['author_id'].'" AND';
					unset($filter['author_id']);
				}
				if (array_key_exists('key_term_id', $filter)) {
					$join .= ' JOIN `rel_publ_to_key_terms` rk ON (rk.`publication_id` = p.`id`)';
					$where .= ' rk.`key_term_id` LIKE "'.$filter['key_term_id'].'" AND';
					unset($filter['key_term_id']);
				}

				/* Creates the WHERE clause from the rest of filter array */
				foreach ($filter as $key => $value) {
					$where .= ' p.`'.$key.'` LIKE "'.$value.'" AND';
				}
				$where = substr($where, 0, -3);
			}
		}
		unset($filter);

		/* Combines everything to the complete query */
		$query = $select.' '.$from.' '.$join.' '.$where.' '.$order.';';

		return $this -> getData($query);	// TODO: Return Publication objects instead of that?
	}


}

?>