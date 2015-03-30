<?php


namespace publin\src;

use PDO;
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
	public $pdo;
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


	public function bindValue($parameter, $value, $type = null) {

		if (empty($type)) {
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


	public function bindColumn($parameter, $column) {

		return $this->stmt->bindColumn($parameter, $column, PDO::PARAM_STR);
	}


	public function fetchAll($fetch_style = PDO::FETCH_ASSOC) {


		return $this->stmt->fetchAll($fetch_style);
	}


	public function execute(array $parameters = null) {

		return $this->stmt->execute($parameters);
	}


	public function fetchSingle($fetch_style = PDO::FETCH_ASSOC) {


		return $this->stmt->fetch($fetch_style);
	}


	public function fetchColumn() {

		return $this->stmt->fetchColumn();
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

		return $this->pdo->exec($query);
	}


//	public function query($query){
//
//		$stmt = $this->pdo->query($query);
//
//	}
}
