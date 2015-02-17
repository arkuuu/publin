<?php


namespace publin\src;

class StudyFieldView extends ViewWithPublications {

	public function __construct(StudyField $study_field) {

		parent::__construct($study_field, 'studyfield');
	}
}
