<?php

require_once 'SubmitModel.php';

class SubmitController {

	public function __construct() {
		// not if there is a session already
		// session_destroy();
		session_start();
		// $_SESSION = array();

	}

	public function run() {
		if (isset($_GET['m'])) {
			$mode = $_GET['m'];
		}
		else {
			$mode = 'start';
		}

		$model = new SubmitModel;

		if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST)) {
			if (!empty($_POST['bibtex'])) {
				$_SESSION['input'] = $model -> formatImport($_POST['bibtex'], 'Bibtex');
			}
			else {
				$_SESSION['input'] = $model -> formatPost($_POST);					
			}
		}

		switch ($mode) {

			case 'form':
				$view = new SubmitView($model, 'form');
				break;

			case 'preview':
				if (isset($_SESSION['input'])) {
					print_r($_SESSION['input']);
					$publication = $model -> createNewPublication($_SESSION['input']);
					$errors = $model -> getErrors();
					print_r($errors);

					if (!$errors && $publication) {
						$_SESSION['publication'] = $publication;
						$view = new SubmitView($model, 'preview');
					}
					else {
						$view = new SubmitView($model, 'form');
					}
				}
				else {
					$view = new SubmitView($model, 'form');	
				}
				break;

			case 'done':
				if (isset($_POST['accept']) && $_POST['accept'] === 'yes' && isset($_SESSION['publication'])) {

					$model -> storePublication($_SESSION['publication']);
					$view = new SubmitView($model, 'done');

					unset($_SESSION['publication']);
					unset($_SESSION['post']);
				}
				// TODO: what happens if not?
				else if (!empty($_SESSION['publication'])) {
					$view = new SubmitView($model, 'preview');
				}
				else {
					$view = new SubmitView($model, 'form');			
				}
				break;
			
			default:
				$view = new SubmitView($model, $mode);
				
				break;
		}

		
		return $view -> display();
	}
}
