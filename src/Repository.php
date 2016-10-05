<?php


namespace publin\src;

use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class Repository
 *
 * @package publin\src
 */
class Repository {

	public $select;
	public $from;
	public $join;
	public $where;
	public $order;
	public $limit;
	public $values_to_bind;
	public $columns_to_bind;
	protected $db;


	/**
	 * @param Database $db
	 */
	public function __construct(Database $db) {

		$this->db = $db;
		$this->reset();
	}


    /**
     * Resets the repository to its defaults.
     *
     * Overwrite this if you need to select special fields every time.
     *
     * @return $this
     */
    public function reset() {

		$this->select = '';
		$this->join = '';
		$this->where = '';
		$this->order = '';
		$this->limit = '';
		$this->values_to_bind = array();
		$this->columns_to_bind = array();

        return $this;
	}


	/**
	 * @param       $table
	 * @param array $columns
	 *
	 * @return $this
	 */
	public function select($table, array $columns = null) {

		if (!empty($columns)) {
			$columns = 'self.`'.implode('`, self.`', $columns).'`';
		}
		else {
			$columns = 'self.*';
		}
		$this->select = 'SELECT '.$columns;
		$this->from = 'FROM `'.$table.'` self';

		return $this;
	}


	/**
	 * @param        $column
	 * @param        $order
	 * @param string $table
	 *
	 * @return $this
	 */
	public function order($column, $order, $table = 'self') {

		$order = ($order === 'ASC') ? 'ASC' : 'DESC';
		if ($table !== 'self') {
			$table = '`'.$table.'`';
		}

		if (empty($this->order)) {
			$this->order = 'ORDER BY '.$table.'.`'.$column.'` '.$order;
		}
		else {
			$this->order .= ', '.$table.'.`'.$column.'` '.$order;
		}

		return $this;
	}


	/**
	 * @param     $limit
	 * @param int $offset
	 *
	 * @return $this
	 */
	public function limit($limit, $offset = 0) {

		if (!is_numeric($limit) || !is_numeric($offset)) {
			throw new InvalidArgumentException('LIMIT and OFFSET values must be numeric');
		}

		$this->values_to_bind[] = (int)$offset;
		$this->values_to_bind[] = (int)$limit;
		$this->limit = 'LIMIT ?,?';

		return $this;
	}


	/**
	 * @param        $column
	 * @param        $comparator
	 * @param        $value
	 * @param null   $function
	 * @param string $table
	 *
	 * @return $this
	 */
	public function where($column, $comparator, $value, $function = null, $table = 'self') {

		if (!$comparator = $this->isAllowedComparator($comparator)) {
			throw new UnexpectedValueException('disallowed comparator in where clause');
		}
		if (isset($function) && !$function = $this->isAllowedFunction($function)) {
			throw new UnexpectedValueException('disallowed function in where clause');
		}

		$this->values_to_bind[] = $value;

		$suffix = (empty($this->where)) ? 'WHERE' : ' AND';
		if ($table !== 'self') {
			$table = '`'.$table.'`';
		}
		if ($function) {
			$column = $function.'('.$table.'.`'.$column.'`)';
		}
		else {
			$column = $table.'.`'.$column.'`';
		}

		$this->where .= $suffix.' '.$column.' '.$comparator.' ?';

		return $this;
	}


	/**
	 * @param $comparator
	 *
	 * @return bool
	 */
	private function isAllowedComparator($comparator) {

		$allowed = array('=', '<', '>', '<=', '>=', 'LIKE');
		$key = array_search($comparator, $allowed, true);
		if ($key !== false) {
			return $allowed[$key];
		}
		else {
			return false;
		}
	}


	/**
	 * @param $function
	 *
	 * @return bool
	 */
	private function isAllowedFunction($function) {

		$allowed = array('MONTH', 'YEAR', 'DISTINCT', 'COUNT');
		$key = array_search($function, $allowed, true);
		if ($key !== false) {
			return $allowed[$key];
		}
		else {
			return false;
		}
	}


// This does not work with InnoDB and MySQL < 5.6
//	public function match(array $columns, $value, $boolean_mode = false) {
//
//		$columns = 'self.`'.implode('`, self.`', $columns).'`';
//		$suffix = (empty($this->where)) ? 'WHERE' : ' AND';
//		$boolean_mode = ($boolean_mode) ? ' IN BOOLEAN MODE' : '';
//
//		$this->values_to_bind[] = $value;
//		$this->where .= $suffix.' MATCH('.$columns.') AGAINST (?'.$boolean_mode.')';
//
//		return $this;
//	}

