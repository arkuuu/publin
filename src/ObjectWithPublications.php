<?php


namespace publin\src;

use InvalidArgumentException;

class ObjectWithPublications {

	/**
	 * @var Publication[]
	 */
	protected $publications;


	/**
	 * @param array         $data
	 * @param Publication[] $publications
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct(array $data, array $publications = array()) {

		foreach ($data as $property => $value) {
			if (property_exists($this, $property)) {
				$this->$property = $value;
			}
		}
		$this->setPublications($publications);
	}


	/**
	 * @return Publication[]
	 */
	public function getPublications() {

		return $this->publications;
	}


	/**
	 * @param Publication[] $publications
	 *
	 * @return bool
	 * @throws InvalidArgumentException
	 */
	public function setPublications(array $publications) {

		$this->publications = array();

		foreach ($publications as $publication) {

			if ($publication instanceof Publication) {
				$this->publications[] = $publication;
			}
			else {
				throw new InvalidArgumentException('must be array with Publication objects');
			}
		}

		return true;
	}


	/**
	 * @return int
	 */
	public function getPublicationsNum() {

		return count($this->publications);
	}
}
