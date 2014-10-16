<?php

class Publication {

	private $db;
	private $id;
	private $type_id;
	private $type;
	private $study_field_id;
	private $study_field;
	private $title;
	private $authors;
	private $authors_string;
	private $abstract;	
	private $year;
	private $month;
	private $key_terms;
	private $key_terms_string;

	private $metadata_complete;


	public function __construct(array $data, Database $db) {	// TODO input validation, exception

			$this -> db = $db;

			$this -> id = (int)$data['id'];
			$this -> type_id = (int)$data['type_id'];
			$this -> study_field_id = (int)$data['study_field_id'];
			$this -> title = $data['title'];
			$this -> year = (int)$data['year'];
			$this -> month = (int)$data['month'];
			$this -> metadata_complete = false;
	} 
	

	/**
	 * Loads additional metadata from the database. Additional metadata is the data
	 * which is only needed for the publication pages and thus should not be loaded everytime.
	 *
	 * @return	void
	 */
	private function getMissingData() {

		$data = $this -> db -> getPublications(array(
										'select' => array('abstract'),
										'id' => $this -> id));

		$this -> abstract = $data[0]['abstract'];
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
	 * Returns the type.
	 *
	 * @return string
	 */
	public function getType() {

		if (!isset($this -> type)) {

			$data = $this -> db -> getTypes($this -> type_id);

			$this -> type = $data[0]['name'];
		}

		return $this -> type;
	}


	/**
	 * Returns the field of study.
	 *
	 * @return string
	 */
	public function getStudyField() {

		if (!isset($this -> study_field)) {

			$data = $this -> db -> getStudyFields($this -> study_field_id);

			$this -> study_field = $data[0]['name'];
		}

		return $this -> study_field;
	}


	/**
	 * Returns the title of the publication.
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this -> title;
	}


	/**
	 * Returns an array with all authors as Author object sorted by their priority.
	 *
	 * @return array
	 */
	public function getAuthors() {

		if (!isset($this -> authors)) {
			
			$data = $this -> db -> getAuthors(array('publication_id' => $this -> id));

			foreach ($data as $value) {
				$author = new Author($value, $this -> db);
				$this -> authors[] = $author;
			}
		}

		return $this -> authors;
	}


	/**
	 * Returns a simple string with the names of all authors sorted by their priority. Authors
	 * names are separated by given parameter or default value ' and '.
	 *
	 * @param string	$separator	optional separator between the authors
	 *
	 * @return string
	 */
	public function getAuthorsString($separator = ' and ') {

		if (!isset($this -> authors_string)) {
			$temp = '';

			foreach ($this -> getAuthors() as $author) {
				$temp .= $author -> getName().$separator;
			}

			$this -> authors_string = substr($temp, 0, -(strlen($separator)));
		}

		return $this -> authors_string;
	}


	/**
	 * Returns the abstract.
	 *
	 * @return string
	 */
	public function getAbstract() {

		/* Checks if missing metadata was loaded already */
		if (!$this -> metadata_complete) {
			$this -> getMissingData();
		}
		return $this -> abstract;
	}


	/**
	 * Returns the year.
	 *
	 * @return int
	 */
	public function getYear() {
		return $this -> year;
	}


	/**
	 * Returns the month.
	 *
	 * @return int
	 */
	public function getMonth() {
		return $this -> month;
	}


	/**
	 * Returns an array with all key terms' ids and names.
	 *
	 * @return array
	 */
	public function getKeyTerms() {

		if (!isset($this -> key_terms)) {
			
			$this -> key_terms = array();

			$data = $this -> db -> getKeyTerms(array('publication_id' => $this -> id));

			$this -> key_terms = $data;
		}

		return $this -> key_terms;
	}


	/**
	 * Returns a simple string with the names of all key terms.
	 * Names are separated by given parameter or default value ', '.
	 *
	 * @param string	$separator	optional separator between the key terms
	 *
	 * @return string
	 */
	public function getKeyTermsString($separator = ', ') {

		if (!isset($this -> key_terms_string)) {
			
			$temp = '';

			foreach ($this -> getKeyTerms() as $key => $value) {
				$temp .= $value['name'].$separator;
			}

			$this -> key_terms_string = substr($temp, 0, -(strlen($separator)));
		}

		return $this -> key_terms_string;
	}	


}

?>
