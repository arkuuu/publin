<?php


namespace publin\src;

class PublicationRepository extends Repository {


	public function select() {

		$this->select = 'SELECT `types`.`name` AS `type`, `study_fields`.`name` AS `study_field`, self.*';
		$this->from = 'FROM `publications` self';

		$this->join('types', 'id', '=', 'type_id', 'LEFT');
		$this->join('study_fields', 'id', '=', 'study_field_id', 'LEFT');

		return $this;
	}


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
			$column = 'name';
			$table = 'keywords';
			// TODO
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

			if ($full === true) {
				$repo = new KeywordRepository($this->db);
				$publication->setKeywords($repo->select()->where('publication_id', '=', $publication->getId())->order('name', 'ASC')->find());

				$repo = new FileRepository($this->db);
				$publication->setFiles($repo->select()->where('publication_id', '=', $publication->getId())->find());
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

			if ($full === true) {
				$repo = new KeywordRepository($this->db);
				$publication->setKeywords($repo->select()->where('publication_id', '=', $publication->getId())->order('name', 'ASC')->find());

				$repo = new FileRepository($this->db);
				$publication->setFiles($repo->select()->where('publication_id', '=', $publication->getId())->find());
			}

			return $publication;
		}
		else {
			return false;
		}
	}
}
