<?php

require_once 'Publication.php';
require_once 'Author.php';
require_once 'KeyTerm.php';

class PublicationModel {
	
	private $db;
	private $id;
	private $publication;

	
	public function __construct($id, Database $db) {

		$this -> db = $db;
		$this -> id = $id;

		$this -> fetchPublication();
		$this -> fetchAuthors();
		$this -> fetchKeyTerms();
	}


	/**
	 * Returns the Publication object.
	 *
	 * @return	Publication
	 */
	public function getPublication() {
		return $this -> publication;
	}


	/**
	 * Gets the publication data from database and creates a new Publication object.
	 *
	 * @return	void
	 */
	private function fetchPublication() {

		$data = $this -> db -> fetchSinglePublication($this -> id);

		$this -> publication = new Publication($data[0], $this -> db);

	}


	/**
	 * Gets the publication's authors from database, creates Author objects and
	 * adds them to the Publication object.
	 *
	 * @return	void
	 */
	private function fetchAuthors() {

		$data = $this -> db -> fetchAuthorsOfPublication($this -> id);

		foreach ($data as $key => $value) {
			$authors[] = new Author($value);
		}

		$this -> publication -> setAuthors($authors);
	}


	/**
	 * Gets the publication's key terms from database, creates KeyTerm objects and
	 * adds them to the Publication object.
	 *
	 * @return	void
	 */
	private function fetchKeyTerms() {
		$key_terms = array();
		$data = $this -> db -> fetchKeyTermsOfPublication($this -> id);

		foreach ($data as $key => $value) {
			$key_terms[] = new KeyTerm($value);
		}

		$this -> publication -> setKeyTerms($key_terms);
	}

}
