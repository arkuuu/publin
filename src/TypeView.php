<?php


namespace publin\src;

class TypeView extends ViewWithPublications {

	private $type;


	public function __construct(Type $type, array $publications) {

		parent::__construct($publications, 'type');
		$this->type = $type;
	}


	public function showPageTitle() {

		return $this->html($this->showName());
	}


	public function showName() {

		return $this->html($this->type->getName());
	}
}
