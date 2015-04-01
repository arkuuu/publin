<?php


namespace publin\src;

class StudyFieldRepository extends QueryBuilder {


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
	 * @return StudyField
	 */
	public function findSingle() {

		$result = parent::findSingle();

		return new StudyField($result);
	}
}
