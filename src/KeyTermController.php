<?php


namespace publin\src;

class KeyTermController {

	private $db;
	private $model;


	public function __construct(Database $db) {

		$this->db = $db;
		$this->model = new KeyTermModel($db);
	}


	public function run($id) {

		if (isset($_POST['delete']) && $_POST['delete'] === 'yes') {
			$this->model->delete($id);
		}
		else if (isset($_GET['m']) && $_GET['m'] === 'edit' && !empty($_POST)) {

			$validator = new Validator();
			$validator->addRule('name', 'text', true, 'Given name is required but invalid');

			if ($validator->validate($_POST)) {
				$input = $validator->getSanitizedResult();
				//var_dump($input);
				$sucess = $this->model->update($id, $input);
			}
			else {
				print_r($validator->getErrors());
			}
		}

		$keywords = $this->model->fetch(true, array('id' => $id));

		if (isset($_GET['m']) && $_GET['m'] === 'edit') {
			$view = new KeyTermView($keywords[0], true);
		}
		else {
			$view = new KeyTermView($keywords[0]);
		}

		return $view->display();
	}
}
