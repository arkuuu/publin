<?php

require_once 'AuthorModel.php';
require_once 'KeyTermModel.php';
require_once 'Publication.php';


class PublicationModel {

	private $db;
	private $num;

	
	public function __construct(Database $db) {
		$this -> db = $db;
	}


	public function getNum() {
		return $this -> num;
	}


	public function fetch($mode, array $filter = array()) {

		$publications = array();

		/* Gets the publications */
		$data = $this -> db -> fetchPublications($filter);
		$this -> num = $this -> db -> getNumRows();

		foreach ($data as $key => $value) {
			$publication = new Publication($value);

			/* Gets the publications' authors */
			$model = new AuthorModel($this -> db);
			$authors = $model -> fetch(false, array('publication_id' => $publication -> getId()));
			$publication -> setAuthors($authors);

			if ($mode) {
				/* Gets the publications' key terms */
				$model = new KeyTermModel($this -> db);
				$key_terms = $model -> fetch(array('publication_id' => $publication -> getId()));
				$publication -> setKeyTerms($key_terms);
			}

			$publications[] = $publication;
		}

		return $publications;
	}


	public function validate(array $input) {

		$errors = array();

		// validation

		return $errors;
	}


	public function create(array $data, array $authors, array $key_terms) {

		// validation here?
		$publication = new Publication($data);
		$publication -> setAuthors($authors);
		$publication -> setKeyTerms($key_terms);

		return $publication;
	}


	public function store(Publication $publication) {

	}

}
