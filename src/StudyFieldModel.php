<?php

namespace publin\src;

use InvalidArgumentException;
use RuntimeException;

class StudyFieldModel {


	private $old_db;
	private $db;


	public function __construct(Database $db) {

		$this->old_db = $db;
		$this->db = new PDODatabase();
	}


	/**
	 * @param array $filter
	 *
	 * @return StudyField[]
	 */
	public function fetch(array $filter = array()) {

		$study_fields = array();

		$data = $this->old_db->fetchStudyFields($filter);
		$this->num = $this->old_db->getNumRows();

		foreach ($data as $key => $value) {
			$study_fields[] = new StudyField($value);
		}

		return $study_fields;
	}


	public function fetchPublications($study_field_id) {

		$repo = new PublicationRepository($this->db);

		return $repo->select()->where('study_field_id', '=', $study_field_id)->order('date_published', 'DESC')->find();
	}


	public function store(StudyField $study_field) {

		$data = $study_field->getData();
		foreach ($data as $property => $value) {
			if (empty($value) || is_array($value)) {
				unset($data[$property]);
			}
		}

		return $this->old_db->insertData('list_study_fields', $data);
	}


	public function update($id, array $data) {
	}


	public function delete($id) {

		//TODO: this only works when no foreign key constraints fail
		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}
		$where = array('id' => $id);
		$rows = $this->old_db->deleteData('list_study_fields', $where);

		// TODO: how to get rid of these?
		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error while deleting study field '.$id.': '.$this->old_db->error);
		}
	}


	public function getValidator() {

		$validator = new Validator();
		$validator->addRule('name', 'text', true, 'Name is required but invalid');

		return $validator;
	}
}
