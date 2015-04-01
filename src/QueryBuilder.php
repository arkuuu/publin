<?php


namespace publin\src;

use InvalidArgumentException;
use UnexpectedValueException;

class QueryBuilder {

	public $select;
	public $from;
	public $join;
	public $where;
	public $order;
	public $limit;
	public $values_to_bind;
	public $columns_to_bind;
	protected $db;


	public function __construct(PDODatabase $db) {

		$this->db = $db;
		$this->reset();
	}


	public function reset() {

		$this->select = '';
		$this->join = '';
		$this->where = '';
		$this->order = '';
		$this->limit = '';
		$this->values_to_bind = array();
		$this->columns_to_bind = array();
	}


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


	public function order($column, $order) {

		$order = ($order === 'ASC') ? 'ASC' : 'DESC';

		if (empty($this->order)) {
			$this->order = 'ORDER BY self.`'.$column.'` '.$order;
		}
		else {
			$this->order .= ', self.`'.$column.'` '.$order;
		}

		return $this;
	}


	public function limit($limit, $offset = 0) {

		if (!is_numeric($limit) || !is_numeric($offset)) {
			throw new InvalidArgumentException('LIMIT and OFFSET values must be numeric');
		}

		$this->values_to_bind[] = (int)$offset;
		$this->values_to_bind[] = (int)$limit;
		$this->limit = 'LIMIT ?,?';

		return $this;
	}


	public function where($column, $comparator, $value, $function = null, $table = 'self') {

		if (!$comparator = $this->isAllowedComparator($comparator)) {
			throw new UnexpectedValueException('disallowed comparator in where clause');
		}
		if (isset($function) && !$function = $this->isAllowedFunction($function)) {
			throw new UnexpectedValueException('disallowed function in where clause');
		}

		$this->values_to_bind[] = $value;

		$suffix = (empty($this->where)) ? 'WHERE' : ' AND';
		if ($function) {
			$column = $function.'(`'.$column.'`)';
		}
		if ($table !== 'self') {
			$table = '`'.$table.'`';
		}

		$this->where .= $suffix.' '.$table.'.`'.$column.'` '.$comparator.' ?';

		return $this;
	}


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


	public function find() {

		$query = $this->select.' '.$this->from.' '.$this->join.' '.$this->where.' '.$this->order.' '.$this->limit.';';
		$this->db->prepare($query);
		foreach ($this->values_to_bind as $key => $value) {
			$this->db->bindValue($key + 1, $value);
		}

		$this->db->execute();

		return $this->db->fetchAll();
	}


	public function findSingle() {

		$query = $this->select.' '.$this->from.' '.$this->join.' '.$this->where.' '.$this->order.' LIMIT 0,1;';
		$this->db->prepare($query);
		foreach ($this->values_to_bind as $key => $value) {
			$this->db->bindValue($key + 1, $value);
		}

		$this->db->execute();

		return $this->db->fetchSingle();
	}


	public function count() {

		$query = 'SELECT COUNT(*) '.$this->from.' '.$this->join.' '.$this->where.';';
		$this->db->prepare($query);
		foreach ($this->values_to_bind as $key => $value) {
			$this->db->bindValue($key + 1, $value);
		}

		$this->db->execute();

		return (int)$this->db->fetchColumn();
	}


	public function __toString() {

		return $this->select.' '.$this->from.' '.$this->join.' '.$this->where.' '.$this->order.' '.$this->limit.';';
	}


	public function update($table, array $columns, array $values) {
	}


	public function insert($table, array $columns, array $values) {
	}


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


