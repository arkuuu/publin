<?php


namespace publin\src;

use InvalidArgumentException;
use RuntimeException;

class FileModel {

	private $db;


	public function __construct(Database $db) {

		$this->db = $db;
	}


	/**
	 * @param $publication_id
	 *
	 * @return File[]
	 * @throws exceptions\SQLException
	 */
	public function fetch($publication_id) {

		$query = 'SELECT *
		FROM `files`
		WHERE `publication_id` = '.$publication_id.'
		ORDER BY `full_text` DESC, `restricted` ASC;';

		$data = $this->db->getData($query);
		$files = array();
		foreach ($data as $value) {
			$file = new File($value);
			$files[] = $file;
		}

		return $files;
	}


	public function store(File $file, $publication_id) {

		if (!is_numeric($publication_id)) {
			throw new InvalidArgumentException('publication id must be numeric');
		}
		$data = array('publication_id' => $publication_id,
					  'name'           => $file->getName(),
					  'title'          => $file->getTitle(),
					  'full_text'      => $file->isFullText(),
					  'restricted'     => $file->isRestricted());

		return $this->db->insertData('files', $data);
	}


	public function delete($id) {

		if (!is_numeric($id)) {
			throw new InvalidArgumentException('id must be numeric');
		}

		$rows = $this->db->deleteData('files', array('id' => $id));
		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error while deleting file: '.$this->db->error);
		}
	}


	/**
	 * @param $id
	 *
	 * @return File
	 * @throws exceptions\SQLException
	 */
	public function fetchById($id) {

		$query = 'SELECT *
		FROM `files`
		WHERE `id` = '.$id.'
		LIMIT 0,1;';

		$data = $this->db->getData($query);
		$file = new File($data[0]); // TODO: error when empty array

		return $file;
	}


	public function getValidator() {

		$validator = new Validator();

		$validator->addRule('name', 'text', false, 'File name is invalid');
		$validator->addRule('title', 'text', true, 'File title is required but invalid');
		$validator->addRule('full_text', 'boolean', false, 'Full text is required but invalid');
		$validator->addRule('restricted', 'boolean', false, 'Full text is required but invalid');

		return $validator;
	}
}
