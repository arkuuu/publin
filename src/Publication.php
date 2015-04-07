<?php

namespace publin\src;

use InvalidArgumentException;

/**
 * Handles publication data.
 *
 * TODO: comment
 */
class Publication {

	private $id;
	private $date_added;
	private $type_id;
	private $type;
	private $study_field;
	private $study_field_id;
	private $title;
	private $authors;
	private $journal;
	private $volume;
	private $number;
	private $booktitle;
	private $series;
	private $edition;
	private $pages_from;
	private $pages_to;
	private $note;
	private $location;
	private $date_published;
	private $publisher;
	private $institution;
	private $school;
	private $address;
	private $howpublished;
	private $copyright;
	private $doi;
	private $isbn;
	private $abstract;
	private $keywords;
	private $files;


	public function __construct(array $data, array $authors = array(), array $keywords = array(), array $files = array()) {

		foreach ($data as $property => $value) {
			if (property_exists($this, $property)) {
				$this->$property = $value;
			}
		}

		$this->setAuthors($authors);
		$this->setKeywords($keywords);
		$this->setFiles($files);
	}


	/**
	 * @return mixed
	 */
	public function getId() {

		return $this->id;
	}


	/**
	 * @return array
	 */
	public function getData() {

		return get_object_vars($this);
	}


	/**
	 * @return array|bool
	 */
	public function getTypeId() {

		return $this->type_id;
	}


	/**
	 * Returns the field of study.
	 *
	 * @return string
	 */
	public function getStudyField() {

		return $this->study_field;
	}


	/**
	 * @return mixed
	 */
	public function getStudyFieldId() {

		return $this->study_field_id;
	}


	/**
	 * @return string
	 */
	public function getIsbn() {

		return $this->isbn;
	}


	/**
	 * @return string
	 */
	public function getDoi() {

		return $this->doi;
	}


	/**
	 * @return array|bool
	 */
	public function getCopyright() {

		return $this->copyright;
	}


	/**
	 * @param $divider
	 *
	 * @return string
	 */
	public function getPages($divider) {

		if ($this->pages_from && $this->pages_to) {
			return $this->pages_from.$divider.$this->pages_to;
		}
		else {
			return false;
		}
	}


	/**
	 * @return mixed
	 */
	public function getFirstPage() {

		return $this->pages_from;
	}


	/**
	 * @return mixed
	 */
	public function getLastPage() {

		return $this->pages_to;
	}


	/**
	 * Returns the date the publication was added.
	 *
	 * @param    string $format date format (optional)
	 *
	 * @return    string
	 */
	public function getDateAdded($format) {

		if ($this->date_added) {
			return date($format, strtotime($this->date_added));
		}
		else {
			return false;
		}
	}


	/**
	 * Returns an array with the authors of this publication.
	 * The array consists of Author objects.
	 *
	 * @return    Author[]
	 */
	public function getAuthors() {

		return $this->authors;
	}


	/**
	 * @param Author[] $authors
	 *
	 * @return bool
	 */
	public function setAuthors(array $authors) {

		$this->authors = array();

		foreach ($authors as $author) {

			if ($author instanceof Author) {
				$this->authors[] = $author;
			}
			else {
				throw new InvalidArgumentException('must be array with Author objects');
			}
		}

		return true;
	}


	/**
	 * @return    File[]
	 */
	public function getFiles() {

		return $this->files;
	}


	/**
	 * @param File[] $files
	 *
	 * @return bool
	 *
	 */
	public function setFiles(array $files) {

		$this->files = array();

		foreach ($files as $file) {

			if ($file instanceof File) {
				$this->files[] = $file;
			}
			else {
				throw new InvalidArgumentException('must be array with File objects');
			}
		}

		return true;
	}


	/**
	 * Returns an array with the key terms of this publication.
	 * The array consists of KeyTerm objects.
	 *
	 * @return    Keyword[]
	 */
	public function getKeywords() {

		return $this->keywords;
	}


	/**
	 * @param Keyword[] $keywords
	 *
	 * @return bool
	 */
	public function setKeywords(array $keywords) {

		$this->keywords = array();

		foreach ($keywords as $keyword) {

			if ($keyword instanceof Keyword) {
				$this->keywords[] = $keyword;
			}
			else {
				throw new InvalidArgumentException('must be array with KeyTerm objects');
			}
		}

		return true;
	}


	/**
	 * Returns the type.
	 *
	 * @return string
	 */
	public function getTypeName() {

		return $this->type;
	}


	/**
	 * Returns the title of the publication.
	 *
	 * @return string
	 */
	public function getTitle() {

		return $this->title;
	}


	/**
	 * @return string
	 */
	public function getJournal() {

		return $this->journal;
	}


	/**
	 * @return string
	 */
	public function getBooktitle() {

		return $this->booktitle;
	}


	/**
	 * @return string
	 */
	public function getPublisher() {

		return $this->publisher;
	}


	/**
	 * @return string
	 */
	public function getEdition() {

		return $this->edition;
	}


	/**
	 * @return string
	 */
	public function getInstitution() {

		return $this->institution;
	}


	/**
	 * @return string
	 */
	public function getSchool() {

		return $this->school;
	}


	/**
	 * @return string
	 */
	public function getHowpublished() {

		return $this->howpublished;
	}


	/**
	 * Returns the publish date.
	 *
	 * @param    string $format date format (optional)
	 *
	 * @return    string
	 */
	public function getDatePublished($format) {

		if ($this->date_published) {
			return date($format, strtotime($this->date_published));
		}
		else {
			return false;
		}
	}


	/**
	 * @return string
	 */
	public function getVolume() {

		return $this->volume;
	}


	/**
	 * @return string
	 */
	public function getNumber() {

		return $this->number;
	}


	/**
	 * @return string
	 */
	public function getSeries() {

		return $this->series;
	}


	/**
	 * Returns the abstract.
	 *
	 * @return string
	 */
	public function getAbstract() {

		return $this->abstract;
	}


	/**
	 * @return string
	 */
	public function getNote() {

		return $this->note;
	}


	/**
	 * @return string
	 */
	public function getAddress() {

		return $this->address;
	}


	/**
	 * @return string
	 */
	public function getLocation() {

		return $this->location;
	}
}
