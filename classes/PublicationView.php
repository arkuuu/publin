<?php

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
	 * The path to the template file
	 * @var	string
	 */
	private $template;



	/**
	 * Constructs the publication view.
	 *
	 * @param	PublicationModel	$model		The publication model
	 * @param	string				$template	The template folder
	 */
	public function __construct(PublicationModel $model, $template = 'dev') {

		$this -> publication = $model -> getPublication();
		$this -> template = './templates/'.$template.'/publication.html';
	}


	/**
	 * Returns the content of the template file using parent method.
	 *
	 * @return	string
	 */
	public function getContent() {
		return parent::getContent($this -> template);
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
	public function showPublishDate() {
		return $this -> publication -> getPublishDate();
	}


	/**
	 * Shows the publication's type.
	 *
	 * @return	string
	 */
	public function showType() {
		return $this -> publication -> getType();
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
	 * @return	string
	 */
	public function showExport() {
		$string = '';

		foreach (Export::getFormats() as $format) {
			$string .= '<li><a href="'.Export::getPublicationsExport($this -> publication, $format).'" target="_blank">'.$format.' (TODO)</a></li>';
		}

		return $string;
	}
		
}
