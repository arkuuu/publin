<?php


namespace publin\src;

/**
 * Class UrlRepository
 *
 * @package publin\src
 */
class UrlRepository extends Repository {

	/**
	 * @return $this
	 */
	public function select() {

		$this->select = 'SELECT self.*';
		$this->from = 'FROM `urls` self';

		return $this;
	}


	/**
	 * @return Url[]
	 */
	public function find() {

		$result = parent::find();
		$urls = array();

		foreach ($result as $row) {
			$urls[] = new Url($row);
		}

		return $urls;
	}


	/**
	 * @return Url|false
	 */
	public function findSingle() {

		if ($result = parent::findSingle()) {
			return new Url($result);
		}
		else {
			return false;
		}
	}
}
