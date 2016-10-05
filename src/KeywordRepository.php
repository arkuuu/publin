<?php


namespace publin\src;

/**
 * Class KeywordRepository
 *
 * @package publin\src
 */
class KeywordRepository extends Repository {


    public function reset()
    {
        parent::reset();
        $this->select = 'SELECT self.*';
        $this->from = 'FROM `keywords` self';

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
            $table = 'publications_keywords';
            $this->join($table, 'keyword_id', '=', 'id');
        }

        parent::where($column, $comparator, $value, $function, $table);

        return $this;
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

	    $result = parent::findSingle();

		if ($result) {
			return new Keyword($result);
		}
		else {
			return false;
		}
	}
}
