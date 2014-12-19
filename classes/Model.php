<?php

require_once 'Publication.php';
require_once 'Author.php';
require_once 'KeyTerm.php';
require_once 'Journal.php';

abstract class Model {

	protected $db;
	protected $num;



	protected function __construct(Database $db) {
		$this -> db = $db;
	}


	public function getNum() {
		return $this -> num;
	}


	protected function createPublications($mode, array $filter = array()) {

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


	protected function createAuthors($mode, array $filter = array()) {

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


	protected function createKeyTerms(array $filter = array()) {

		$key_terms = array();

		$data = $this -> db -> fetchKeyTerms($filter);
		$num = $this -> db -> getNumRows();

		foreach ($data as $key => $value) {
			$key_terms[] = new KeyTerm($value);
		}

		$this -> num = $num;

		return $key_terms;
	}


	protected function createStudyFields(array $filter = array()) {

		$study_fields = array();

		$data = $this -> db -> fetchStudyFields($filter);
		$num = $this -> db -> getNumRows();

		foreach ($data as $key => $value) {
			$study_fields[] = new StudyField($value);
		}

		$this -> num = $num;

		return $study_fields;
	}


	protected function createTypes(array $filter = array()) {

		$types = array();

		$data = $this -> db -> fetchTypes($filter);
		$num = $this -> db -> getNumRows();

		foreach ($data as $key => $value) {
			$types[] = new Type($value);
		}

		$this -> num = $num;

		return $types;
	}


	protected function createJournals(array $filter = array()) {

		$journals = array();

		$data = $this -> db -> fetchJournals($filter);
		$num = $this -> db -> getNumRows();

		foreach ($data as $key => $value) {
			$journals[] = new Journal($value);
		}

		$this -> num = $num;

		return $journals;
	}

}
