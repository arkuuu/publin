<?php


namespace publin\src;

/**
 * Class PublicationRepository
 *
 * @package publin\src
 */
class PublicationRepository extends Repository {

	/**
	 * @return $this
	 */
	public function select() {

		$this->select = 'SELECT `types`.`name` AS `type`, `study_fields`.`name` AS `study_field`, self.*';
		$this->from = 'FROM `publications` self';

		$this->join('types', 'id', '=', 'type_id', 'LEFT');
		$this->join('study_fields', 'id', '=', 'study_field_id', 'LEFT');

		return $this;
	}


	/**
	 * @param      $column
	 * @param      $comparator
	 * @param      $value
	 * @param null $function
	 *
	 * @return $this
	 */
	public function where($column, $comparator, $value, $function = null) {

		if ($column === 'author_id') {
			$table = 'publications_authors';
			$this->join($table, 'publication_id', '=', 'id');
		}
		else if ($column === 'keyword_id') {
			$table = 'publications_keywords';
			$this->join($table, 'publication_id', '=', 'id');
		}
		else if ($column === 'keyword_name') {
			$this->join('publications_keywords', 'publication_id', '=', 'id');
			$this->join .= ' JOIN `keywords` ON (`publications_keywords`.`keyword_id` = `keywords`.`id`)';
			$table = 'keywords';
			$column = 'name';
		}
		else {
			$table = 'self';
		}

		return parent::where($column, $comparator, $value, $function, $table);
	}


	/**
	 * @param bool $full
	 *
	 * @return Publication[]
	 */
	public function find($full = false) {

		$result = parent::find();
		$publications = array();

		foreach ($result as $row) {
			$publication = new Publication($row);

			$repo = new AuthorRepository($this->db);
			$publication->setAuthors($repo->select()->where('publication_id', '=', $publication->getId())->order('priority', 'ASC')->find());

			$repo = new FileRepository($this->db);
			$publication->setFiles($repo->select()->where('publication_id', '=', $publication->getId())->find());

			$repo = new UrlRepository($this->db);
			$publication->setUrls($repo->select()->where('publication_id', '=', $publication->getId())->find());

			if ($full === true) {
				$repo = new KeywordRepository($this->db);
				$publication->setKeywords($repo->select()->where('publication_id', '=', $publication->getId())->order('name', 'ASC')->find());
			}
			$publications[] = $publication;
		}

		return $publications;
	}


	/**
	 * @param bool $full
	 *
	 * @return Publication
	 */
	public function findSingle($full = false) {

		if ($result = parent::findSingle()) {
			$publication = new Publication($result);

			$repo = new AuthorRepository($this->db);
			$publication->setAuthors($repo->select()->where('publication_id', '=', $publication->getId())->order('priority', 'ASC')->find());

			$repo = new FileRepository($this->db);
			$publication->setFiles($repo->select()->where('publication_id', '=', $publication->getId())->find());

			$repo = new UrlRepository($this->db);
			$publication->setUrls($repo->select()->where('publication_id', '=', $publication->getId())->find());

			if ($full === true) {
				$repo = new KeywordRepository($this->db);
				$publication->setKeywords($repo->select()->where('publication_id', '=', $publication->getId())->order('name', 'ASC')->find());
			}

			return $publication;
		}
		else {
			return false;
		}
	}
}
