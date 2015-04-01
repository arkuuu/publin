<?php


namespace publin\src;

class PublicationRepository extends QueryBuilder {


	public function select() {

		$this->select = 'SELECT `list_types`.`name` AS `type`, `list_study_fields`.`name` AS `study_field`, self.*';
		$this->from = 'FROM `list_publications` self';

		$this->join('list_types', 'id', '=', 'type_id', 'LEFT');
		$this->join('list_study_fields', 'id', '=', 'study_field_id', 'LEFT');

		return $this;
	}


	public function where($column, $comparator, $value, $function = null) {

		if ($column === 'author_id') {
			$table = 'rel_publ_to_authors';
			$this->join($table, 'publication_id', '=', 'id');
		}
		else if ($column === 'keyword_id') {
			$table = 'rel_publication_keywords';
			$this->join($table, 'publication_id', '=', 'id');
		}
		else {
			$table = 'self';
		}

		return parent::where($column, $comparator, $value, $function, $table);
	}


	public function go($full = false) {

		$result = parent::go();
		$publications = array();

		foreach ($result as $row) {
			$publication = new Publication($row);
			$repo = new AuthorRepository($this->db);
			$publication->setAuthors($repo->select()->where('publication_id', '=', $publication->getId())->go());

			if ($full === true) {
				$repo = new KeywordRepository($this->db);
				$publication->setKeywords($repo->select()->where('publication_id', '=', $publication->getId())->go());

				$repo = new FileRepository($this->db);
				$publication->setFiles($repo->select()->where('publication_id', '=', $publication->getId())->go());
			}
			$publications[] = $publication;
		}

		return $publications;
	}
}
