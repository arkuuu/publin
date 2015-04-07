<?php

namespace publin\src;

use Exception;
use InvalidArgumentException;
use RuntimeException;

class PublicationModel {

	private $old_db;
	private $db;


	public function __construct(Database $db) {

		$this->old_db = $db;
		$this->db = new PDODatabase();
	}


	public function store(Publication $publication) {

		$data = $publication->getData();
		foreach ($data as $property => $value) {
			if (empty($value) || is_array($value)) {
				unset($data[$property]);
			}
		}

		$authors = $publication->getAuthors();
		$keywords = $publication->getKeywords();

		/* Stores the authors */
		$author_ids = array();
		$model = new AuthorModel($this->db);
		foreach ($authors as $author) {
			$author_ids[] = $model->store($author);
		}
		/* Stores the key terms */
		$keyword_ids = array();
		$model = new KeywordModel($this->db);
		foreach ($keywords as $keyword) {
			$keyword_ids[] = $model->store($keyword);
		}
		/* Stores the type */
		if (isset($data['type'])) {
			$repo = new TypeRepository($this->db);
			$type = $repo->select()->where('name', '=', $data['type'])->findSingle();
			$data['type_id'] = $type->getId();
			unset($data['type']);
		}
		/* Stores the study field */
		/*if (isset($data['study_field'])) {
			$model = new StudyFieldModel($this->old_db);
			$study_field = new StudyField(array('name' => $data['study_field']));
			$data['study_field_id'] = $model->store($study_field);
			unset($data['study_field']);
		}*/
		/* Stores the publication */
		$publication_id = $this->old_db->insertData('publications', $data);

		if (!empty($publication_id)) {

			/* Stores the relation between the publication and its authors */
			if (!empty($author_ids)) {
				$priority = 1; // TODO: really start with 1 and go up?
				foreach ($author_ids as $author_id) {
					$this->addAuthor($publication_id, $author_id, $priority);
					$priority++;
				}
			}
			/* Stores the relation between the publication and its key terms */
			if (!empty($keyword_ids)) {
				foreach ($keyword_ids as $keyword_id) {
					$this->addKeyword($publication_id, $keyword_id);
				}
			}

			return $publication_id;
		}
		else {
			// TODO: streamline this with the other Model classes
			throw new Exception('Error while inserting publication to DB');
		}
	}


	public function addAuthor($publication_id, $author_id, $priority) {

		if (!is_numeric($publication_id) || !is_numeric($author_id) || !is_numeric($priority)) {
			throw new InvalidArgumentException('params should be numeric');
		}

		$data = array('publication_id' => $publication_id,
					  'author_id'      => $author_id,
					  'priority'       => $priority);

		return $this->old_db->insertData('publications_authors', $data);
	}


	public function addKeyword($publication_id, $keyword_id) {

		if (!is_numeric($publication_id) || !is_numeric($keyword_id)) {
			throw new InvalidArgumentException('params should be numeric');
		}

		$data = array('publication_id' => $publication_id,
					  'keyword_id'     => $keyword_id);

		return $this->old_db->insertData('publications_keywords', $data);
	}


	public function update($id, array $data) {

		/* Stores the type */
		if (isset($data['type'])) {
			$model = new TypeModel($this->db);
			$type = new Type(array('name' => $data['type']));
			$data['type_id'] = $model->store($type);
			unset($data['type']);
		}
		/* Stores the study field */
		if (isset($data['study_field'])) {
			$model = new StudyFieldModel($this->db);
			$study_field = new StudyField(array('name' => $data['study_field']));
			$data['study_field_id'] = $model->store($study_field);
			unset($data['study_field']);
		}

		return $this->old_db->updateData('publications', array('id' => $id), $data);
	}


	public function delete($id) {

		//TODO: this only works when no foreign key constraints fail
		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}

		$where = array('publication_id' => $id);
		$this->old_db->deleteData('publications_authors', $where);
		$this->old_db->deleteData('publications_keywords', $where);

