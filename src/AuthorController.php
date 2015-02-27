<?php


namespace publin\src;

class AuthorController {

	private $db;
	private $model;


	public function __construct(Database $db) {

		$this->db = $db;
		$this->model = new AuthorModel($db);
	}


	public function run(Request $request) {

		if ($request->mode === 'edit' && $request->getPost()) {

			$validator = new Validator();
			$validator->addRule('given', 'text', true, 'Given name is required but invalid');
			$validator->addRule('family', 'text', true, 'Family name is required but invalid');
			$validator->addRule('website', 'url', false, 'Website URL is invalid');
			$validator->addRule('contact', 'text', false, 'Contact info is invalid');
			$validator->addRule('text', 'text', false, 'Text is invalid');

			if ($validator->validate($request->getPost())) {
				$input = $validator->getSanitizedResult();
				$success = $this->model->update($request->id, $input);
			}
			else {
				print_r($validator->getErrors());
			}
		}

		$author = $this->model->fetch(true, array('id' => $request->id));

		if ($request->mode === 'edit') {
			$view = new AuthorView($author[0], true);
		}
		else {
			$view = new AuthorView($author[0]);
		}

		return $view->display();
	}
}
