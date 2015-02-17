<?php


namespace publin\src;

use InvalidArgumentException;

class ObjectWithPublications extends Object {

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

		parent::__construct($data);
		$this->publications = array();
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
}
