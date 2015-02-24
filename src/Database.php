<?php

namespace publin\src;

use InvalidArgumentException;
use mysqli;
use publin\src\exceptions\SQLException;

/**
 * Handles all database communication.
 *
 * TODO: comment
 */
class Database extends mysqli {

	// TODO: get these from a config file
	private $host = 'localhost';
	private $readonly_user = 'readonly';
	private $readonly_password = 'readonly';
	private $writeonly_user = 'root';    // TODO: change
	private $writeonly_password = 'root';
	private $database = 'dev';
	private $charset = 'utf8';

	private $num_rows;


	/**
	 * @throws SQLException
	 */
	public function __construct() {

		/* Calls the constructor of mysqli and creates a connection */
		parent::__construct($this->host,
							$this->readonly_user,
							$this->readonly_password,
							$this->database);

		/* Stops if the connection cannot be established */
		if ($this->connect_errno) {
			throw new SQLException($this->connect_error);
		}
		/* Sets the charset used for transmission */
		parent::set_charset($this->charset);
	}


	// TODO DOC
	public function __destruct() {

		parent::close();    // TODO: really as destructor? Not a real method?
	}


	// TODO DOC
	public function getNumRows() {

		return $this->num_rows;
	}


	/**
	 * @param       $table
	 * @param array $data
	 *
	 * @return mixed
	 * @throws SQLException
	 */
	public function insertData($table, array $data) {

		if (empty($data)) {
			throw new InvalidArgumentException('where must not be empty when deleting');
		}

		parent::change_user($this->writeonly_user,
							$this->writeonly_password,
							$this->database);

		$into = array_keys($data);
		$values = array_values($data);
		$query = 'INSERT INTO `'.$table.'`(';

		foreach ($into as $field) {
			$query .= '`'.$field.'`, ';
		}
		$query = substr($query, 0, -2);

		$query .= ') VALUES (';

		foreach ($values as $value) {
			$query .= '"'.$value.'", ';
		}
		$query = substr($query, 0, -2);

		$query .= ') ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id);';

		// DEV: write query to log
		$msg = str_replace(array("\r\n", "\r", "\n"), ' ', $query);
		$msg = str_replace("\t", '', $msg);
		$file = fopen('./logs/sql.log', 'a');
		fwrite($file, '['.date('d.m.Y H:i:s').'] '
					.$msg."\n");
		fclose($file);

		$result = parent::query($query);

		if (!$result) {
			throw new SQLException($this->error);
		}

		return $this->insert_id;
	}


	/**
	 * @param       $table
	 * @param array $where
	 *
	 * @return int
	 * @throws SQLException
	 */
	public function deleteData($table, array $where) {

		if (empty($where)) {
			throw new InvalidArgumentException('where must not be empty when deleting');
		}

		parent::change_user($this->writeonly_user,
							$this->writeonly_password,
							$this->database);

		$query = 'DELETE FROM `'.$table.'`';
		$query .= ' WHERE';

		foreach ($where as $key => $value) {
			$query .= ' `'.$key.'` = "'.$value.'" AND';
		}
		$query = substr($query, 0, -3);

		$msg = str_replace(array("\r\n", "\r", "\n"), ' ', $query);
		$msg = str_replace("\t", '', $msg);
		$file = fopen('./logs/sql.log', 'a');
		fwrite($file, '['.date('d.m.Y H:i:s').'] '
					.$msg."\n");
		fclose($file);

		$result = parent::query($query);

		if (!$result) {
			throw new SQLException($this->error);
		}

		return $this->affected_rows;
	}


