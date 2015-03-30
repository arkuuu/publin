<?php


namespace publin\src;

class AuthorRepository extends QueryBuilder {


	public function select() {

		return parent::select('list_authors');
	}


	public function where($column, $comparator, $value, $function = null) {

		if ($column === 'publication_id') {
			$table = 'rel_publ_to_authors';
			$this->join($table, 'author_id', '=', 'id');
		}
		else {
			$table = 'self';
		}

		return parent::where($column, $comparator, $value, $function, $table);
	}


	public function go() {

		$result = parent::go();
		$authors = array();

		foreach ($result as $row) {
			$authors[] = new Author($row);
		}

		return $authors;
	}
}
