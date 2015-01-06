<?php

require_once 'View.php';

class SubmitView extends View {

	private $model;


	public function __construct(SubmitModel $model, $template = 'dev') {

		parent::__construct($template.'/submit.html');
		$this -> model = $model;
	}


	public function showPageTitle() {
		return 'Submit publication';
	}


	public function listTypeOptions() {
		$string = '';
		$types = $this -> model -> createTypes();

		foreach ($types as $type) {
			$string .= '<option value="'.$type -> getId().'">'.$type -> getName().'</option>'; 
		}

		return $string;
	}


	public function listStudyFieldOptions() {
		$string = '';
		$study_fields = $this -> model -> createStudyFields();

		foreach ($study_fields as $study_field) {
			$string .= '<option value="'.$study_field -> getId().'">'.$study_field -> getName().'</option>'; 
		}

		return $string;
	}

}
