<?php

require_once 'View.php';

class SubmitView extends View {

	private $model;
	private $submit_mode = 'form';


	public function __construct(SubmitModel $model, $submit_mode) {

		if (in_array($submit_mode, array('start', 'form', 'preview', 'done', 'match'))) {
			$this -> submit_mode = $submit_mode;
		}

		parent::__construct('submit');
		$this -> model = $model;
	}


	public function isForm() {
		if ($this -> submit_mode == 'form') {
			return true;
		}
		else {
			return false;
		}
	}

	public function isPreview() {
		if ($this -> submit_mode == 'preview') {
			return true;
		}
		else {
			return false;
		}
	}

	public function isDone() {
		if ($this -> submit_mode == 'done') {
			return true;
		}
		else {
			return false;
		}
	}


	public function showPageTitle() {
		return 'Submit publication';
	}


	public function showPreview() {
		$publication = $this -> model -> getPublication();

		if (!empty($publication)) {
			$sub_view = new PublicationView($publication);
			return $sub_view -> displayContentOnly();
		}
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


	// public function listAuthorOptions() {
	// 	$string = '';
	// 	$authors = $this -> model -> createAuthors(false);

	// 	foreach ($authors as $author) {
	// 		$string .= '<option value="'.$author -> getId().'">'.$author -> getLastName().', '.$author -> getAcademicTitle().' '.$author -> getFirstName().'</option>'; 
	// 	}

	// 	return $string;
	// }


	// public function listKeyTermOptions() {
	// 	$string = '';
	// 	$key_terms = $this -> model -> createKeyTerms();

	// 	foreach ($key_terms as $key_term) {
	// 		$string .= '<option value="'.$key_term -> getId().'">'.$key_term -> getName().'</option>'; 
	// 	}

	// 	return $string;
	// }


	public function listErrors() {
		$errors = $this -> model -> getErrors();

		if (!empty($errors)) {
			$string = '';
			foreach ($errors as $error) {
				$string .= '<li>'.$error.'</li>';
			}

			return $string;
		}
		else {
			return false;
		}
	}

}
