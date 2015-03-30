<?php


namespace publin\src;

class StudyFieldController {

	private $db;
	private $model;


	public function __construct(Database $db) {

		$this->db = $db;
		$this->model = new StudyFieldModel($db);
	}


	public function run(Request $request) {

		$study_fields = $this->model->fetch(array('id' => $request->get('id')));
		$publications = $this->model->fetchPublications($request->get('id'));

		$view = new StudyFieldView($study_fields[0], $publications);

		return $view->display();
	}
}
