<?php


namespace publin\src;

/**
 * Class TypeRepository
 *
 * @package publin\src
 */
class TypeRepository extends Repository {


    public function reset()
    {
        parent::reset();
        $this->select = 'SELECT self.*';
        $this->from = 'FROM `types` self';

        return $this;
    }


	/**
	 * @return Type[]
	 */
	public function find() {

		$result = parent::find();
		$types = array();

		foreach ($result as $row) {
			$types[] = new Type($row);
		}

		return $types;
	}


	/**
	 * @return Type|false
	 */
	public function findSingle() {

        $result = parent::findSingle();

		if ($result) {
			return new Type($result);
		}
		else {
			return false;
		}
	}
}
