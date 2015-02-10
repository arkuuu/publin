<?php

namespace publin\src;

use InvalidArgumentException;
use RuntimeException;

class StudyFieldModel {


	private $db;
	private $num;


	public function __construct(Database $db) {

		$this->db = $db;
	}


	public function getNum() {

		return $this->num;
	}


	/**
	 * @param array $filter
	 *
	 * @return StudyField[]
	 */
	public function fetch(array $filter = array()) {

		$study_fields = array();

		$data = $this->db->fetchStudyFields($filter);
		$this->num = $this->db->getNumRows();

		foreach ($data as $key => $value) {
			$study_fields[] = new StudyField($value);
		}

		return $study_fields;
	}


	public function validate(array $input) {

		$errors = array();

		// validation
		return $errors;
	}


	public function create(array $data) {

		// validation here?
		$study_field = new StudyField($data);

		return $study_field;
	}


	public function store(StudyField $study_field) {

		$data = $study_field->getData();

		return $this->db->insertData('list_study_fields', $data);
	}


	public function update($id, array $data) {

	}


	public function delete($id) {

		//TODO: this only works when no foreign key constraints fail
		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}
		$where = array('id' => $id);
		$rows = $this->db->deleteData('list_study_fields', $where);

		// TODO: how to get rid of these?
		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error while deleting study field '.$id.': '.$this->db->error);
		}
	}

}
