<?php


namespace publin\src;

class StudyFieldView extends ViewWithPublications {

	private $study_field;


	public function __construct(StudyField $study_field, array $publications) {

		parent::__construct($publications, 'studyfield');
		$this->study_field = $study_field;
	}


	public function showPageTitle() {

		return $this->html($this->showName());
	}


	public function showName() {

		return $this->html($this->study_field->getName());
	}
}
