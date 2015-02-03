<?php

require_once 'StudyFieldModel.php';
require_once 'TypeModel.php';
require_once 'JournalModel.php';
require_once 'PublisherModel.php';

class BrowseModel {
	
	private $db;
	private $browse_list = array();
	private $result = array();
	private $browse_type;
	private $is_result = false;
	private $num = -9999;


	public function __construct(Database $db) {
		$this -> db = $db;
	}

	public function getNum() {
		return $this -> num;
	}


	public function handle($type, $id) {

		if (!empty($type)) {

			$this -> browse_type = $type;

			switch ($type) {

				case 'recent':
					$this -> is_result = true;
					$model = new PublicationModel($this -> db);
					$this -> result = $model -> fetch(false, array('limit' => '0,10'));
					$this -> num = $model -> getNum();
					break;

				case 'author':
					$model = new AuthorModel($this -> db);
					$this -> browse_list = $model -> fetch(false);
					break;

				case 'key_term':
					if ($id > 0) {
						$this -> is_result = true;
						$model = new PublicationModel($this -> db);
						$this -> result = $model -> fetch(false, array('key_term_id' => $id));
						$this -> num = $model -> getNum();
					}
					else {
						$model = new KeyTermModel($this -> db);
						$this -> browse_list = $model -> fetch();
					}
					break;
				
				case 'study_field':				
					if ($id > 0) {
						$this -> is_result = true;
						$model = new PublicationModel($this -> db);
						$this -> result = $model -> fetch(false, array('study_field_id' => $id));
						$this -> num = $model -> getNum();
					}
					else {
						$model = new StudyFieldModel($this -> db);
						$this -> browse_list = $model -> fetch();
						$this -> num = $model -> getNum();
					}
					break;

				case 'type':
					if ($id > 0) {
						$this -> is_result = true;
						$model = new PublicationModel($this -> db);
						$this -> result = $model -> fetch(false, array('type_id' => $id));
						$this -> num = $model -> getNum();
					}
					else {
						$model = new TypeModel($this -> db);
						$this -> browse_list = $model -> fetch();
					}
					break;

				case 'journal':
					if ($id > 0) {
						$this -> is_result = true;
						$model = new PublicationModel($this -> db);
						$this -> result = $model -> fetch(false, array('journal_id' => $id));
						$this -> num = $model -> getNum();
					}
					else {
						$model = new JournalModel($this -> db);
						$this -> browse_list = $model -> fetch();
					}
					break;

				case 'publisher':
					if ($id > 0) {
						$this -> is_result = true;
						$model = new PublicationModel($this -> db);
						$this -> result = $model -> fetch(false, array('publisher_id' => $id));
						$this -> num = $model -> getNum();
					}
					else {
						$model = new PublisherModel($this -> db);
						$this -> browse_list = $model -> fetch();
					}
					break;

				case 'year':
					if ($id > 0) {

						$this -> is_result = true;
						$model = new PublicationModel($this -> db);
						$this -> result = $model -> fetch(false, array('year_published' => $id));
						$this -> num = $model -> getNum();

					}
					else {
						$this -> browse_list = $this -> fetchYears();
					}
					break;


				default:
					throw new Exception('unknown browse type "'.$type.'"');
					
					break;
			}
		}
	}

	/**
	 * Returns the browse type.
	 *
	 * @return	string
	 */
	public function getBrowseType() {
		return $this -> browse_type;
	}


	/**
	 * Returns the browse list.
	 *
	 * @return	array
	 */
	public function getBrowseList() {
		return $this -> browse_list;
	}


	/**
	 * Returns the browse results.
	 *
	 * @return	array
	 */
	public function getBrowseResult() {
		return $this -> result;
	}


	/**
	 * Returns true if there is a browse result.
	 *
	 * This is used to determine whether the result list or the browse list should be shown,
	 * so this returns true even if the browse result is empty.
	 *
	 * @return	boolean
	 */
	public function isBrowseResult() {
		return $this -> is_result;
	}


	private function fetchYears() {
		$data = $this -> db -> fetchYears();
		$this -> num = $this -> db -> getNumRows();

		$years = array();

		foreach ($data as $key => $value) {
			$years[] = $value['year'];
		}

		return $years;

	}

}
