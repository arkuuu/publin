<?php

require_once 'Publication.php';
require_once 'Author.php';
require_once 'KeyTerm.php';

abstract class Model {

	protected $db;
	protected $num;



	public function __construct(Database $db) {
		$this -> db = $db;
	}


	public function getNum() {
		return $this -> num;
	}


	public function createPublications($mode, array $filter = array()) {

		$publications = array();

		/* Gets the publications */
		$data = $this -> db -> fetchPublications($filter);
		$num = $this -> db -> getNumRows();

		foreach ($data as $key => $value) {
			$publication = new Publication($value);

			/* Gets the publications' authors */
			$authors = $this -> createAuthors(false, array('publication_id' => $publication -> getId()));
			$publication -> setAuthors($authors);

			if ($mode) {
				/* Gets the publications' key terms */
				$key_terms = $this -> createKeyTerms(array('publication_id' => $publication -> getId()));
				$publication -> setKeyTerms($key_terms);
			}

			$publications[] = $publication;
		}

		$this -> num = $num;

		return $publications;
	}


	public function createAuthors($mode, array $filter = array()) {

		$authors = array();

		/* Gets the authors */
		$data = $this -> db -> fetchAuthors($filter);
		$num = $this -> db -> getNumRows();

		foreach ($data as $key => $value) {
			$author = new Author($value);
		
			if ($mode) {
				/* Gets the authors' publications */
				$publications = $this -> createPublications(false, array('author_id' => $author -> getId()));
				$author -> setPublications($publications);
			}

			$authors[] = $author;
		}

		$this -> num = $num;

		return $authors;
	}


	public function createKeyTerms(array $filter = array()) {

		$key_terms = array();

		/* Gets the key terms */
		$data = $this -> db -> fetchKeyTerms($filter);
		$num = $this -> db -> getNumRows();

		foreach ($data as $key => $value) {
			$key_terms[] = new KeyTerm($value);
		}

		$this -> num = $num;

		return $key_terms;
	}

}
