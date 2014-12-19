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
	public function __construct(PublicationModel $model, $template = 'dev') {

		parent::__construct($template.'/publication.html');
		$this -> publication = $model -> getPublication();
	}


	/**
	 * Shows the page title.
	 *
	 * @return	string
	 */
	public function showPageTitle() {
		return $this -> publication -> getTitle();
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
		return $this -> publication -> getTitle();
	}


	/**
	 * Shows the publication's authors.
	 *
	 * @param	string	$separator	Optional separator between authors
	 *
	 * @return	string
	 */
	public function showAuthors() {

		$string = '';
		$url = '?p=author&amp;id=';
		$authors = $this -> publication -> getAuthors();
		$num = count($authors);

		if ($num < 1) {
			$string .= 'unknown author';
		}
		else {
			$i = 1;
			foreach ($authors as $author) {
				if ($i == 1) {
					/* first author */
					$string .= '<a href="'.$url.$author -> getId().'">'
					 			.$author -> getName().'</a>';
				}
				else if ($i == $num) {
					/* last author */
					$string .= ' and <a href="'.$url.$author -> getId().'">'
			 	 				.$author -> getName().'</a>';
				}
				else {
					/* all other authors */
					$string .= ', <a href="'.$url.$author -> getId().'">'
			 	 				.$author -> getName().'</a>';
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
	public function showDatePublished() {
		$string = $this -> publication -> getDatePublished('F Y');

		if (!empty($string)) {
			return $string;
		}
		else {
			return 'unknown';
		}
	}


	/**
	 * Shows the publication's journal name.
	 *
	 * @return	string
	 */
	public function showJournal() {
		$string = $this -> publication -> getJournal();

		if (!empty($string)) {
			$url = '?p=browse&amp;by=journal&amp;id=';

			return '<a href="'.$url.$this -> publication -> getJournalId().'">'.$string.'</a>';
		}
		else {
			return '';
		}

	}


	/**
	 * Shows the publication's type.
	 *
	 * @return	string
	 */
	public function showType() {
		$string = $this -> publication -> getType();

		if (!empty($string)) {
			return $string;
		}
		else {
			return 'unknown';
		}
	}


	/**
	 * Shows the publication's abstract.
	 *
	 * @return	string
	 */
	public function showAbstract() {
		$string = $this -> publication -> getAbstract();

		if (!empty($string)) {
			return $string;
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
