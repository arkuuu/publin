<?php

require_once 'Model.php';

/**
 * Model for author page
 *
 * TODO: comment
 */
class AuthorModel extends Model {
	
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

		parent::__construct($db);

		$authors = $this -> createAuthors(true, array('id' => $id));
		// TODO: check if really only one was returned
		$this -> author = $authors[0];
	}


	/**
	 * Returns the Author object.
	 *
	 * @return	Author
	 */
	public function getAuthor() {
		return $this -> author;
	}

}
