<?php


namespace publin\src;

/**
 * Class StudyFieldRepository
 *
 * @package publin\src
 */
class StudyFieldRepository extends Repository {


	/**
	 * @return $this
	 */
	public function select() {

		$this->select = 'SELECT self.*';
		$this->from = 'FROM `study_fields` self';

		return $this;
	}


	/**
	 * @return StudyField[]
	 */
	public function find() {

		$result = parent::find();
		$study_fields = array();

		foreach ($result as $row) {
			$study_fields[] = new StudyField($row);
		}

		return $study_fields;
	}


	/**
	 * @return StudyField|false
	 */
	public function findSingle() {

		if ($result = parent::findSingle()) {
			return new StudyField($result);
		}
		else {
			return false;
		}
	}
}
