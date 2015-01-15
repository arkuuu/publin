<?php

class SubmitController {

	public function __construct() {

	}

	public function run() {
		if (isset($_GET['m'])) {
			$mode = $_GET['m'];
		}
		else {
			$mode = 'start';
		}

		$model = new SubmitModel;

		if (!empty($_POST)) {
			$model -> createPublicationFromSubmit($_POST);
			$errors = $model -> getErrors();

			if (empty($errors)) {
				$view = new SubmitView($model, 'preview');
			}
			else {
				$view = new SubmitView($model, 'form');
			}
		}
		else {
			$view = new SubmitView($model, $mode);
		}
		return $view -> display();
	}
}
