<?php


namespace publin\src;

class StudyFieldController {

	private $db;


	public function __construct(Database $db) {

		$this->db = new PDODatabase();
	}


	public function run(Request $request) {

		$repo = new StudyFieldRepository($this->db);
		$study_field = $repo->select()->where('id', '=', $request->get('id'))->findSingle();

		$repo = new PublicationRepository($this->db);
		$publications = $repo->select()->where('study_field_id', '=', $request->get('id'))->order('date_published', 'DESC')->find();

		$view = new StudyFieldView($study_field, $publications);

		return $view->display();
	}
}
