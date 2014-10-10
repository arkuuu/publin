<?php

class Database extends mysqli {
	
	private $host = 'localhost';
	private $user = 'root';
	private $password = 'root';
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
		parent::__construct($this -> host, $this -> user, $this -> password, $this -> database);

		/* Stops if the connection cannot be established */
		if ($this -> connect_errno) {
			die('NO CONNECTION TO DATABASE');	// TODO don't use die!
		}
		/* Sets the charset used for transmission */
		$this -> set_charset($this -> charset);
	}


	/**
	 * Returns data from query. Returns an array (rows) with arrays (columns) inside if
	 * multiple entries were found or an array (columns) if a single entry was found.
	 * The last fetched data can be recovered using getLastData().
	 * The last executed query can be recovered using getLastQuery().
	 *
	 * @param string	$query	the sql query
	 * @return array
	 */
	public function getData($query) {

		$this -> last_data = array();
		$this -> last_query = $query;

		$result = parent::query($query);
		$this -> num_data = $result -> num_rows;

		while ($entry = $result -> fetch_assoc()) {
			$this -> last_data[] = $entry;
		}

		// $this -> last_data = $data;

		return $this -> last_data;
	}


	/**
	 * Returns the last fetched data
	 *
	 * @return array
	 */
	public function getLastData() {
		return $this -> last_data;
	}


	/**
	 * Returns the last executed query.
	 *
	 * @return string
	 */
	public function getLastQuery() {
		return $this -> last_query;
	}


	/**
	 * Returns the number of data entries found or affected by last query.
	 *
	 * @return int
	 */
	public function getNumData() {
		return $this -> num_data;
	}


	public function __destruct() {
		parent::close();	// TODO: really as destructor? Not a real method?
	}
}

?>
