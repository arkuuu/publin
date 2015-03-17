<?php


namespace publin\src;

class ViewWithPublications extends View {

	/**
	 * @var ObjectWithPublications
	 */
	protected $object;


	/**
	 * @param ObjectWithPublications $object
	 * @param array                  $type
	 * @param array                  $errors
	 */
	public function __construct(ObjectWithPublications $object, $type, array $errors = array()) {

		parent::__construct($type, $errors);
		$this->object = $object;
	}


	/**
	 * @return    string
	 */
	public function showPageTitle() {

		return $this->html($this->showName());
	}


	/**
	 * @return string
	 */
	public function showName() {

		$name = $this->object->getName();

		if ($name) {
			return $this->html($name);
		}
		else {
			// TODO: maybe log here because this should never happen?
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


	/**
	 * @return int
	 */
	public function showPublicationsNum() {

		return $this->object->getPublicationsNum();
	}
}
