<?php


namespace publin\src;

class TypeController {

	private $db;
	private $model;


	public function __construct(Database $db) {

		$this->db = new PDODatabase();
		$this->model = new TypeModel($db);
	}


	public function run(Request $request) {

		$repo = new TypeRepository($this->db);
		$type = $repo->select()->where('id', '=', $request->get('id'))->findSingle();

		$repo = new PublicationRepository($this->db);
		$publications = $repo->select()->where('type_id', '=', $request->get('id'))->order('date_published', 'DESC')->find();
		$view = new TypeView($type, $publications);

		return $view->display();
	}
}
