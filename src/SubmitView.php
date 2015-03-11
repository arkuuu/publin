<?php

namespace publin\src;

class SubmitView extends View {

	private $model;
	private $submit_mode;


	public function __construct(SubmitModel $model, $submit_mode) {

		if (in_array($submit_mode, array('start', 'import', 'form'))) {
			$this->submit_mode = $submit_mode;
		}
		else {
			$this->submit_mode = 'start';
		}

		parent::__construct('submit');
		$this->model = $model;
	}


	public function isForm() {

		if ($this->submit_mode == 'form') {
			return true;
		}
		else {
			return false;
		}
	}


	public function isImport() {

		if ($this->submit_mode == 'import') {
			return true;
		}
		else {
			return false;
		}
	}


	public function showPageTitle() {

		return 'Submit publication';
	}


	public function listTypeOptions() {

		$types = $this->model->createTypes();
		$selected_name = $this->show('type');

		if ($selected_name) {
			$string = '<option value disabled>Select...</option>';
		}
		else {
			$string = '<option value selected disabled>Select...</option>';
		}

		/* @var $type Type */
		foreach ($types as $type) {
			if ($type->getName() == $selected_name) {
				$selected = 'selected';
			}
			else {
				$selected = '';
			}

			$string .= '<option value="'.$this->html($type->getName()).'" '.$selected.'>'
				.$this->html($type->getName()).'</option>';
		}

		return $string;
	}


	public function show($field, $field2 = null, $field3 = null) {

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


	public function listStudyFieldOptions() {

		$study_fields = $this->model->createStudyFields();
		$selected_name = $this->show('study_field');

		if ($selected_name) {
			$string = '<option value disabled>Select...</option>';
		}
		else {
			$string = '<option value selected disabled>Select...</option>';
		}

		/* @var $study_field StudyField */
		foreach ($study_fields as $study_field) {
			if ($study_field->getName() == $selected_name) {
				$selected = 'selected';
			}
			else {
				$selected = '';
			}

			$string .= '<option value="'.$this->html($study_field->getName()).'" '.$selected.'>'
				.$this->html($study_field->getName()).'</option>';
		}

		return $string;
	}


	public function listErrors() {

		$errors = array();
		// TODO

		if (!empty($errors)) {
			$string = '';
			foreach ($errors as $error) {
				$string .= '<li>'.$this->html($error).'</li>';
			}

			return $string;
		}
		else {
			return false;
		}
	}


	public function listKeywords() {

		$string = '';
		$keywords = $this->show('keywords');
		if ($keywords) {
			/* @var $keywords Keyword[] */
			foreach ($keywords as $key => $value) {
				$string .= '<li class="multi-field">
						<input type="text" name="keywords[]" placeholder="Keyword" value="'.$this->show('keywords', $key).'"/>
						<button type="button" class="remove-field">x</button>
						</li>';
			}
		}
		else {
			$string .= '<li class="multi-field">
						<input type="text" name="keywords[]" placeholder="Keyword"/>
						<button type="button" class="remove-field">x</button>
						</li>';
		}

		return $string;
	}


	public function listAuthors() {

		$string = '';
		$authors = $this->show('authors');
		if ($authors) {
			/* @var $authors Author[] */
			foreach ($authors as $key => $value) {
				$string .= '<li class="multi-field">
				<input type="text" name="authors[given][]" placeholder="Given Name(s)" value="'.$this->show('authors', $key, 'given').'"/>
				<input type="text" name="authors[family][]" placeholder="Family Name" value="'.$this->show('authors', $key, 'family').'"/>
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
