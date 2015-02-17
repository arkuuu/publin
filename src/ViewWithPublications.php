<?php


namespace publin\src;

class ViewWithPublications extends View {

	/**
	 * @var ObjectWithPublications
	 */
	protected $object;


	/**
	 * @param ObjectWithPublications $object
	 * @param                        $type
	 */
	public function __construct(ObjectWithPublications $object, $type) {

		parent::__construct($type);
		$this->object = $object;
	}


	/**
	 * @return    string
	 */
	public function showPageTitle() {

		return $this->showName();
	}


	/**
	 * @return string
	 */
	public function showName() {

		$name = $this->object->getName();

		if ($name) {
			return $name;
		}
		else {
			return 'No name';
		}
	}


	/**
	 * @return string
	 */
	public function showPublications() {

		$string = '';

		foreach ($this->object->getPublications() as $publication) {
			$string .= '<li>'.$this->showCitation($publication).'</li>'."\n";
		}

		if (!empty($string)) {
			return $string;
		}
		else {
			return '<li>no publications found</li>';
		}
	}
}
