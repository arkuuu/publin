<?php

namespace publin\src;

use Exception;

class BrowseModel {

	private $old_db;
	private $db;
	private $browse_list = array();
	private $result = array();
	private $browse_type;
	private $is_result = false;


	public function __construct(Database $db) {

		$this->old_db = $db;
		$this->db = new PDODatabase();
	}


	public function handle($type, $id) {

		if (!empty($type)) {

			$this->browse_type = $type;

			switch ($type) {

				case 'recent':
					$this->is_result = true;
					$repo = new PublicationRepository($this->db);
					$this->result = $repo->select()->order('date_added', 'DESC')->limit(20)->find();
					break;

				case 'author':
					$repo = new AuthorRepository($this->db);
					$this->browse_list = $repo->select()->order('family', 'ASC')->find();
					break;

				case 'keyword':
					$repo = new KeywordRepository($this->db);
					$this->browse_list = $repo->select()->order('name', 'ASC')->find();
					break;

				case 'study_field':
					$repo = new StudyFieldRepository($this->db);
					$this->browse_list = $repo->select()->order('name', 'ASC')->find();
					break;

				case 'type':
					/*if ($id > 0) {
						$this->is_result = true;
						$repo = new PublicationRepository($this->db);
						$this->result = $repo->select()->where('type_id', '=', $id)->order('date_published', 'DESC')->find();
					}
					else {
						$model = new TypeModel($this->old_db);
						$this->browse_list = $model->fetch();
					}*/
					$repo = new TypeRepository($this->db);
					$this->browse_list = $repo->select()->order('name', 'ASC')->find();
					break;

				case 'year':
					if ($id > 0) {

						$this->is_result = true;
						$repo = new PublicationRepository($this->db);
						$this->result = $repo->select()->where('date_published', '=', $id, 'YEAR')->order('date_published', 'DESC')->find();
					}
					else {
						$this->browse_list = $this->fetchYears();
					}
					break;

				default:
					throw new Exception('unknown browse type "'.$type.'"');

					break;
			}
		}
	}


	private function fetchYears() {

		$query = 'SELECT DISTINCT YEAR(`date_published`) AS `year`
					FROM `publications`
					ORDER BY `year` DESC';

		$data = $this->old_db->getData($query);

		$years = array();

		foreach ($data as $key => $value) {
			$years[] = $value['year'];
		}

		return $years;
	}


	/**
	 * Returns the browse type.
	 *
	 * @return    string
	 */
	public function getBrowseType() {

		return $this->browse_type;
	}


	/**
	 * Returns the browse list.
	 *
	 * @return    array
	 */
	public function getBrowseList() {

		return $this->browse_list;
	}


	/**
	 * Returns the browse results.
	 *
	 * @return    array
	 */
	public function getBrowseResult() {

		return $this->result;
	}


	/**
	 * Returns true if there is a browse result.
	 *
	 * This is used to determine whether the result list or the browse list should be shown,
	 * so this returns true even if the browse result is empty.
	 *
	 * @return    boolean
	 */
	public function isBrowseResult() {

		return $this->is_result;
	}
}
