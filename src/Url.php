<?php


namespace publin\src;

/**
 * Class Url
 *
 * @package publin\src
 */
class Url extends Entity {

	protected $id;
	protected $name;
	protected $url;


	/**
	 * @return string|null
	 */
	public function getId() {

		return $this->id;
	}


	/**
	 * @return string|null
	 */
	public function getName() {

		return $this->name;
	}


	/**
	 * @return string|null
	 */
	public function getUrl() {

		return $this->url;
	}
}
