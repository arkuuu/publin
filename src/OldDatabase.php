<?php

namespace publin\src;

use InvalidArgumentException;
use mysqli;
use mysqli_result;
use publin\Config;
use publin\src\exceptions\DBDuplicateEntryException;
use publin\src\exceptions\DBException;
use publin\src\exceptions\DBForeignKeyException;

class OldDatabase extends mysqli {


	/**
	 * @throws DBException
	 */
	public function __construct() {

		/* Calls the constructor of mysqli and creates a connection */
		parent::__construct(Config::SQL_HOST,
							Config::SQL_USER,
							Config::SQL_PASSWORD,
							Config::SQL_DATABASE);

		/* Stops if the connection cannot be established */
		if ($this->connect_errno) {
			throw new DBException($this->connect_error);
		}
		/* Sets the charset used for transmission */
		parent::set_charset('utf8');
	}


	/**
	 *
	 */
	public function __destruct() {

		parent::close();
	}


	/**
	 * @param       $table
	 * @param array $where
	 * @param array $data
	 *
	 * @return int
	 * @throws DBException
	 */
	public function updateData($table, array $where, array $data) {

		if (empty($where) || empty($data)) {
			throw new InvalidArgumentException('where and data must not be empty when updating');
		}

		$this->changeToWriteUser();

		$query = 'UPDATE `'.$table.'`';

		$query .= ' SET';

		foreach ($data as $column => $value) {
			if (is_null($value)) {
				$query .= ' `'.$column.'` = NULL,';
			}
			else {
				$query .= ' `'.$column.'` = "'.self::real_escape_string($value).'",';
			}
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


	public function changeToWriteUser() {

		$success = parent::change_user(Config::SQL_USER,
									   Config::SQL_PASSWORD,
									   Config::SQL_DATABASE);

		if ($success && empty($this->error)) {
			return true;
		}
		else {
			throw new DBException('could not change user: '.$this->error);
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
			throw new DBDuplicateEntryException($this->error);
		}
		else if (strpos($this->error, 'foreign key constraint fails') !== false) {
			throw new DBForeignKeyException($this->error);
		}
		else {
			throw new DBException($this->error);
		}
	}
}
