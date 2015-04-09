<?php


namespace publin\src;

class AuthorRepository extends Repository {


	public function select() {

		$this->select = 'SELECT self.*';
		$this->from = 'FROM `authors` self';

		return $this;
	}


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
	 * @return Author
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
