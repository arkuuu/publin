<?php

namespace publin\src;

use Exception;
use InvalidArgumentException;

/**
 * Class PublicationModel
 *
 * @package publin\src
 */
class PublicationModel extends Model {


	/**
	 * @param Publication $publication
	 *
	 * @return string
	 * @throws exceptions\DBDuplicateEntryException
	 * @throws exceptions\DBForeignKeyException
	 */
	public function store(Publication $publication) {

		if ($publication->getTypeId()) {
			$type_id = $publication->getTypeId();
		}
		else {
			$repo = new TypeRepository($this->db);
			$type = $repo->select()->where('name', '=', $publication->getTypeName())->findSingle();
			$type_id = $type->getId();
		}

		if ($publication->getStudyFieldId()) {
			$study_field_id = $publication->getStudyFieldId();
		}
		else {
			$repo = new StudyFieldRepository($this->db);
			$type = $repo->select()->where('name', '=', $publication->getStudyField())->findSingle();
			$study_field_id = $type->getId();
		}

		$query = 'INSERT INTO
  `publications` (`type_id`, `study_field_id`, `title`, `date_published`, `booktitle`, `journal`, `volume`, `number`, `pages_from`, `pages_to`, `series`, `edition`, `note`, `location`, `publisher`, `institution`, `school`, `address`, `isbn`, `doi`, `howpublished`, `abstract`, `copyright`, `foreign`)
VALUES
  (:type_id, :study_field_id, :title, :date_published, :booktitle, :journal, :volume, :number, :pages_from, :pages_to,
   :series, :edition, :note, :location, :publisher, :institution, :school, :address, :isbn, :doi, :howpublished,
   :abstract, :copyright, :foreign);';
		$this->db->prepare($query);
		$this->db->bindValue(':type_id', $type_id);
		$this->db->bindValue(':study_field_id', $study_field_id);
		$this->db->bindValue(':title', $publication->getTitle());
		$this->db->bindValue(':date_published', $publication->getDatePublished());
		$this->db->bindValue(':booktitle', $publication->getBooktitle());
		$this->db->bindValue(':journal', $publication->getJournal());
		$this->db->bindValue(':volume', $publication->getVolume());
		$this->db->bindValue(':number', $publication->getNumber());
		$this->db->bindValue(':pages_from', $publication->getFirstPage());
		$this->db->bindValue(':pages_to', $publication->getLastPage());
		$this->db->bindValue(':series', $publication->getSeries());
		$this->db->bindValue(':edition', $publication->getEdition());
		$this->db->bindValue(':note', $publication->getNote());
		$this->db->bindValue(':location', $publication->getLocation());
		$this->db->bindValue(':publisher', $publication->getPublisher());
		$this->db->bindValue(':institution', $publication->getInstitution());
		$this->db->bindValue(':school', $publication->getSchool());
		$this->db->bindValue(':address', $publication->getAddress());
		$this->db->bindValue(':isbn', $publication->getIsbn());
		$this->db->bindValue(':doi', $publication->getDoi());
		$this->db->bindValue(':howpublished', $publication->getHowpublished());
		$this->db->bindValue(':abstract', $publication->getAbstract());
		$this->db->bindValue(':copyright', $publication->getCopyright());
		$this->db->bindValue(':foreign', $publication->getForeign());		
		$this->db->execute();

		return $this->db->lastInsertId();
	}


	/**
	 * @param $publication_id
	 * @param $author_id
	 * @param $priority
	 *
	 * @return string
	 * @throws exceptions\DBDuplicateEntryException
	 * @throws exceptions\DBForeignKeyException
	 */
	public function addAuthor($publication_id, $author_id, $priority) {

		if (!is_numeric($publication_id) || !is_numeric($author_id) || !is_numeric($priority)) {
			throw new InvalidArgumentException('params should be numeric');
		}

		$query = 'INSERT INTO `publications_authors` (`publication_id`, `author_id`, `priority`) VALUES (:publication_id, :author_id, :priority);';
		$this->db->prepare($query);
		$this->db->bindValue(':publication_id', (int)$publication_id);
		$this->db->bindValue(':author_id', (int)$author_id);
		$this->db->bindValue(':priority', (int)$priority);
		$this->db->execute();

		return $this->db->lastInsertId();
	}


	/**
	 * @param $publication_id
	 * @param $keyword_id
	 *
	 * @return string
	 * @throws exceptions\DBDuplicateEntryException
	 * @throws exceptions\DBForeignKeyException
	 */
	public function addKeyword($publication_id, $keyword_id) {

		if (!is_numeric($publication_id) || !is_numeric($keyword_id)) {
			throw new InvalidArgumentException('params should be numeric');
		}

		$query = 'INSERT INTO `publications_keywords` (`publication_id`, `keyword_id`) VALUES (:publication_id, :keyword_id);';
		$this->db->prepare($query);
		$this->db->bindValue(':publication_id', (int)$publication_id);
		$this->db->bindValue(':keyword_id', (int)$keyword_id);
		$this->db->execute();

		return $this->db->lastInsertId();
	}


