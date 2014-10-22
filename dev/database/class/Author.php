<?php

class Author {

	private $db;
	private $id;
	private $user_id;
	private $last_name;
	private $first_name;
	private $academic_title;
	private $publications;
	private $website;
	private $contact;
	private $text;

	private $metadata_complete;


	public function __construct(array $data, Database $db) {

		$this -> db = $db;

		$this -> id = (int)$data['id'];
		$this -> user_id = (int)$data['user_id'];
		$this -> last_name = $data['last_name'];
		$this -> first_name = $data['first_name'];
		$this -> academic_title = $data['academic_title'];
		$this -> metadata_complete = false;
	}


	/**
	 * Loads additional metadata from the database.
	 * Additional metadata is the data which is only needed for the author pages
	 * and thus should not be loaded every time. This method must not be used directly.
	 *
	 * @return	void
	 */
	private function getMissingData() {

		/* Gets missing meta data */
		$data = $this -> db -> getAuthors(array(
										'select' => array('website', 'contact', 'text'),
										'id' => $this -> id));
		$this -> website = $data[0]['website'];
		$this -> contact = $data[0]['contact'];
		$this -> text = $data[0]['text'];

		/* Determines that all metadata is loaded */
		$this -> metadata_complete = true;
	}


    /**
     * Returns the id.
     *
     * @return int
     */
    public function getId() {
		return $this -> id;
	}


    /**
     * Returns the user id or 0, if there is no user id.
     *
     * @return int
     */
    public function getUserId() {
    	if (isset($this -> user_id)) {
    		return $this -> user_id;
    	}
    	else {
    		return 0;
    	}
	}


    /**
     * Returns the full name, consisting of academic title, first name and last name.
     *
     * @return string
     */
    public function getName() {
		return $this -> academic_title.' '.$this -> first_name.' '.$this -> last_name;
	}


    /**
     * Returns the last name.
     *
     * @return string
     */
    public function getLastName() {
		return $this -> last_name;
	}


    /**
     * Returns the first name.
     *
     * @return string
     */
    public function getFirstName() {
		return $this -> first_name;
	}


    /**
     * Returns the academic title.
     *
     * @return string
     */
    public function getAcademicTitle() {
		return $this -> academic_title;
	}


    /**
     * Returns an array with all Publication objects of this author.
     *
     * @return array
     */
    public function getPublications() {

		/* Checks if publication data was loaded already */
		if (!isset($this -> publications)) {
			$this -> publications = array();

			/* Gets publication data */
			$data = $this -> db -> getPublications(array('author_id' => $this -> id));

			/* Creates the Publication objects and puts them in array */
			foreach ($data as $value) {
				$publication = new Publication($value, $this -> db);
				$this -> publications[] = $publication;
			}
		}

		return $this -> publications;
	}


    /**
     * Returns the website.
     *
     * @return string
     */
    public function getWebsite() {

		/* Checks if missing metadata was loaded already */
		if (!$this -> metadata_complete) {
			$this -> getMissingData();
		}

		return $this -> website;
	}


    /**
     * Returns the contact info.
     *
     * @return string
     */
    public function getContact() {

		/* Checks if missing metadata was loaded already */
		if (!$this -> metadata_complete) {
			$this -> getMissingData();
		}

		return $this -> contact;
	}


    /**
     * Returns the author's text.
     *
     * @return string
     */
    public function getText() {

		/* Checks if missing metadata was loaded already */
		if (!$this -> metadata_complete) {
			$this -> getMissingData();
		}
		return $this -> text;
	}


    /**
     * Returns a search string for a specified service.
     *
     * @param string	$service	service for which the link should be returned
     *
     * @return string
     */
    public function getBibLink($service) {

		switch ($service) {
			case 'google':
				return 'http://scholar.google.com/scholar?q='
						.urlencode('"'.$this -> getFirstName().' '.$this -> getLastName().'"');
				break;

			case 'base':
				return 'http://www.base-search.net/Search/Results?type0[]=aut&lookfor0[]='
						.urlencode('"'.$this -> getFirstName().' '.$this -> getLastName().'"');
				break;

			default:
				return '';
				break;
		}
	}
}

?>