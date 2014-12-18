<?php

require_once 'Object.php';

/**
 * Handles publication data.
 *
 * TODO: comment
 */
class Publication extends Object {

	/**
	 * @var	array
	 */
	private $authors;

	/**
	 * @var	array
	 */
	private $key_terms;



	/**
	 * Returns the type.
	 *
	 * @return string
	 */
	public function getType() {
		return $this -> data['type'];
	}


	/**
	 * Returns the field of study.
	 *
	 * @return string
	 */
	public function getStudyField() {
		return $this -> data['study_field'];
	}


	/**
	 * Returns the title of the publication.
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this -> data['title'];
	}


	/**
	 * Returns the abstract.
	 *
	 * @return string
	 */
	public function getAbstract() {
		return $this -> data['abstract'];
	}


	/**
	 * @return string
	 */
	public function getJournal() {
		return $this -> data['journal'];
	}


	/**
	 * @return string
	 */
	public function getVolume() {
		return $this -> data['volume'];
	}


	/**
	 * @return string
	 */
	public function getPages() {
		return $this -> data['pages'];
	}


	/**
	 * Returns the publish date.
	 *
	 * @param	string		$format		date format (optional)
	 *
	 * @return	string
	 */
	public function getDatePublished($format = 'm.Y') {
		return date($format, strtotime($this -> data['date_published']));
	}


	/**
	 * Returns the date the publication was added.
	 *
	 * @param	string		$format		date format (optional)
	 *
	 * @return	string
	 */
	public function getDateAdded($format = 'd.m.Y') {
		return date($format, strtotime($this -> data['date_added']));
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
