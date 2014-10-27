<?php

class Publication {

	private $id;
	private $type;
	private $study_field;
	private $title;
	private $abstract;	
	private $year;
	private $month;
	private $authors;
	private $key_terms;


	public function __construct(array $data) {	// TODO input validation, exception

		foreach ($data as $key => $value) {
			$this -> $key = $value;
		}
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
		return $this -> type;
	}


	/**
	 * Returns the field of study.
	 *
	 * @return string
	 */
	public function getStudyField() {
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
	 * Returns an array with the authors of this publication.
	 * The array consists of Author objects.
	 *
	 * @return	array
	 */
	public function getAuthors() {
		return $this -> authors;
	}


	/**
	 * Returns an array with the key terms of this publication.
	 * The array consists of KeyTerm objects.
	 *
	 * @return	array
	 */
	public function getKeyTerms() {
		return $this -> key_terms;
	}


    /**
     * Adds the authors to the publication.
     *
     * @param	array	$authors	array with Author objects
     *
     * @return	void
     */
	public function setAuthors(array $authors) {
		// TODO: Input validation
		$this -> authors = $authors;
	}


    /**
     * Adds the key terms to the publication.
     *
     * @param	array	$key_terms	array with KeyTerm objects
     *
     * @return	void
     */
	public function setKeyTerms(array $key_terms) {
		// TODO: Input validation
		$this -> key_terms = $key_terms;
	}
	
}
