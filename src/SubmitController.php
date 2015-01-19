<?php

class SubmitController {

	public function __construct() {
		session_start();

	}

	public function run() {
		if (isset($_GET['m'])) {
			$mode = $_GET['m'];
		}
		else {
			$mode = 'start';
		}

		if (isset($_POST)) {
			$_SESSION['post'] = $_POST;
		}


		$model = new SubmitModel;

		switch ($mode) {
			case 'start':
				$view = new SubmitView($model, 'start');
				break;

			case 'form':
				$view = new SubmitView($model, 'form');
				break;

			case 'preview':
				if (!empty($_SESSION['post'])) {
					$model -> createPublicationFromSubmit($_SESSION['post']);
					$errors = $model -> getErrors();
					// $matches = $model -> getMatches();

					if (empty($errors)) {
						$_SESSION['publication'] = $model -> getPublication();
						$view = new SubmitView($model, 'preview');
					}
					else {
						$view = new SubmitView($model, 'form');
					}
				}
				else {
					$view = new SubmitView($model, 'start');
				}
				break;

			case 'submit':
				if ($_POST['accept'] === 'yes' && isset($_SESSION['publication'])) {

					$model -> storePublication($_SESSION['publication']);
					$view = new SubmitView($model, 'done');

					$_SESSION = array();
					session_destroy();


				}
				break;
			
			default:
				throw new Exception('Unknown mode for submit');
				
				break;
		}

		
		return $view -> display();
	}
}