		// TODO: delete files
		$where = array('id' => $id);
		$rows = $this->old_db->deleteData('publications', $where);

		// TODO: how to get rid of these?
		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error while deleting publication '.$id.': '.$this->old_db->error);
		}
	}


	public function removeAuthor($publication_id, $author_id) {

		if (!is_numeric($publication_id) || !is_numeric($author_id)) {
			throw new InvalidArgumentException('params should be numeric');
		}

		$where = array('publication_id' => $publication_id,
					   'author_id'      => $author_id);

		$rows = $this->old_db->deleteData('publications_authors', $where);

		// TODO: How to get rid of this and move it to DB?
		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error removing author '.$author_id.' from publication '.$publication_id.': '.$this->old_db->error);
		}
	}


	public function removeKeyword($publication_id, $keyword_id) {

		if (!is_numeric($publication_id) || !is_numeric($keyword_id)) {
			throw new InvalidArgumentException('params should be numeric');
		}

		$where = array('publication_id' => $publication_id,
					   'keyword_id'     => $keyword_id);

		$rows = $this->old_db->deleteData('publications_keywords', $where);

		// TODO: How to get rid of this and move it to DB?
		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error removing keyword '.$keyword_id.' from publication '.$publication_id.': '.$this->old_db->error);
		}
	}


	public function getValidator($type) {

		$validator = new Validator();

		$validator->addRule('title', 'text', true, 'Title is required but invalid');

		$validator->addRule('journal', 'text', false, 'Journal is required but invalid');
		$validator->addRule('volume', 'number', false, 'Volume is invalid');
		$validator->addRule('number', 'number', false, 'Number is invalid');
		$validator->addRule('booktitle', 'text', false, 'Booktitle is required but invalid');
		$validator->addRule('series', 'text', false, 'Series is invalid');
		$validator->addRule('edition', 'text', false, 'Edition is invalid');
		$validator->addRule('pages_from', 'number', false, 'First page is invalid');
		$validator->addRule('pages_to', 'number', false, 'Last page is invalid');
		$validator->addRule('note', 'text', false, 'Note is invalid');
		$validator->addRule('location', 'text', false, 'Location is invalid');

		$validator->addRule('date_published', 'date', true, 'Publication date is required but invalid');

		$validator->addRule('publisher', 'text', false, 'Publisher is invalid');
		$validator->addRule('institution', 'text', false, 'Institution is invalid');
		$validator->addRule('school', 'text', false, 'School is invalid');
		$validator->addRule('address', 'text', false, 'Address is invalid');
		$validator->addRule('howpublished', 'text', false, 'Howpublished is invalid');
		$validator->addRule('copyright', 'text', false, 'Copyright is invalid');
		$validator->addRule('doi', 'text', false, 'DOI is invalid'); // TODO: validate DOI
		$validator->addRule('isbn', 'text', false, 'ISBN invalid'); // TODO: validate ISBN

		$validator->addRule('study_field_id', 'number', true, 'Field of Study is required but invalid');
		$validator->addRule('type', 'text', true, 'Type is required but invalid');
		$validator->addRule('abstract', 'text', false, 'Abstract is invalid');

		/* Overwrite rules with required rules depending on type */
		switch ($type) {
			case 'article':
				$validator->addRule('journal', 'text', true, 'Journal is required but invalid');
				break;

			case 'book':
				$validator->addRule('publisher', 'text', true, 'Publisher is required but invalid');
				break;

			case 'incollection':
			case 'inproceedings':
				$validator->addRule('booktitle', 'text', true, 'Booktitle is required but invalid');
				break;

			case 'masterthesis':
			case 'phdthesis':
				$validator->addRule('institution', 'text', true, 'Institution is required but invalid');
				break;

			case 'misc':
				$validator->addRule('howpublished', 'text', true, 'How published is required but invalid');
				break;

			case 'techreport':
				$validator->addRule('institution', 'text', true, 'Institution is required but invalid');
				break;

			case 'unpublished':
				// Nothing here
				break;
		}

		return $validator;
	}
}