	public function updateData($table, array $where, array $data) {

		if (empty($where) || empty($data)) {
			throw new InvalidArgumentException('where and data must not be empty when updating');
		}

		parent::change_user($this->writeonly_user,
							$this->writeonly_password,
							$this->database);

		$query = 'UPDATE `'.$table.'`';

		$query .= ' SET';

		foreach ($data as $column => $value) {
			$query .= ' `'.$column.'` = "'.$value.'",';
		}
		$query = substr($query, 0, -1);

		$query .= ' WHERE';

		foreach ($where as $key => $value) {
			$query .= ' `'.$key.'` = "'.$value.'" AND';
		}
		$query = substr($query, 0, -3);

		$msg = str_replace(array("\r\n", "\r", "\n"), ' ', $query);
		$msg = str_replace("\t", '', $msg);
		$file = fopen('./logs/sql.log', 'a');
		fwrite($file, '['.date('d.m.Y H:i:s').'] '
					.$msg."\n");
		fclose($file);

		$result = parent::query($query);

		if (!$result) {
			throw new SQLException($this->error);
		}

		return $this->affected_rows;
	}


	/**
	 * Returns an array with types. All Columns are returned.
	 * A filter can be specified with the optional parameter $filter.
	 *
	 * @param    array $filter Optional filter array
	 *
	 * @return    array
	 */
	public function fetchTypes(array $filter = array()) {

		$select = 'SELECT t.*';
		$from = 'FROM `list_types` t';
		$where = '';
		$order = 'ORDER BY `name` ASC';
		$limit = '';

		/* Checks if any filter is set */
		if (!empty($filter)) {

			/* Creates the LIMIT clause */
			if (array_key_exists('limit', $filter)) {
				$limit = 'LIMIT '.$filter['limit'];
				unset($filter['limit']);
			}

			/* Checks if filter is still not empty */
			if (!empty($filter)) {
				$where = 'WHERE';

				/* Creates the WHERE clause from the rest of the filter array */
				foreach ($filter as $key => $value) {
					$where .= ' t.`'.$key.'` LIKE "'.$value.'" AND';
				}
				$where = substr($where, 0, -3);
			}
		}
		unset($filter);

		/* Combines everything to the complete query */
		$query = $select.' '.$from.' '.$where.' '.$order.' '.$limit.';';

		return $this->getData($query);
	}


	/**
	 * Returns data from query. Returns an array (rows) with arrays (columns) inside.
	 * The last fetched data can be recovered using getLastData().
	 * The last executed query can be recovered using getLastQuery().
	 * This method must not be used directly.
	 *
	 * @param    string $query The SQL query
	 *
	 * @return array
	 * @throws SQLException
	 */
	public function getData($query) {

		// DEV: write query to log
		$msg = str_replace(array("\r\n", "\r", "\n"), ' ', $query);
		$msg = str_replace("\t", '', $msg);
		$file = fopen('./logs/sql.log', 'a');
		fwrite($file, '['.date('d.m.Y H:i:s').'] '
					.$msg."\n");
		fclose($file);

		/* Sends query to database */
		$result = parent::query($query);

		if (!is_object($result)) {
			throw new SQLException($this->error);
		}
		$this->num_rows = $result->num_rows;

		/* Fetches the results */
		$data = array();
		while ($entry = $result->fetch_assoc()) {
			$data[] = $entry;
		}
		$result->free();

		return $data;
	}