	/**
	 * @param       $id
	 * @param array $data
	 *
	 * @return int
	 */
	public function update($id, array $data) {

		/* Fetches the type */
		if (isset($data['type'])) {
			$repo = new TypeRepository($this->db);
			$type = $repo->select()->where('name', '=', $data['type'])->findSingle();
			$data['type_id'] = $type->getId();
			unset($data['type']);
		}
		/* Fetches the study field */
		if (isset($data['study_field'])) {
			$repo = new StudyFieldRepository($this->db);
			$type = $repo->select()->where('name', '=', $data['study_field'])->findSingle();
			$data['study_field_id'] = $type->getId();
			unset($data['study_field']);
		}
		/* If checkbox is unchecked, we do not get a value */
		if (!isset($data['foreign']) ) {
			$data['foreign'] = 0;
		}

		$old_db = new OldDatabase();

		return $old_db->updateData('publications', array('id' => $id), $data);
	}


	/**
	 * @param $id
	 *
	 * @return int
	 * @throws Exception
	 */
	public function delete($id) {

		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}

		$this->db->beginTransaction();

		try {
			$query = 'DELETE FROM `publications_authors` WHERE `publication_id` = :id;';
			$this->db->prepare($query);
			$this->db->bindValue(':id', (int)$id);
			$this->db->execute();

			$query = 'DELETE FROM `urls` WHERE `publication_id` = :id;';
			$this->db->prepare($query);
			$this->db->bindValue(':id', (int)$id);
			$this->db->execute();

			$query = 'DELETE FROM `publications_keywords` WHERE `publication_id` = :id;';
			$this->db->prepare($query);
			$this->db->bindValue(':id', (int)$id);
			$this->db->execute();

			$query = 'DELETE FROM `publications` WHERE `id` = :id;';
			$this->db->prepare($query);
			$this->db->bindValue(':id', (int)$id);
			$this->db->execute();
			$row_count = $this->db->rowCount();

			$this->db->commitTransaction();

			return $row_count;
		}
		catch (Exception $e) {
			$this->db->cancelTransaction();
			throw $e;
		}
	}


	/**
	 * @param $publication_id
	 * @param $author_id
	 *
	 * @return int
	 * @throws exceptions\DBDuplicateEntryException
	 * @throws exceptions\DBForeignKeyException
	 */
	public function removeAuthor($publication_id, $author_id) {

		if (!is_numeric($publication_id) || !is_numeric($author_id)) {
			throw new InvalidArgumentException('params should be numeric');
		}

		$query = 'DELETE FROM `publications_authors` WHERE `publication_id` = :publication_id AND `author_id` = :author_id;';
		$this->db->prepare($query);
		$this->db->bindValue(':publication_id', (int)$publication_id);
		$this->db->bindValue(':author_id', (int)$author_id);
		$this->db->execute();

		return $this->db->rowCount();
	}


	/**
	 * @param $publication_id
	 * @param $keyword_id
	 *
	 * @return int
	 * @throws exceptions\DBDuplicateEntryException
	 * @throws exceptions\DBForeignKeyException
	 */
	public function removeKeyword($publication_id, $keyword_id) {

		if (!is_numeric($publication_id) || !is_numeric($keyword_id)) {
			throw new InvalidArgumentException('params should be numeric');
		}

		$query = 'DELETE FROM `publications_keywords` WHERE `publication_id` = :publication_id AND `keyword_id` = :keyword_id;';
		$this->db->prepare($query);
		$this->db->bindValue(':publication_id', (int)$publication_id);
		$this->db->bindValue(':keyword_id', (int)$keyword_id);
		$this->db->execute();

		return $this->db->rowCount();
	}


	/**
	 * @param $type
	 *
	 * @return Validator
	 */
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

		$validator->addRule('date_published', 'date', true, 'Publication date is required but invalid. It must in the format YYYY-MM-DD');

		$validator->addRule('publisher', 'text', false, 'Publisher is invalid');
		$validator->addRule('institution', 'text', false, 'Institution is invalid');
		$validator->addRule('school', 'text', false, 'School is invalid');
		$validator->addRule('address', 'text', false, 'Address is invalid');
		$validator->addRule('howpublished', 'text', false, 'Howpublished is invalid');
		$validator->addRule('copyright', 'text', false, 'Copyright is invalid');
		$validator->addRule('doi', 'doi', false, 'DOI is invalid');
		$validator->addRule('isbn', 'text', false, 'ISBN invalid'); // TODO: validate ISBN

		$validator->addRule('study_field', 'text', true, 'Field of Study is required but invalid');
		$validator->addRule('type', 'text', true, 'Type is required but invalid');
		$validator->addRule('abstract', 'text', false, 'Abstract is invalid');
		
		$validator->addRule('foreign', 'number', false, 'Foreign is valid');

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
			case 'inbook':
				$validator->addRule('booktitle', 'text', true, 'Booktitle is required but invalid');
				break;

			case 'mastersthesis':
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