	/**
	 * @param      $foreign_table
	 * @param      $foreign_column
	 * @param      $comparator
	 * @param      $column
	 * @param null $join_type
	 *
	 * @return $this
	 */
	public function join($foreign_table, $foreign_column, $comparator, $column, $join_type = null) {

		if (!$comparator = $this->isAllowedComparator($comparator)) {
			throw new UnexpectedValueException('disallowed comparator in where clause');
		}
		if (isset($join_type) && !$join_type = $this->isAllowedJoinType($join_type)) {
			throw new UnexpectedValueException('disallowed join type in where clause');
		}

		if (!empty($this->join)) {
			$this->join .= ' ';
		}

		$this->join .= $join_type.' JOIN `'.$foreign_table.'` ON (`'.$foreign_table.'`.`'.$foreign_column.'` '.$comparator.' self.`'.$column.'`)';

		return $this;
	}


	/**
	 * @param $join_type
	 *
	 * @return bool
	 */
	private function isAllowedJoinType($join_type) {

		$allowed = array('LEFT', 'RIGHT', 'INNER', 'OUTER');
		$key = array_search($join_type, $allowed, true);
		if ($key !== false) {
			return $allowed[$key];
		}
		else {
			return false;
		}
	}


	/**
	 * @return array
	 * @throws exceptions\DBDuplicateEntryException
	 * @throws exceptions\DBForeignKeyException
	 */
	public function find() {

		$query = $this->select.' '.$this->from.' '.$this->join.' '.$this->where.' '.$this->order.' '.$this->limit.';';
		$this->db->prepare($query);
		foreach ($this->values_to_bind as $key => $value) {
			$this->db->bindValue($key + 1, $value);
		}

		$this->db->execute();

		return $this->db->fetchAll();
	}


	/**
	 * @return array|false
	 * @throws exceptions\DBDuplicateEntryException
	 * @throws exceptions\DBForeignKeyException
	 */
	public function findSingle() {

		$query = $this->select.' '.$this->from.' '.$this->join.' '.$this->where.' '.$this->order.' LIMIT 0,1;';
		$this->db->prepare($query);
		foreach ($this->values_to_bind as $key => $value) {
			$this->db->bindValue($key + 1, $value);
		}

		$this->db->execute();

		return $this->db->fetchSingle();
	}


	/**
	 * @return int
	 * @throws exceptions\DBDuplicateEntryException
	 * @throws exceptions\DBForeignKeyException
	 */
	public function count() {

		$query = 'SELECT COUNT(*) '.$this->from.' '.$this->join.' '.$this->where.';';
		$this->db->prepare($query);
		foreach ($this->values_to_bind as $key => $value) {
			$this->db->bindValue($key + 1, $value);
		}

		$this->db->execute();

		return (int)$this->db->fetchColumn();
	}


	/**
	 * @return string
	 */
	public function __toString() {

		$values = implode(', ', $this->values_to_bind);

		return $this->select.' '.$this->from.' '.$this->join.' '.$this->where.' '.$this->order.' '.$this->limit.'; with values '.$values;
	}


	/*	public function update($table, array $columns, array $values) {
		}*/

	/*	public function insert($table, array $values, $duplicate_key = false) {

			$table = '`'.$table.'`';
			$columns = array_keys($values);
			$columns = '`'.implode('`, `', $columns).'`';
			foreach ($values as $value) {
				$this->values_to_bind[] = $value;
			}
			$values = '?'.str_repeat(',?', count($values)-1);
			$on_duplicate = ($duplicate_key === true) ? ' '

			$query = 'INSERT INTO '.$table.' ('.$columns.') VALUES ('.$values.');';

			$values = implode(', ', $this->values_to_bind);

			print_r($query.' with '.$values);
		}*/

	/**
	 * @return int
	 * @throws exceptions\DBDuplicateEntryException
	 * @throws exceptions\DBForeignKeyException
	 */
	public function delete() {

		$query = 'DELETE '.$this->from.' '.$this->join.' '.$this->where.';';
		$this->db->prepare($query);
		foreach ($this->values_to_bind as $key => $value) {
			$this->db->bindValue($key + 1, $value);
		}

		$this->db->execute();

		return (int)$this->db->rowCount();
	}
}


