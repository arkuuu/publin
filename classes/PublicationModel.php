<?php

require_once 'Model.php';

/**
 * Model for publication page
 *
 * TODO: comment
 */
class PublicationModel extends Model {
	
	/**
	 * @var	Publication
	 */
	private $publication;

	

	/**
	 * Constructs the model and gets all data needed for publication page.
	 *
	 * Fetches the publication, the publications authors and key terms and adds
	 * everything to the publication object.
	 *
	 * @param	int			$id		Id of the publication
	 * @param	Database	$db		Database connection
	 */
	public function __construct($id) {

		parent::__construct();

		$publications = $this -> createPublications(true, array('id' => $id));
		// TODO: check if really only one was returned
		$this -> publication = $publications[0];
	}


	/**
	 * Returns the Publication object.
	 *
	 * @return	Publication
	 */
	public function getPublication() {
		return $this -> publication;
	}

}
