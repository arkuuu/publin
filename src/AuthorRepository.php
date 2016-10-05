<?php


namespace publin\src;

/**
 * Class AuthorRepository
 *
 * @package publin\src
 */
class AuthorRepository extends Repository {



    public function reset()
    {
        parent::reset();
        $this->select = 'SELECT self.*';
        $this->from = 'FROM `authors` self';

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

		if ($column === 'publication_id') {
			$table = 'publications_authors';
			$this->join($table, 'author_id', '=', 'id');
		}

		parent::where($column, $comparator, $value, $function, $table);

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

		if ($column === 'priority') {
			$table = 'publications_authors';
		}

		parent::order($column, $order, $table);

        return $this;
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

        $result = parent::findSingle();

        if ($result) {
			return new Author($result);
		}
		else {
			return false;
		}
	}
}