	/**
	 * Returns an array with key terms. All Columns are returned.
	 * A filter can be specified with the optional parameter $filter.
	 *
	 * @param    array $filter Optional filter array
	 *
	 * @return    array
	 */
	public function fetchKeywords(array $filter = array()) {

		$select = 'SELECT k.*';
		$from = 'FROM `list_keywords` k';
		$join = '';
		$where = '';
		$order = 'ORDER BY `name` ASC';
		$limit = '';

		/* Checks if any filter is set */
		if (!empty($filter)) {

			/* Creates the LIMIT clause */
			if (array_key_exists('limit', $filter)) {
				$limit = 'LIMIT '.$filter['limit'];
				unset($filter['limit']);
			}

			/* Checks if filter is still not empty */
			if (!empty($filter)) {
				$where = 'WHERE';

				/* Creates the JOIN clause if needed */
				if (array_key_exists('publication_id', $filter)) {
					$from = 'FROM `rel_publication_keywords` rk';    // Better SQL performance this way
					$join .= ' JOIN `list_keywords` k ON (rk.`keyword_id` = k.`id`)';
					$where .= ' rk.`publication_id` LIKE "'.$filter['publication_id'].'" AND';
					unset($filter['publication_id']);
				}
				if (array_key_exists('author_id', $filter)) {
					$select = 'SELECT DISTINCT k.*';
					$from = 'FROM `rel_publication_keywords` rk';    // Better SQL performance this way
					$join .= ' JOIN `list_keywords` k ON (rk.`keyword_id` = k.`id`)';
					$join .= ' JOIN `rel_publ_to_authors` a ON (rk.`publication_id` = a.`publication_id`)';
					$where .= ' a.`author_id` LIKE "'.$filter['author_id'].'" AND';
					unset($filter['author_id']);
				}

				/* Creates the WHERE clause from the rest of the filter array */
				foreach ($filter as $key => $value) {
					$where .= ' k.`'.$key.'` LIKE "'.$value.'" AND';
				}
				$where = substr($where, 0, -3);
			}
		}
		unset($filter);

		/* Combines everything to the complete query */
		$query = $select.' '.$from.' '.$join.' '.$where.' '.$order.' '.$limit.';';

		return $this->getData($query);    // TODO: Return Author objects instead?
	}


	/**
	 * Returns an array with study fields. All Columns are returned.
	 * A filter can be specified with the optional parameter $filter.
	 *
	 * @param    array $filter Optional filter array
	 *
	 * @return    array
	 */
	public function fetchStudyFields(array $filter = array()) {

		$select = 'SELECT s.*';
		$from = 'FROM `list_study_fields` s';
		$where = '';
		$order = 'ORDER BY `name` ASC';
		$limit = '';

		/* Checks if any filter is set */
		if (!empty($filter)) {

			/* Creates the LIMIT clause */
			if (array_key_exists('limit', $filter)) {
				$limit = 'LIMIT '.$filter['limit'];
				unset($filter['limit']);
			}

			/* Checks if filter is still not empty */
			if (!empty($filter)) {
				$where = 'WHERE';

				/* Creates the WHERE clause from the rest of the filter array */
				foreach ($filter as $key => $value) {
					$where .= ' s.`'.$key.'` LIKE "'.$value.'" AND';
				}
				$where = substr($where, 0, -3);
			}
		}
		unset($filter);

		/* Combines everything to the complete query */
		$query = $select.' '.$from.' '.$where.' '.$order.' '.$limit.';';

		return $this->getData($query);
	}


	/**
	 * Returns an array with journals. All Columns are returned.
	 * A filter can be specified with the optional parameter $filter.
	 *
	 * @param    array $filter Optional filter array
	 *
	 * @return    array
	 */
	public function fetchJournals(array $filter = array()) {

		$select = 'SELECT j.*';
		$from = 'FROM `list_journals` j';
		$where = '';
		$order = 'ORDER BY `name` ASC';
		$limit = '';

		/* Checks if any filter is set */
		if (!empty($filter)) {

			/* Creates the LIMIT clause */
			if (array_key_exists('limit', $filter)) {
				$limit = 'LIMIT '.$filter['limit'];
				unset($filter['limit']);
			}

			/* Checks if filter is still not empty */
			if (!empty($filter)) {
				$where = 'WHERE';

				/* Creates the WHERE clause from the rest of the filter array */
				foreach ($filter as $key => $value) {
					$where .= ' j.`'.$key.'` LIKE "'.$value.'" AND';
				}
				$where = substr($where, 0, -3);
			}
		}
		unset($filter);

		/* Combines everything to the complete query */
		$query = $select.' '.$from.' '.$where.' '.$order.' '.$limit.';';

		return $this->getData($query);
	}


