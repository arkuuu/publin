<?php


namespace publin\src;

class KeywordRepository extends QueryBuilder {


	public function select() {

		$this->select = 'SELECT self.*';
		$this->from = 'FROM `list_keywords` self';

		return $this;
	}


	public function where($column, $comparator, $value, $function = null) {

		if ($column === 'publication_id') {
			$table = 'rel_publication_keywords';
			$this->join($table, 'keyword_id', '=', 'id');
		}
		else {
			$table = 'self';
		}

		return parent::where($column, $comparator, $value, $function, $table);
	}


	public function go() {

		$result = parent::go();
		$keywords = array();

		foreach ($result as $row) {
			$keywords[] = new Keyword($row);
		}

		return $keywords;
	}
}
