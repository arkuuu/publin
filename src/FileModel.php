<?php


namespace publin\src;

use InvalidArgumentException;
use publin\src\exceptions\NotFoundException;
use RuntimeException;

class FileModel {

	private $db;
	private $pdo;


	public function __construct(Database $db) {

		$this->db = $db;
		$this->pdo = new PDODatabase();
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
	 * @throws NotFoundException
	 */
	public function findById($id) {

		$query = 'SELECT *
					FROM `files`
					WHERE `id` = :id
					LIMIT 0,1;';

		$this->pdo->prepare($query);
		$this->pdo->bindValue(':id', $id, \PDO::PARAM_INT);
		$this->pdo->execute();
		$data = $this->pdo->fetchSingle();

		if ($data === false) {
			throw new NotFoundException('no file with that id');
		}

		return new File($data);
	}


	public function findByPublication($publication_id) {

		$query = 'SELECT *
					FROM `files`
					WHERE `publication_id` = :publication_id
					ORDER BY `full_text` DESC, `hidden` ASC, `restricted` ASC;';

		$this->pdo->prepare($query);
		$this->pdo->bindValue(':publication_id', $publication_id, \PDO::PARAM_INT);
		$this->pdo->execute();
		$result = $this->pdo->fetchAll();

		$files = array();
		foreach ($result as $row) {
			$files[] = new File($row);
		}

		return $files;
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
