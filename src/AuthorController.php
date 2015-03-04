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

		if ($request->get('m') === 'edit') {

			if ($request->post('delete')) {
				$this->delete($request);
				// TODO: header()
			}
			else {
				$this->edit($request);
			}

			$author = $this->model->fetch(true, array('id' => $request->get('id')));
			$view = new AuthorView($author[0], true);
		}
		else {
			$author = $this->model->fetch(true, array('id' => $request->get('id')));
			$view = new AuthorView($author[0]);
		}

		return $view->display();
	}


	public function delete(Request $request) {

		if ($request->post('delete') == 'yes' && $request->get('id')) {
			return $this->model->delete($request->get('id'));
		}
		else {
			return false;
		}
	}


	public function edit(Request $request) {

		if ($request->post()) {

			$validator = new Validator();
			$validator->addRule('given', 'text', true, 'Given name is required but invalid');
			$validator->addRule('family', 'text', true, 'Family name is required but invalid');
			$validator->addRule('website', 'url', false, 'Website URL is invalid');
			$validator->addRule('contact', 'text', false, 'Contact info is invalid');
			$validator->addRule('text', 'text', false, 'Text is invalid');

			if ($validator->validate($request->post())) {
				$input = $validator->getSanitizedResult();

				return $this->model->update($request->get('id'), $input);
			}
			else {
				print_r($validator->getErrors());

				return false;
			}
		}
		else {
			return false;
		}
	}
}
