<?php

namespace publin\src;

use PDO;
use PDOException;
use PDOStatement;
use publin\config\Config;
use publin\src\exceptions\DBDuplicateEntryException;
use publin\src\exceptions\DBForeignKeyException;

/**
 * Class Database
 *
 * @package publin\src
 */
class Database
{

    /**
     * @var PDO
     */
    public $pdo;

    /**
     * @var PDOStatement
     */
    private $stmt;


    public function __construct()
    {
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


    /**
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }


    /**
     * @return bool
     */
    public function commitTransaction()
    {
        return $this->pdo->commit();
    }


    /**
     * @return bool
     */
    public function cancelTransaction()
    {
        return $this->pdo->rollBack();
    }


    /**
     * @param $query
     *
     * @return PDOStatement
     */
    public function prepare($query)
    {
        $this->stmt = $this->pdo->prepare($query);

        return $this->stmt;
    }


    /**
     * @param      $parameter
     * @param      $value
     * @param null $type
     *
     * @return bool
     */
    public function bindValue($parameter, $value, $type = null)
    {
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


    /**
     * @param $parameter
     * @param $column
     *
     * @return bool
     */
    public function bindColumn($parameter, $column)
    {
        return $this->stmt->bindColumn($parameter, $column, PDO::PARAM_STR);
    }


    /**
     * @param int $fetch_style
     *
     * @return array
     */
    public function fetchAll($fetch_style = PDO::FETCH_ASSOC)
    {
        return $this->stmt->fetchAll($fetch_style);
    }


    /**
     * @param array $parameters
     *
     * @return bool
     * @throws DBDuplicateEntryException
     * @throws DBForeignKeyException
     */
    public function execute(array $parameters = null)
    {
        try {
            return $this->stmt->execute($parameters);
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == '1062') {
                throw new DBDuplicateEntryException;
            } else if ($e->errorInfo[1] == '1451') {
                throw new DBForeignKeyException;
            } else {
                throw $e;
            }
        }
    }


    /**
     * @param int $fetch_style
     *
     * @return array|false
     */
    public function fetchSingle($fetch_style = PDO::FETCH_ASSOC)
    {
        return $this->stmt->fetch($fetch_style);
    }


    /**
     * @param int $column_number
     *
     * @return string
     */
    public function fetchColumn($column_number = 0)
    {
        return $this->stmt->fetchColumn($column_number);
    }


    /**
     * @return int
     */
    public function rowCount()
    {
        return $this->stmt->rowCount(); // TODO it's told to not work every time when SELECT
    }


    /**
     * @return string
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }


    /**
     * @return bool
     */
    public function debugDumpParams()
    {
        return $this->stmt->debugDumpParams();
    }


    /**
     * @param $query
     *
     * @return int
     */
    public function executeAndReturnAffectedRows($query)
    {
        return $this->pdo->exec($query);
    }


    /**
     * @param $query
     *
     * @return PDOStatement
     */
    public function query($query)
    {
        $this->stmt = $this->pdo->query($query);

        return $this->stmt;
    }
}
