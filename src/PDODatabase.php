<?php


namespace publin\src;

use PDO;
use PDOException;
use PDOStatement;
use publin\src\exceptions\DBDuplicateEntryException;
use publin\src\exceptions\DBForeignKeyException;

class PDODatabase {


	/**
	 * @var PDO
	 */
	public $pdo;
	/**
	 * @var PDOStatement
	 */
	private $stmt;


	public function __construct() {

		$dsn = 'mysql:host='.Config::SQL_HOST.';dbname='.Config::SQL_DATABASE.';charset=UTF8';
		$options = array(
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			//PDO::ATTR_PERSISTENT => false,
			//PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		);

		$this->pdo = new PDO($dsn, Config::SQL_USER, Config::SQL_PASSWORD, $options);

		if (version_compare(PHP_VERSION, '5.3.6', '<')) {
			$this->pdo->exec('SET NAMES UTF8');
		}
	}


	public function beginTransaction() {

		return $this->pdo->beginTransaction();
	}


	public function commitTransaction() {

		return $this->pdo->commit();
	}


	public function cancelTransaction() {

		return $this->pdo->rollBack();
	}


	public function prepare($query) {

		$this->stmt = $this->pdo->prepare($query);

		return $this->stmt;
	}


	public function bindValue($parameter, $value, $type = null) {

		if (empty($type)) {
			switch (true) {
				case is_null($value):
					$type = PDO::PARAM_NULL;
					break;
				case is_int($value):
					$type = PDO::PARAM_INT;
					break;
				case is_bool($value):
					$type = PDO::PARAM_BOOL;
					break;
				default:
					$type = PDO::PARAM_STR;
					break;
			}
		}

		// TODO what if stmt is not set? Exception?
		return $this->stmt->bindValue($parameter, $value, $type);
	}


	public function bindColumn($parameter, $column) {

		return $this->stmt->bindColumn($parameter, $column, PDO::PARAM_STR);
	}


	public function fetchAll($fetch_style = PDO::FETCH_ASSOC) {

		return $this->stmt->fetchAll($fetch_style);
	}


	public function execute(array $parameters = null) {

		try {
			return $this->stmt->execute($parameters);
		}
		catch (PDOException $e) {
			if ($e->errorInfo[1] == '1062') {
				throw new DBDuplicateEntryException;
			}
			else if ($e->errorInfo[1] == '1451') {
				throw new DBForeignKeyException;
			}
			else {
				throw $e;
			}
		}
	}


	public function fetchSingle($fetch_style = PDO::FETCH_ASSOC) {

		return $this->stmt->fetch($fetch_style);
	}


	public function fetchColumn() {

		return $this->stmt->fetchColumn();
	}


	public function rowCount() {

		return $this->stmt->rowCount(); // TODO it's told to not work every time when SELECT
	}


	public function lastInsertId() {

		return $this->pdo->lastInsertId();
	}


	public function debugDumpParams() {

		return $this->stmt->debugDumpParams();
	}


	public function executeAndReturnAffectedRows($query) {

		return $this->pdo->exec($query);
	}


	public function query($query) {

		$this->stmt = $this->pdo->query($query);

		return $this->stmt;
	}
}
