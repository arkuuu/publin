<?php


namespace publin\src;

/**
 * Class StudyFieldRepository
 *
 * @package publin\src
 */
class StudyFieldRepository extends Repository {


    public function reset()
    {
        parent::reset();
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

        $result = parent::findSingle();

		if ($result) {
			return new StudyField($result);
		}
		else {
			return false;
		}
	}
}
