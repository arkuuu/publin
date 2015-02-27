<?php


namespace publin\src;

class KeywordController {

	private $db;
	private $model;


	public function __construct(Database $db) {

		$this->db = $db;
		$this->model = new KeywordModel($db);
	}


	public function run(Request $request) {

		if ($request->getPost('delete') === 'yes') {
			$this->model->delete($request->id);
		}
		else if ($request->mode === 'edit' && $request->getPost()) {

			$validator = new Validator();
			$validator->addRule('name', 'text', true, 'Name is required but invalid');

			if ($validator->validate($request->getPost())) {
				$input = $validator->getSanitizedResult();
				$success = $this->model->update($request->id, $input);
			}
			else {
				print_r($validator->getErrors());
			}
		}

		$keywords = $this->model->fetch(true, array('id' => $request->id));

		if ($request->mode === 'edit') {
			$view = new KeywordView($keywords[0], true);
		}
		else {
			$view = new KeywordView($keywords[0]);
		}

		return $view->display();
	}
}
