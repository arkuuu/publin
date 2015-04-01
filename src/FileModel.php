<?php


namespace publin\src;

use InvalidArgumentException;
use RuntimeException;

class FileModel {

	private $old_db;


	public function __construct(Database $db) {

		$this->old_db = $db;
	}


	public function store(File $file, $publication_id) {

		if (!is_numeric($publication_id)) {
			throw new InvalidArgumentException('publication id must be numeric');
		}
		$data = array('publication_id' => $publication_id,
					  'name'           => $file->getName(),
					  'title'          => $file->getTitle(),
					  'full_text'      => $file->isFullText(),
					  'restricted' => $file->isRestricted(),
					  'hidden'     => $file->isHidden());

		return $this->old_db->insertData('files', $data);
	}


	public function delete($id) {

		if (!is_numeric($id)) {
			throw new InvalidArgumentException('id must be numeric');
		}

		$rows = $this->old_db->deleteData('files', array('id' => $id));
		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error while deleting file: '.$this->old_db->error);
		}
	}


	public function getValidator() {

		$validator = new Validator();

		$validator->addRule('name', 'text', false, 'File name is invalid');
		$validator->addRule('title', 'text', true, 'File title is required but invalid');
		$validator->addRule('full_text', 'boolean', false, 'Full text flag must be boolean');
		$validator->addRule('restricted', 'boolean', false, 'Restricted flag must be boolean');
		$validator->addRule('hidden', 'boolean', false, 'Hidden flag must be boolean');

		return $validator;
	}
}
