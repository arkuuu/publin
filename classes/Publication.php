<?php

/**
 * Handles publication data.
 *
 * TODO: comment
 */
class Publication {

	/**
	 * @var	string
	 */
	private $id;

	/**
	 * @var	string
	 */
	private $type;

	/**
	 * @var	string
	 */
	private $study_field;

	/**
	 * @var	string
	 */
	private $title;

	/**
	 * @var	string
	 */
	private $abstract;

	/**
	 * @var	string
	 */	
	private $date_published;

	/**
	 * @var	string
	 */
	private $date_added;

	/**
	 * @var	array
	 */
	private $authors;

	/**
	 * @var	array
	 */
	private $key_terms;



	/**
	 * Constructs an publication object.
	 *
	 * @param	array	$data	publication data from database
	 */
	public function __construct(array $data) {

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
	 * Returns the publish date.
	 *
	 * @param	string		$format		date format (optional)
	 *
	 * @return	string
	 */
	public function getDatePublished($format = 'm.Y') {
		return date($format, strtotime($this -> date_published));
	}


	/**
	 * Returns the date the publication was added.
	 *
	 * @param	string		$format		date format (optional)
	 *
	 * @return	string
	 */
	public function getDateAdded($format = 'd.m.Y') {
		return date($format, strtotime($this -> date_added));
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
