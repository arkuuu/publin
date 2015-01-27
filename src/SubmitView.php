<?php

require_once 'View.php';

class SubmitView extends View {

	private $model;
	private $submit_mode = 'form';


	public function __construct(SubmitModel $model, $submit_mode) {

		if (in_array($submit_mode, array('start', 'form', 'preview', 'done', 'bibtex'))) {
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

	public function isBibtex() {
		if ($this -> submit_mode == 'bibtex') {
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
		
		$types = $this -> model -> createTypes();
		$selected = $this -> showPostValue('type');

		if ($selected) {
			$string = '<option value disabled>Select...</option>';
		}
		else {
			$string = '<option value selected disabled>Select...</option>';		
		}

		foreach ($types as $type) {
			if ($type -> getName() == $selected) {
				$string .= '<option value="'.$type -> getName().'" selected>'.$type -> getName().'</option>';
			}
			else {
				$string .= '<option value="'.$type -> getName().'">'.$type -> getName().'</option>';
			}

		}

		return $string;
	}


	public function listStudyFieldOptions() {

		$study_fields = $this -> model -> createStudyFields();
		$selected = $this -> showPostValue('study_field');

		if ($selected) {
			$string = '<option value disabled>Select...</option>';
		}
		else {
			$string = '<option value selected disabled>Select...</option>';		
		}

		foreach ($study_fields as $study_field) {
			if ($study_field -> getName() == $selected) {
				$string .= '<option value="'.$study_field -> getName().'" selected>'.$study_field -> getName().'</option>';
			}
			else {
				$string .= '<option value="'.$study_field -> getName().'">'.$study_field -> getName().'</option>';
			}

		}

		return $string;
	}


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


	public function showPostValue($field, $field2 = null, $field3 = null) {
		
		if (isset($field2)) {
			if (isset($field3)) {
				return isset($_SESSION['input'][$field][$field2][$field3]) ? $_SESSION['input'][$field][$field2][$field3] : false;
			}
			else {
				return isset($_SESSION['input'][$field][$field2]) ? $_SESSION['input'][$field][$field2] : false;
			}
		}
		else {
			return isset($_SESSION['input'][$field]) ? $_SESSION['input'][$field] : false;
		}
		
	}


	public function listKeyTerms() {
		$string = '';
		$key_terms = $this -> showPostValue('key_terms');
		if ($key_terms) {
			foreach ($key_terms as $key => $value) {
				$string .= '<li class="multi-field">
						<input type="text" name="key_terms[]" placeholder="Keyword" value="'.$this -> showPostValue('key_terms', $key).'"/>
						<button type="button" class="remove-field">x</button>
						</li>';
			}
		}
		else {
			$string .= '<li class="multi-field">
						<input type="text" name="key_terms[]" placeholder="Keyword"/>
						<button type="button" class="remove-field">x</button>
						</li>';
		}

		return $string;
	}

	public function listAuthors() {
		$string = '';
		$authors = $this -> showPostValue('authors');
		if ($authors) {
			foreach ($authors as $key => $value) {
				$string .= '<li class="multi-field">
				<input type="text" name="authors[given][]" placeholder="Given Name(s)" value="'.$this -> showPostValue('authors', $key, 'given').'"/>
				<input type="text" name="authors[family][]" placeholder="Family Name" value="'.$this -> showPostValue('authors', $key, 'family').'"/>
				<button type="button" class="remove-field">x</button>
				</li>';
			}
		}
		else {
			$string .= '<li class="multi-field">
				<input type="text" name="authors[given][]" placeholder="Given Name(s)" />
				<input type="text" name="authors[family][]" placeholder="Family Name" />
				<button type="button" class="remove-field">x</button>
				</li>';
		}

		return $string;
	}

}
