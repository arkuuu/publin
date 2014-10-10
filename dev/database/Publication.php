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
	private $date_added;	// TODO really needed outside the database (sorting)?
	private $key_terms;
	private $key_terms_string;


	public function __construct(array $data, Database $db) {	// TODO input validation, exception

			$this -> db = $db;

			$this -> id = (int)$data['id'];
			$this -> type_id = (int)$data['type_id'];
			$this -> study_field_id = (int)$data['study_field_id'];
			$this -> title = $data['title'];
			$this -> abstract = $data['abstract'];
			$this -> year = (int)$data['year'];
			$this -> month = (int)$data['month'];
			$this -> date_added = $data['date_added'];
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

			$data = $this -> db -> getData('	SELECT `name`
												FROM `list_types`
												WHERE `id` LIKE '.$this -> type_id);

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

			$data = $this -> db -> getData('	SELECT `name`
												FROM `list_study_fields`
												WHERE `id` LIKE '.$this -> study_field_id);

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
	 * Returns an array with all authors ids and names sorted by their priority.
	 *
	 * @return array
	 */
	public function getAuthors() {

		if (!isset($this -> authors)) {
			
			$this -> authors = array();

			$data = $this -> db -> getData('	SELECT a.`id`, a.`last_name`,
													a.`first_name`, a.`academic_title`
												FROM `list_authors` a
												JOIN `rel_publ_to_authors` r
													ON (r.`author_id` = a.`id`)
												WHERE r.`publication_id`
													LIKE '.$this -> id.'
												ORDER BY r.`priority` ASC');

			$this -> authors = $data;
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

			foreach ($this -> getAuthors() as $key => $value) {

				if (!empty($value['academic_title'])) {
					$temp .= $value['academic_title'].' ';
				}

				$temp .= $value['first_name'].' '.$value['last_name'].$separator;
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

			$data = $this -> db -> getData('	SELECT k.`id`, k.`name`
												FROM `list_key_terms` k
												JOIN `rel_publ_to_key_terms` r
													ON (r.`key_term_id` = k.`id`)
												WHERE r.`publication_id`
													LIKE '.$this -> id.'
												ORDER BY k.`name` ASC');

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