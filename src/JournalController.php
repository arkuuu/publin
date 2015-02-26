<?php


namespace publin\src;

class JournalController {

	private $db;
	private $model;


	public function __construct(Database $db) {

		$this->db = $db;
		$this->model = new JournalModel($db);
	}


	public function run($id) {

		if (isset($_POST['delete']) && $_POST['delete'] === 'yes') {
			$this->model->delete($id);
		}
		else if (isset($_GET['m']) && $_GET['m'] === 'edit' && !empty($_POST)) {

			$validator = new Validator();
			$validator->addRule('name', 'text', true, 'Name is required but invalid');

			if ($validator->validate($_POST)) {
				$input = $validator->getSanitizedResult();
				$success = $this->model->update($id, $input);
			}
			else {
				print_r($validator->getErrors());
			}
		}

		$journals = $this->model->fetch(true, array('id' => $id));

		if (isset($_GET['m']) && $_GET['m'] === 'edit') {
			$view = new JournalView($journals[0], true);
		}
		else {
			$view = new JournalView($journals[0]);
		}

		return $view->display();
	}
}
