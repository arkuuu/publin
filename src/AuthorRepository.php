<?php


namespace publin\src;

/**
 * Class AuthorRepository
 *
 * @package publin\src
 */
class AuthorRepository extends Repository {


	/**
	 * @return $this
	 */
	public function select() {

		$this->select = 'SELECT self.*';
		$this->from = 'FROM `authors` self';

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
			$table = 'publications_authors';
			$this->join($table, 'author_id', '=', 'id');
		}
		else {
			$table = 'self';
		}

		return parent::where($column, $comparator, $value, $function, $table);
	}


	/**
	 * @param $column
	 * @param $order
	 *
	 * @return $this
	 */
	public function order($column, $order) {

		if ($column === 'priority') {
			$table = 'publications_authors';
		}
		else {
			$table = 'self';
		}

		return parent::order($column, $order, $table);
	}


	/**
	 * @return Author[]
	 */
	public function find() {

		$result = parent::find();
		$authors = array();

		foreach ($result as $row) {
			$authors[] = new Author($row);
		}

		return $authors;
	}


	/**
	 * @return Author|false
	 */
	public function findSingle() {

		if ($result = parent::findSingle()) {
			return new Author($result);
		}
		else {
			return false;
		}
	}
}
