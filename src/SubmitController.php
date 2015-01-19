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

			case 'preview':
				if (!empty($_SESSION['post'])) {
					$model -> createPublicationFromSubmit($_SESSION['post']);
					$errors = $model -> getErrors();

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

			case 'done':
				if (isset($_POST['accept']) && $_POST['accept'] === 'yes' && isset($_SESSION['publication'])) {

					$model -> storePublication($_SESSION['publication']);
					$view = new SubmitView($model, 'done');

					$_SESSION = array();
					session_destroy();
				}
				// TODO: what happens if not?
				// else if (!empty($_SESSION['publication'])) {
				// 	$view = new SubmitView($model, 'preview');
				// }
				// else {
				// 	$view = new SubmitView($model, 'form');			
				// }
				break;
			
			default:
				$view = new SubmitView($model, $mode);
				
				break;
		}

		
		return $view -> display();
	}
}
