<?php

namespace publin\src;

use InvalidArgumentException;
use mysqli;
use mysqli_result;
use publin\src\exceptions\SQLDuplicateEntryException;
use publin\src\exceptions\SQLException;
use publin\src\exceptions\SQLForeignKeyException;

/**
 * Handles all database communication.
 *
 * TODO: comment
 */
class Database extends mysqli {

	const HOST = 'localhost';
	const READONLY_USER = 'readonly';
	const READONLY_PASSWORD = 'readonly';
	const WRITEONLY_USER = 'root';
	const WRITEONLY_PASSWORD = 'root';
	const DATABASE = 'dev';
	const CHARSET = 'utf8';

	/**
	 * @var int
	 */
	private $num_rows;


	/**
	 * @throws SQLException
	 */
	public function __construct() {

		/* Calls the constructor of mysqli and creates a connection */
		parent::__construct(self::HOST,
							self::READONLY_USER,
							self::READONLY_PASSWORD,
							self::DATABASE);

		/* Stops if the connection cannot be established */
		if ($this->connect_errno) {
			throw new SQLException($this->connect_error);
		}
		/* Sets the charset used for transmission */
		parent::set_charset(self::CHARSET);
	}


	/**
	 *
	 */
	public function __destruct() {

		parent::close();    // TODO: really as destructor? Not a real method?
	}


	/**
	 * @return mixed
	 */
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
			throw new InvalidArgumentException('where must not be empty when inserting');
		}

		$this->changeToWriteUser();

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

		$this->query($query);

		return $this->insert_id;
	}


	public function changeToWriteUser() {

		$success = parent::change_user(self::WRITEONLY_USER,
									   self::WRITEONLY_PASSWORD,
									   self::DATABASE);

		if ($success && empty($this->error)) {
			return true;
		}
		else {
			throw new SQLException('could not change user: '.$this->error);
		}
	}


	public function query($query) {

		if (false) {
			$msg = str_replace(array("\r\n", "\r", "\n"), ' ', $query);
			$msg = str_replace("\t", '', $msg);
			$file = fopen('./logs/sql.log', 'a');
			fwrite($file, '['.date('d.m.Y H:i:s').'] '
						.$msg."\n");
			fclose($file);
		}

		$result = parent::query($query);

		if (($result === true || $result instanceof mysqli_result) && empty($this->error)) {
			return $result;
		}
		else if (strpos($this->error, 'Duplicate entry') !== false) {
			throw new SQLDuplicateEntryException($this->error);
		}
		else if (strpos($this->error, 'foreign key constraint fails') !== false) {
			throw new SQLForeignKeyException($this->error);
		}
		else {
			throw new SQLException($this->error);
		}
	}


	public function insert($table, array $data) {

		if (empty($data)) {
			throw new InvalidArgumentException('where must not be empty when inserting');
		}

		$this->changeToWriteUser();

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

		$query .= ');';

		$this->query($query);

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

		$this->changeToWriteUser();

		$query = 'DELETE FROM `'.$table.'`';
		$query .= ' WHERE';

		foreach ($where as $key => $value) {
			$query .= ' `'.$key.'` = "'.$value.'" AND';
		}
		$query = substr($query, 0, -3);

		$this->query($query);

		return $this->affected_rows;
	}


	/**
	 * @param       $table
	 * @param array $where
	 * @param array $data
	 *
	 * @return int
	 * @throws SQLException
	 */
	public function updateData($table, array $where, array $data) {

		if (empty($where) || empty($data)) {
			throw new InvalidArgumentException('where and data must not be empty when updating');
		}

		$this->changeToWriteUser();

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

		$this->query($query);

		return $this->affected_rows;
	}


	/**
	 * @param array $filter
	 *
	 * @return array
	 * @throws SQLException
	 */
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
	 * @param $query
	 *
	 * @return array
	 * @throws SQLException
	 */
	public function getData($query) {

		/* Sends query to database */
		$result = $this->query($query);
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
	 * @param array $filter
	 *
	 * @return array
	 * @throws SQLException
	 */
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


	/**
	 * @param array $filter
	 *
	 * @return array
	 * @throws SQLException
	 */
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
