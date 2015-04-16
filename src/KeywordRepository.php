<?php


namespace publin\src;

/**
 * Class KeywordRepository
 *
 * @package publin\src
 */
class KeywordRepository extends Repository {


	/**
	 * @return $this
	 */
	public function select() {

		$this->select = 'SELECT self.*';
		$this->from = 'FROM `keywords` self';

		return $this;
	}


	/**
	 * @param      $column
	 * @param      $comparator
	 * @param      $value
	 * @param null $function
	 *
	 * @return $this
	 */
	public function where($column, $comparator, $value, $function = null) {

		if ($column === 'publication_id') {
			$table = 'publications_keywords';
			$this->join($table, 'keyword_id', '=', 'id');
		}
		else {
			$table = 'self';
		}

		return parent::where($column, $comparator, $value, $function, $table);
	}


	/**
	 * @return Keyword[]
	 */
	public function find() {

		$result = parent::find();
		$keywords = array();

		foreach ($result as $row) {
			$keywords[] = new Keyword($row);
		}

		return $keywords;
	}


	/**
	 * @return Keyword|false
	 */
	public function findSingle() {

		if ($result = parent::findSingle()) {
			return new Keyword($result);
		}
		else {
			return false;
		}
	}
}