	public function fetchPublishers(array $filter = array()) {

		$select = 'SELECT p.*';
		$from = 'FROM `list_publishers` p';
		$where = '';
		$order = 'ORDER BY `name` ASC';
		$limit = '';

		/* Checks if any filter is set */
		if (!empty($filter)) {

			/* Creates the LIMIT clause */
			if (array_key_exists('limit', $filter)) {
				$limit = 'LIMIT '.$filter['limit'];
				unset($filter['limit']);
			}

			/* Checks if filter is still not empty */
			if (!empty($filter)) {
				$where = 'WHERE';

				/* Creates the WHERE clause from the rest of the filter array */
				foreach ($filter as $key => $value) {
					$where .= ' p.`'.$key.'` LIKE "'.$value.'" AND';
				}
				$where = substr($where, 0, -3);
			}
		}
		unset($filter);

		/* Combines everything to the complete query */
		$query = $select.' '.$from.' '.$where.' '.$order.' '.$limit.';';

		return $this->getData($query);
	}


	public function fetchUsers(array $filter = array()) {

		$select = 'SELECT u.*';
		$from = 'FROM `list_users` u';
		$where = '';
		$order = 'ORDER BY `name` ASC';
		$limit = '';

		/* Checks if any filter is set */
		if (!empty($filter)) {

			/* Creates the LIMIT clause */
			if (array_key_exists('limit', $filter)) {
				$limit = 'LIMIT '.$filter['limit'];
				unset($filter['limit']);
			}

			/* Checks if filter is still not empty */
			if (!empty($filter)) {
				$where = 'WHERE';

				/* Creates the WHERE clause from the rest of the filter array */
				foreach ($filter as $key => $value) {
					$where .= ' u.`'.$key.'` LIKE "'.$value.'" AND';
				}
				$where = substr($where, 0, -3);
			}
		}
		unset($filter);

		/* Combines everything to the complete query */
		$query = $select.' '.$from.' '.$where.' '.$order.' '.$limit.';';

		return $this->getData($query);
	}


	/**
	 * Returns an array with all years.
	 *
	 * @return array
	 */
	public function fetchYears() {

		$query = 'SELECT DISTINCT YEAR(`date_published`) AS `year`
					FROM `list_publications`
					ORDER BY `year` DESC';

		return $this->getData($query);
	}


	/**
	 * Returns an array with all months of a specified year.
	 *
	 * @param    string $year The year which months should be returned.
	 *
	 * @return    array
	 */
	// public function fetchMonths($year) {

	// 	$query = 'SELECT `month`
	// 				FROM `list_publications`
	// 				WHERE `year` LIKE '.$year.'
	// 				GROUP BY `month`
	// 				ORDER BY `month` DESC';

	// 	return $this -> getData($query);
	// }

