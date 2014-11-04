<?php

require_once 'Author.php';
require_once 'Publication.php';

/**
 * Model for author page
 *
 * TODO: comment
 */
class AuthorModel {
	
	/**
	 * @var	Database
	 */
	private $db;

	/**
	 * @var	int
	 */
	private $id;

	/**
	 * @var	Author
	 */
	private $author;



	/**
	 * Constructs the model and gets all data needed for author page.
	 *
	 * Fetches the author, the author's publications and the authors of these
	 * publications and adds everything to the author object.
	 *
	 * @param	int			$id		Id of the author
	 * @param	Database	$db		Database connection
	 */
	public function __construct($id, Database $db) {

		$this -> db = $db;
		$this -> id = $id;

		$this -> fetchAuthor();
		$this -> fetchPublications();
		$this -> fetchPublicationsAuthors();
	}


	/**
	 * Returns the Author object.
	 *
	 * @return	Publication
	 */
	public function getAuthor() {
		return $this -> author;
	}


	/**
	 * Gets the author data from database and creates a new Author object.
	 *
	 * @return	void
	 */
	private function fetchAuthor() {

		$data = $this -> db -> fetchSingleAuthor($this -> id);

		$this -> author = new Author($data[0]);

	}


	/**
	 * Gets the author's publications from database, creates Publication objects and
	 * adds them to the Author object.
	 *
	 * @return	void
	 */
	private function fetchPublications() {

		$publications = array();

		$data = $this -> db -> fetchPublicationsOfAuthor($this -> id);

		foreach ($data as $key => $value) {
			$publications[] = new Publication($value);
		}

		$this -> author -> setPublications($publications);
	}


	/**
	 * Gets the author's publication's authors from database, creates Author objects and
	 * adds them to the Publication objects.
	 *
	 * @return	void
	 */
	private function fetchPublicationsAuthors() {

		foreach ($this -> author -> getPublications() as $publication) {
			$authors = array();
			$data = $this -> db -> fetchAuthorsOfPublication($publication -> getId());

			foreach ($data as $key => $value) {
				$authors[] = new Author($value);
			}

			$publication -> setAuthors($authors);
		}
	}

}
