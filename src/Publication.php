<?php

namespace publin\src;

use InvalidArgumentException;

/**
 * Class Publication
 *
 * @package publin\src
 */
class Publication extends Entity {

	protected $id;
	protected $date_added;
	protected $type_id;
	protected $type;
	protected $study_field;
	protected $study_field_id;
	protected $title;
	protected $authors;
	protected $journal;
	protected $volume;
	protected $number;
	protected $booktitle;
	protected $series;
	protected $edition;
	protected $pages_from;
	protected $pages_to;
	protected $note;
	protected $location;
	protected $date_published;
	protected $publisher;
	protected $institution;
	protected $school;
	protected $address;
	protected $howpublished;
	protected $copyright;
	protected $doi;
	protected $isbn;
	protected $abstract;
	protected $keywords;
	protected $files;
	protected $full_text_file;
	protected $urls;
	protected $citations;
	protected $foreign;


	/**
	 * @param array      $data
	 * @param Author[]   $authors
	 * @param Keyword[]  $keywords
	 * @param File[]     $files
	 * @param Citation[] $citations
	 */
	public function __construct(array $data, array $authors = array(), array $keywords = array(), array $files = array(), array $urls = array(), array $citations = array()) {

		parent::__construct($data);
		$this->setAuthors($authors);
		$this->setKeywords($keywords);
		$this->setFiles($files);
		$this->setUrls($urls);
		$this->setCitations($citations);
	}


	/**
	 * @return string|null
	 */
	public function getId() {

		return $this->id;
	}


	/**
	 * @return string|null
	 */
	public function getTypeId() {

		return $this->type_id;
	}


	/**
	 * @return string|null
	 */
	public function getStudyField() {

		return $this->study_field;
	}


	/**
	 * @return string|null
	 */
	public function getStudyFieldId() {

		return $this->study_field_id;
	}


	/**
	 * @return string|null
	 */
	public function getIsbn() {

		return $this->isbn;
	}


	/**
	 * @return string|null
	 */
	public function getDoi() {

		return $this->doi;
	}


	/**
	 * @return string|null
	 */
	public function getCopyright() {

		return $this->copyright;
	}


	/**
	 * @param $divider
	 *
	 * @return string|null
	 */
	public function getPages($divider) {

		if ($this->pages_from && $this->pages_to) {
			return $this->pages_from.$divider.$this->pages_to;
		}
		else {
			return null;
		}
	}


	/**
	 * @return string|null
	 */
	public function getFirstPage() {

		return $this->pages_from;
	}


	/**
	 * @return string|null
	 */
	public function getLastPage() {

		return $this->pages_to;
	}


	/**
	 * Returns the date the publication was added.
	 *
	 * @param    string $format date format (optional)
	 *
	 * @return    string|null
	 */
	public function getDateAdded($format = null) {

		if ($format && $this->date_added) {
			return date($format, strtotime($this->date_added));
		}
		else {
			return $this->date_added;
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
	 * Returns an array with the citations of this publication.
	 * The array consists of Citation objects.
	 *
	 * @return    Citation[]
	 */
	public function getCitations() {
	
		return $this->citations;
	}
	
	/**
	 * @param Citation[] $citations
	 *
	 * @return bool
	 */
	public function setCitations(array $citations) {
	
		$this->citations = array();

		foreach ($citations as $citation) {

			if ($citation instanceof Citation) {
				$this->citations[] = $citation;
			}
			else {
				throw new InvalidArgumentException('must be array with Citation objects');
			}
		}

		return true;
	}


	/**
	 * @return    File|null
	 */
	public function getFullTextFile() {

		return $this->full_text_file;
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
				if ($file->isFullText()) {
					$this->full_text_file = $file;
				}
				$this->files[] = $file;
			}
			else {
				throw new InvalidArgumentException('must be array with File objects');
			}
		}

		return true;
	}


	/**
	 * @return Url[]
	 */
	public function getUrls() {

		return $this->urls;
	}


	/**
	 * @param Url[] $urls
	 *
	 * @return bool
	 */
	public function setUrls(array $urls) {

		$this->urls = array();
		foreach ($urls as $url) {
			if ($url instanceof Url) {
				$this->urls[] = $url;
			}
			else {
				throw new InvalidArgumentException('must be an array of Url objects');
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
	 * @return string|null
	 */
	public function getTypeName() {

		return $this->type;
	}


	/**
	 * Returns the title of the publication.
	 *
	 * @return string|null
	 */
	public function getTitle() {

		return $this->title;
	}


	/**
	 * @return string|null
	 */
	public function getJournal() {

		return $this->journal;
	}


	/**
	 * @return string|null
	 */
	public function getBooktitle() {

		return $this->booktitle;
	}


	/**
	 * @return string|null
	 */
	public function getPublisher() {

		return $this->publisher;
	}


	/**
	 * @return string|null
	 */
	public function getEdition() {

		return $this->edition;
	}


	/**
	 * @return string|null
	 */
	public function getInstitution() {

		return $this->institution;
	}


	/**
	 * @return string|null
	 */
	public function getSchool() {

		return $this->school;
	}


	/**
	 * @return string|null
	 */
	public function getHowpublished() {

		return $this->howpublished;
	}


	/**
	 * Returns the publish date.
	 *
	 * @param    string $format date format (optional)
	 *
	 * @return    string|null
	 */
	public function getDatePublished($format = null) {

		if ($format && $this->date_published) {
			return date($format, strtotime($this->date_published));
		}
		else {
			return $this->date_published;
		}
	}


	/**
	 * @return string|null
	 */
	public function getVolume() {

		return $this->volume;
	}


	/**
	 * @return string|null
	 */
	public function getNumber() {

		return $this->number;
	}


	/**
	 * @return string|null
	 */
	public function getSeries() {

		return $this->series;
	}


	/**
	 * Returns the abstract.
	 *
	 * @return string|null
	 */
	public function getAbstract() {

		return $this->abstract;
	}


	/**
	 * @return string|null
	 */
	public function getNote() {

		return $this->note;
	}


	/**
	 * @return string|null
	 */
	public function getAddress() {

		return $this->address;
	}


	/**
	 * @return string|null
	 */
	public function getLocation() {

		return $this->location;
	}


	/**
	 * @return string|null
	 */
	public function getPublinUrl() {

		return Request::createUrl(array('p' => 'publication', 'id' => $this->id));
	}
	
	
	/**
	 * @return int 0 or 1
	 */
	public function getForeign() {
		// For the foreign attribute a checkbox is used. If the checkbox is not
		// set, we do not get a value. Hence set it to the dafault value.
		if (isset($this->foreign)) {
			return $this->foreign;
		} else {
			return 0;
		}
	}
}
