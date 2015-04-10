<?php


namespace publin\src;

use publin\src\exceptions\NotFoundException;

class StudyFieldController {

	private $db;


	public function __construct(PDODatabase $db) {

		$this->db = $db;
	}


	public function run(Request $request) {

		$repo = new StudyFieldRepository($this->db);
		$study_field = $repo->select()->where('id', '=', $request->get('id'))->findSingle();
		if (!$study_field) {
			throw new NotFoundException('study field not found');
		}

		$repo = new PublicationRepository($this->db);
		$publications = $repo->select()->where('study_field_id', '=', $request->get('id'))->order('date_published', 'DESC')->find();

		$view = new StudyFieldView($study_field, $publications);

		return $view->display();
	}
}
