<?php

require_once 'PublicationModel.php';
require_once 'Author.php';


class AuthorModel {


	private $db;
	private $num;

	
	public function __construct(Database $db) {
		$this -> db = $db;
	}


	public function getNum() {
		return $this -> num;
	}


	public function fetch($mode, array $filter = array()) {

		$authors = array();

		/* Gets the authors */
		$data = $this -> db -> fetchAuthors($filter);
		$this -> num = $this -> db -> getNumRows();

		foreach ($data as $key => $value) {
			$author = new Author($value);
		
			if ($mode) {
				/* Gets the authors' publications */
				$model = new PublicationModel($this -> db);
				$publications = $model -> fetch(false, array('author_id' => $author -> getId()));
				$author -> setPublications($publications);
			}

			$authors[] = $author;
		}

		return $authors;
	}


	public function validate(array $input) {

		$errors = array();
		// validation
		return $errors;
	}


	public function create(array $data) {

		// validation here?
		$author = new Author($data);
		return $author;
	}


	public function store(Author $author) {

		$data = $author -> getData();
		$id = $this -> db -> insertData('list_authors', $data);

		if (!empty($id)) {
			return $id;
		}
		else {
			throw new Exception('Error while inserting author to DB');
			
		}
	}

}
