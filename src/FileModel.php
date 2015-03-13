<?php


namespace publin\src;

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
		ORDER BY `full_text` DESC;';

		$data = $this->db->getData($query);
		$files = array();
		foreach ($data as $value) {
			$file = new File($value);
			$files[] = $file;
		}

		return $files;
	}


	/**
	 * @param $id
	 *
	 * @return File
	 * @throws exceptions\SQLException
	 */
	public function fetchById($id) {

		$query = 'SELECT `id`, `name`, `restricted`
		FROM `files`
		WHERE `id` = '.$id.'
		LIMIT 0,1;';

		$data = $this->db->getData($query);
		$file = new File($data[0]);

		return $file;
	}


	public function removeFile($name) {

		$where = array('name' => $name);
		$rows = $this->db->deleteData('files', $where);

		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error while deleting file: '.$this->db->error);
		}
	}


	public function addFile($publication_id, $file_name, $title, $restricted, $full_text) {

		$data = array('publication_id' => $publication_id,
					  'name'      => $file_name,
					  'title'     => $title,
					  'full_text' => $full_text,
					  'restricted'     => $restricted);

		return $this->db->insertData('files', $data);
	}
}
