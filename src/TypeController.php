<?php


namespace publin\src;

use publin\src\exceptions\NotFoundException;

class TypeController extends Controller {

	private $db;
	private $model;


	public function __construct(Database $db) {

		$this->db = $db;
		$this->model = new TypeModel($db);
	}


	public function run(Request $request) {

		$repo = new TypeRepository($this->db);
		$type = $repo->select()->where('id', '=', $request->get('id'))->findSingle();
		if (!$type) {
			throw new NotFoundException('type not found');
		}

		$repo = new PublicationRepository($this->db);
		$publications = $repo->select()->where('type_id', '=', $request->get('id'))->order('date_published', 'DESC')->find();

		$view = new TypeView($type, $publications);

		return $view->display();
	}
}
