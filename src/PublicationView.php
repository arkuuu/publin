<?php

namespace publin\src;

use Exception;

/**
 * View for publication page
 *
 * TODO: comment
 */
class PublicationView extends View {

	/**
	 * @var    Publication
	 */
	private $publication;


	/**
	 * Constructs the publication view.
	 *
	 * @param Publication $publication
	 */
	public function __construct(Publication $publication) {

		parent::__construct('publication');
		$this->publication = $publication;
	}


	/**
	 * Shows the page title.
	 *
	 * @return    string
	 */
	public function showPageTitle() {

		return $this->showTitle();
	}


	/**
	 * Shows the publication's title.
	 *
	 * @return    string
	 */
	public function showTitle() {

		return $this->publication->getTitle();
	}


	/**
	 * Shows the meta tags.
	 *
	 * @return    string
	 */
	public function showMetaTags() {

		$string = '';

		foreach (MetaTags::getStyles() as $style) {
			$string .= MetaTags::getPublicationsMetaTags($this->publication, $style);
		}

		return $string;
	}


	/**
	 * Shows the publication's authors.
	 *
	 * @return string
	 * @throws Exception
	 */
	public function showAuthors() {

		$result = false;
		$authors = $this->publication->getAuthors();
		$num = count($authors);

		if ($num > 0) {
			$i = 1;
			foreach ($authors as $author) {

				$url = '?p=author&amp;id=';
				$author_id = $author->getId();
				$author_name = $author->getName();

				if ($author_id && $author_name) {
					$author = '<a href="'.$url.$author_id.'">'.$author_name.'</a>';
				}
				else if ($author_name) {
					$author = $author_name;
				}
				else {
					// $author = 'Unknown Author';
					throw new Exception('the publication with id '.$this->publication->getId().' has an author with no name');
				}

				if ($i == 1) {
					/* first author */
					$result .= $author;
				}
				else if ($i == $num) {
					/* last author */
					$result .= ' and '.$author;
				}
				else {
					/* all other authors */
					$result .= ', '.$author;
				}
				$i++;
			}
		}

		return $result;
	}


	/**
	 * Shows the publication's publish date.
	 *
	 * @param string $format
	 *
	 * @return string
	 */
	public function showDatePublished($format = 'F Y') {

		return $this->publication->getDatePublished($format);
	}


	/**
	 * Shows the publication's type.
	 *
	 * @return    string
	 */
	public function showType() {

		$url = '?p=browse&amp;by=type&amp;id=';
		$type_name = $this->publication->getTypeName();
		$type_id = $this->publication->getTypeId();

		if ($type_id && $type_name) {
			return '<a href="'.$url.$type_id.'">'.$type_name.'</a>';
		}
		else if ($type_name) {
			return $type_name;
		}
		else {
			return false;
		}
	}


	/**
	 * Shows the publication's journal name.
	 *
	 * @return    string
	 */
	public function showJournal() {

		$url = '?p=browse&amp;by=journal&amp;id=';
		$journal_name = $this->publication->getJournalName();
		$journal_id = $this->publication->getJournalId();

		if ($journal_id && $journal_name) {
			return '<a href="'.$url.$journal_id.'">'.$journal_name.'</a>';
		}
		else if ($journal_name) {
			return $journal_name;
		}
		else {
			return false;

		}
	}


	/**
	 * Shows the publication's book name.
	 *
	 * @return    string
	 */
	public function showBook() {

		$url = '?p=publication&amp;id=';
		$book_name = $this->publication->getBookName();
		$book_id = $this->publication->getBookId();

		if ($book_id && $book_name) {
			return '<a href="'.$url.$book_id.'">'.$book_name.'</a>';
		}
		else if ($book_name) {
			return $book_name;
		}
		else {
			return false;

		}
	}


	/**
	 * @param string $divider
	 *
	 * @return string
	 */
	public function showPages($divider = '-') {

		return $this->publication->getPages($divider);
	}


	/**
	 * @return bool|string
	 */
	public function showPublisher() {

		$url = '?p=browse&amp;by=publisher&amp;id=';
		$publisher_name = $this->publication->getPublisherName();
		$publisher_id = $this->publication->getPublisherId();

		if ($publisher_id && $publisher_name) {
			return '<a href="'.$url.$publisher_id.'">'.$publisher_name.'</a>';
		}
		else if ($publisher_name) {
			return $publisher_name;
		}
		else {
			return false;

		}
	}


	/**
	 * @return string
	 */
	public function showInstitution() {

		return $this->publication->getInstitution();
	}


	/**
	 * @return string
	 */
	public function showSchool() {

		return $this->publication->getSchool();
	}


	/**
	 * @return string
	 */
	public function showHowpublished() {

		return $this->publication->getHowpublished();
	}


	/**
	 * @return string
	 */
	public function showAddress() {

		return $this->publication->getAddress();
	}


	/**
	 * @return string
	 */
	public function showDoi() {

		return $this->publication->getDoi();
	}


	/**
	 * @return string
	 */
	public function showIsbn() {

		return $this->publication->getIsbn();
	}


	/**
	 * @return string
	 */
	public function showNote() {

		return $this->publication->getNote();
	}


	/**
	 * @return string
	 */
	public function showSeries() {

		return $this->publication->getSeries();
	}


	/**
	 * @return string
	 */
	public function showNumber() {

		return $this->publication->getNumber();
	}


	/**
	 * @return string
	 */
	public function showVolume() {

		return $this->publication->getVolume();
	}


	/**
	 * @return string
	 */
	public function showEdition() {

		return $this->publication->getEdition();
	}


	/**
	 * Shows the publication's abstract.
	 *
	 * @return    string
	 */
	public function showAbstract() {

		return $this->publication->getAbstract();
	}


	/**
	 * Shows the publication's references.
	 *
	 * TODO: implement
	 *
	 * @return    string
	 */
	public function showReferences() {

		return false;
	}


	/**
	 * Shows the publication's key terms.
	 *
	 * @param    string $separator Optional separator between key terms
	 *
	 * @return    string
	 */
	public function showKeyTerms($separator = ', ') {

		$key_terms = $this->publication->getKeyTerms();

		if (!empty($key_terms)) {

			$string = '';
			$url = '?p=browse&amp;by=key_term&amp;id=';

			foreach ($key_terms as $key_term) {

				$key_term_id = $key_term->getId();
				$key_term_name = $key_term->getName();

				if ($key_term_id && $key_term_name) {
					$string .= '<a href="'.$url.$key_term_id.'">'.$key_term_name.'</a>'.$separator;
				}
				else if ($key_term_name) {
					$string .= $key_term_name.$separator;
				}
			}

			return substr($string, 0, -(strlen($separator)));
		}
		else {
			return false;
		}
	}


	/**
	 * Shows links to other bibliographic indexes for this publication.
	 *
	 * @return    string
	 */
	public function showBibLinks() {

		$string = '';

		foreach (BibLink::getServices() as $service) {
			$string .= '<li><a href="'.BibLink::getPublicationsLink($this->publication, $service).'" target="_blank">'.$service.'</a></li>';
		}

		return $string;
	}


	/**
	 * Shows links to export formats.
	 *
	 * @param    string $format The export format
	 *
	 * @return    string
	 */
	public function showExport($format) {

		try {
			$export = new FormatHandler($format);

			return $export->export($this->publication->toArray());
		} catch (Exception $e) {
			return 'Error: '.$e->getMessage();
		}

	}

}
