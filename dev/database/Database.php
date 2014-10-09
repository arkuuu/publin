<?php

class Database extends mysqli {
	
	private $host = 'localhost';
	private $user = 'root';
	private $password = 'root';
	private $database = 'dev';
	private $charset = 'utf8';

	private $num_entries;


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
	 *
	 * @param string	$query	the sql query
	 * @return array
	 */
	public function query($query) {

		$return = array();
		$result = parent::query($query);
		$this -> num_entries = $result -> num_rows;

		while ($entry = $result -> fetch_assoc()) {
			$return[] = $entry;
		}

		return $return;
	}


	/**
	 * Returns the number of entries found or affected by last action.
	 *
	 * @return int
	 */
	public function getNumEntries() {
		return $this -> num_entries;
	}


	public function __destruct() {
		parent::close();	// TODO: really as destructor? Not a real method?
	}
}

?>
