<?php

require_once 'View.php';
require_once 'MetaTags.php';
require_once 'BibLink.php';
require_once 'Export.php';

/**
 * View for publication page
 *
 * TODO: comment
 */
class PublicationView extends View {

	/**
	 * @var	Publication
	 */	
	private $publication;



	/**
	 * Constructs the publication view.
	 *
	 * @param	PublicationModel	$model		The publication model
	 * @param	string				$template	The template folder
	 */
	public function __construct(Publication $publication) {

		parent::__construct('publication');
		$this -> publication = $publication;
	}


	/**
	 * Shows the page title.
	 *
	 * @return	string
	 */
	public function showPageTitle() {
		return $this -> showTitle();
	}


	/**
	 * Shows the meta tags.
	 *
	 * @return	string
	 */
	public function showMetaTags() {

		$string = '';

		foreach (MetaTags::getStyles() as $style) {
			$string .= MetaTags::getPublicationsMetaTags($this -> publication, $style);
		}

		return $string;
	}


	/**
	 * Shows the publication's title.
	 *
	 * @return	string
	 */
	public function showTitle() {
		$title = $this -> publication -> getTitle();

		if ($title) {
			return $title;
		}
		else {
			throw new Exception('the publication with id '.$this -> publication -> getId().' has no title');
		}
	}


	/**
	 * Shows the publication's authors.
	 *
	 * @return	string
	 */
	public function showAuthors() {

		$string = '';
		$authors = $this -> publication -> getAuthors();
		$num = count($authors);

		if ($num < 1) {
			throw new Exception('the publication with id '.$this -> publication -> getId().' has no authors');
		}
		else {
			$i = 1;
			foreach ($authors as $author) {

				$url = '?p=author&amp;id=';
				$author_id = $author -> getId();
				$author_name = $author -> getName();

				if ($author_id && $author_name) {
					$author = '<a href="'.$url.$author_id.'">'.$author_name.'</a>';
				}
				else if ($author_name) {
					$author = $author_name;
				}
				else {
					// $author = 'Unknown Author';
					throw new Exception('the publication with id '.$this -> publication -> getId().' has an author with no name');
				}

				if ($i == 1) {
					/* first author */
					$string .= $author;
				}
				else if ($i == $num) {
					/* last author */
					$string .= ' and '.$author;
				}
				else {
					/* all other authors */
					$string .= ', '.$author;
				}
				$i++;
			}
		}

		return $string;	
	}


	/**
	 * Shows the publication's publish date.
	 *
	 * @return	string
	 */
	public function showDatePublished($format = 'F Y') {
		$date = $this -> publication -> getDatePublished($format);

		if (!empty($date)) {
			return $date;
		}
		else {
			throw new Exception('the publication with id '.$this -> publication -> getId().' has no publish date');
		}
	}


	/**
	 * Shows the publication's type.
	 *
	 * @return	string
	 */
	public function showType() {

		$url = '?p=browse&amp;by=type&amp;id=';
		$type_name = $this -> publication -> getTypeName();
		$type_id = $this -> publication -> getTypeId();

		if ($type_id && $type_name) {
			return '<a href="'.$url.$type_id.'">'.$type_name.'</a>';
		}
		// TODO: change this to not only show number
		else if ($type_id) {
			return '<a href="'.$url.$type_id.'">'.$type_id.'</a>';
		}
		else if ($type_name) {
			return $type_name;
		}
		else {
			throw new Exception('the publication with id '.$this -> publication -> getId().' has no type');
		}
	}


	/**
	 * Shows the publication's journal name.
	 *
	 * @return	string
	 */
	public function showJournal() {

		$url = '?p=browse&amp;by=journal&amp;id=';
		$journal_name = $this -> publication -> getJournalName();
		$journal_id = $this -> publication -> getJournalId();

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


	public function showPages($divider = '-') {
		return $this -> publication -> getPages($divider);
	}

	/**
	 * Shows the publication's abstract.
	 *
	 * @return	string
	 */
	public function showAbstract() {
		$abstract = $this -> publication -> getAbstract();

		if (!empty($abstract)) {
			return $abstract;
		}
		else {
			return 'no abstract given';
		}
	}

	/**
	 * Shows the publication's references.
	 *
	 * TODO: implement
	 *
	 * @return	string
	 */
	public function showReferences() {
		return '<li>TODO</li>';
	}


	/**
	 * Shows the publication's key terms.
	 *
	 * @param	string	$separator	Optional separator between key terms
	 *
	 * @return	string
	 */
	public function showKeyTerms($separator = ', ') {
		$key_terms = $this -> publication -> getKeyTerms();

		if (!empty($key_terms)) {

			$string = '';
			$url = '?p=browse&amp;by=key_term&amp;id=';

			foreach ($this -> publication -> getKeyTerms() as $key_term) {
				$string .= '<a href="'.$url.$key_term -> getId().'">'
									.$key_term -> getName().'</a>'.$separator;
			}

			return substr($string, 0, -(strlen($separator)));
		}
		else {

			return 'no key terms assigned';
		}
	}


	/**
	 * Shows links to other bibliographic indexes for this publication.
	 *
	 * @return	string
	 */
	public function showBibLinks() {
		$string = '';

		foreach (BibLink::getServices() as $service) {
			$string .= '<li><a href="'.BibLink::getPublicationsLink($this -> publication, $service).'" target="_blank">'.$service.'</a></li>';
		}

		return $string;
	}


	/**
	 * Shows links to export formats.
	 *
	 * @param	string	$format	The export format
	 *
	 * @return	string
	 */
	public function showExport($format) {
		return Export::getPublicationsExport($this -> publication, $format);
	}
		
}
