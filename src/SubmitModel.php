<?php

namespace publin\src;

/**
 * Class SubmitModel
 *
 * @package publin\src
 */
class SubmitModel extends Model {


	/**
	 * @param array $post
	 *
	 * @return array
	 */
	public function formatPost(array $post) {

		$result = array();

		foreach ($post as $key => $value) {

			if ($key == 'authors' && !empty($value)) {
				$value = $this->rewriteArray($value);
				$value = array_filter($value);
				if ($value) {
					$result[$key] = $value;
				}
			}
			if ($key == 'keywords' && !empty($value)) {
				$value = array_filter($value);
				if ($value) {
					$result[$key] = $value;
				}
			}
			else if (!empty($value)) {
				$result[$key] = $value;
			}
		}

		return $result;
	}


	/**
	 * @param array $input
	 *
	 * @return array
	 */
	private function rewriteArray(array $input) {

		$result = array();
		$given_fields = array_keys($input);

		foreach ($given_fields as $field) {
			foreach ($input[$field] as $key => $value) {
				if (!empty($value)) {
					$result[$key][$field] = $value;
				}
			}
		}

		return $result;
	}


	/**
	 * @return Type[]
	 */
	public function createTypes() {

		$repo = new TypeRepository($this->db);

		return $repo->order('name', 'ASC')->find();
	}


	/**
	 * @return StudyField[]
	 */
	public function createStudyFields() {

		$repo = new StudyFieldRepository($this->db);

		return $repo->order('name', 'ASC')->find();
	}
}