	/**
	 * Returns an array with authors. All Columns are returned.
	 * A filter can be specified with the optional parameter $filter.
	 *
	 * @param    array $filter Optional filter array
	 *
	 * @return    array
	 */
	public function fetchAuthors(array $filter = array()) {

		$select = 'SELECT a.*';
		$from = 'FROM `list_authors` a';
		$join = '';
		$where = '';
		$order = 'ORDER BY `family` ASC';
		$limit = '';

		/* Checks if any filter is set */
		if (!empty($filter)) {

			// /* Creates the SELECT clause */
			// if (array_key_exists('select', $filter)) {
			// 	$select = 'SELECT';

			// 	foreach ($filter['select'] as $key => $value) {
			// 		$select .= ' a.`'.$value.'`,';
			// 	}
			// 	$select = substr($select, 0, -1);
			// 	unset($filter['select']);
			// }

			/* Creates the LIMIT clause */
			if (array_key_exists('limit', $filter)) {
				$limit = 'LIMIT '.$filter['limit'];
				unset($filter['limit']);
			}

			/* Checks if filter is still not empty */
			if (!empty($filter)) {
				$where = 'WHERE';

				/* Creates the JOIN clause if needed */
				if (array_key_exists('publication_id', $filter)) {
					$from = 'FROM `rel_publ_to_authors` rb';    // Better SQL performance this way
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
		$query = $select.' '.$from.' '.$join.' '.$where.' '.$order.' '.$limit.';';

		return $this->getData($query);
	}


	/**
	 * Returns an array with publications.
	 * A filter can be specified with the optional parameter $filter.
	 *
	 * @param    array $filter Optional filter array
	 *
	 * @return    array
	 */
	public function fetchPublications(array $filter = array()) {

		$select = 'SELECT t.`name` AS `type`, j.`name` AS `journal`, pub.`name` AS `publisher`, p.*';
		$from = 'FROM `list_publications` p';
		$join = 'LEFT JOIN `list_types` t ON (t.`id` = p.`type_id`)';
		$join .= ' LEFT JOIN `list_journals` j ON (j.`id` = p.`journal_id`)';
		$join .= ' LEFT JOIN `list_publishers` pub ON (pub.`id` = p.`publisher_id`)';
		$where = '';
		$order = 'ORDER BY `date_added` DESC';
		$limit = '';

		/* Checks if any filter is set */
		if (!empty($filter)) {

			/* Creates the LIMIT clause */
			if (array_key_exists('limit', $filter)) {
				$limit = 'LIMIT '.$filter['limit'];
				unset($filter['limit']);
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
				if (array_key_exists('keyword_id', $filter)) {
					$join .= ' JOIN `rel_publication_keywords` rk ON (rk.`publication_id` = p.`id`)';
					$where .= ' rk.`keyword_id` LIKE "'.$filter['keyword_id'].'" AND';
					unset($filter['keyword_id']);
				}
				if (array_key_exists('year_published', $filter)) {
					$where .= ' YEAR(p.`date_published`) LIKE "'.$filter['year_published'].'" AND';
					unset($filter['year_published']);
				}
				if (array_key_exists('month_published', $filter)) {
					$where .= ' MONTH(p.`date_published`) LIKE "'.$filter['month_published'].'" AND';
					unset($filter['month_published']);
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
		$query = $select.' '.$from.' '.$join.' '.$where.' '.$order.' '.$limit.';';

		return $this->getData($query);
	}


	public function fetchRoles(array $filter = array()) {

		$select = 'SELECT r.`id`, r.`name`';
		$from = 'FROM `list_roles` r';
		$join = '';
		$where = '';
		$order = 'ORDER BY r.`name` ASC';
		$limit = '';

		if (!empty($filter)) {
			$where = 'WHERE';

			if (array_key_exists('user_id', $filter)) {
				$join .= ' JOIN `rel_user_roles` ru ON (ru.`role_id` = r.`id`)';
				$where .= ' ru.`user_id` = '.$filter['user_id'].' AND';
				unset($filter['user_id']);
			}
			foreach ($filter as $key => $value) {
				$where .= ' r.`'.$key.'` LIKE "'.$value.'" AND';
			}
			$where = substr($where, 0, -3);
		}
		unset($filter);

		/* Combines everything to the complete query */
		$query = $select.' '.$from.' '.$join.' '.$where.' '.$order.' '.$limit.';';

		return $this->getData($query);
	}


	public function fetchPermissions(array $filter = array()) {

		$select = 'SELECT p.`id`, p.`name`';
		$from = 'FROM `list_permissions` p';
		$join = '';
		$where = '';
		$order = 'ORDER BY p.`name` ASC';
		$limit = '';

		if (!empty($filter)) {
			$where = 'WHERE';

			if (array_key_exists('role_id', $filter)) {
				$join .= ' JOIN `rel_roles_permissions` rr ON (rr.`permission_id` = p.`id`)';
				$where .= ' rr.`role_id` = '.$filter['role_id'].' AND';
				unset($filter['role_id']);
			}
			foreach ($filter as $key => $value) {
				$where .= ' p.`'.$key.'` LIKE "'.$value.'" AND';
			}
			$where = substr($where, 0, -3);
		}
		unset($filter);

		/* Combines everything to the complete query */
		$query = $select.' '.$from.' '.$join.' '.$where.' '.$order.' '.$limit.';';

		return $this->getData($query);
	}
}
