<?php


namespace publin\src;

use PDO;
use PDOException;
use PDOStatement;

class PDODatabase {

	const HOST = 'localhost';
	const USER = 'root';
	const PASSWORD = 'root';
	const DATABASE = 'dev';
	const CHARSET = 'UTF8';

	/**
	 * @var PDO
	 */
	private $pdo;
	/**
	 * @var PDOStatement
	 */
	private $stmt;


	public function __construct() {

		$dsn = 'mysql:host='.self::HOST.';dbname='.self::DATABASE.';charset='.self::CHARSET;
		$options = array(
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			//PDO::ATTR_PERSISTENT => false,
		);

		$this->pdo = new PDO($dsn, self::USER, self::PASSWORD, $options);

		if (version_compare(PHP_VERSION, '5.3.6', '<')) {
			$this->pdo->exec('SET NAMES '.self::CHARSET);
		}
	}


	public function beginTransaction() {

		return $this->pdo->beginTransaction();
	}


	public function endTransaction() {

		return $this->pdo->commit();
	}


	public function cancelTransaction() {

		return $this->pdo->rollBack();
	}


	public function prepare($query) {

		$this->stmt = $this->pdo->prepare($query);
		// TODO: really no return?

	}


	public function bind($parameter, $value, $type = null) {

		if (is_null($type)) {
			switch (true) {
				case is_int($value):
					$type = PDO::PARAM_INT;
					break;
				case is_bool($value):
					$type = PDO::PARAM_BOOL;
					break;
				case is_null($value):
					$type = PDO::PARAM_NULL;
					break;
				default:
					$type = PDO::PARAM_STR;
					break;
			}
		}

		// TODO what if stmt is not set? Exception?
		return $this->stmt->bindValue($parameter, $value, $type);
	}


	public function fetchAll($fetch_style = PDO::FETCH_ASSOC) {

		$this->execute(); // TODO: really here?

		return $this->stmt->fetchAll($fetch_style);
	}


	public function execute(array $parameters = null) {

		try {
			return $this->stmt->execute($parameters);
		}
		catch (PDOException $e) {
			// TODO
		}
	}


	public function fetchSingle($fetch_style = PDO::FETCH_ASSOC) {

		$this->execute(); // TODO: really here?

		return $this->stmt->fetch($fetch_style);
	}


	public function resultCount() {

		return $this->stmt->rowCount(); // TODO it's told to not work every time
	}


	public function lastInsertId() {

		return $this->pdo->lastInsertId();
	}


	public function debugDumpParams() {

		return $this->stmt->debugDumpParams();
	}


	public function executeAndReturnAffectedRows($query) {

		try {
			return $this->pdo->exec($query);
		}
		catch (PDOException $e) {
			//TODO
		}
	}


//	public function query($query){
//
//		$stmt = $this->pdo->query($query);
//
//	}
}
