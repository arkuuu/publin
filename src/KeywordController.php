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

		if ($request->post('action')) {
			$method = $request->post('action');
			if (method_exists($this, $method)) {
				$this->$method($request);
			}
		}

		$keywords = $this->model->fetch(true, array('id' => $request->get('id')));

		if ($request->get('m') === 'edit') {
			$view = new KeywordView($keywords[0], true);
		}
		else {
			$view = new KeywordView($keywords[0]);
		}

		return $view->display();
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool
	 */
	private function delete(Request $request) {

		if ($request->post('delete') == 'yes' && $request->get('id')) {
			return $this->model->delete($request->get('id'));
		}
		else {
			return false;
		}
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool|int
	 */
	private function edit(Request $request) {

		if ($request->post()) {

			$validator = $this->model->getValidator();

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
