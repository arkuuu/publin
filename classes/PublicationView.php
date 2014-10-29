<?php

require_once 'MetaTags.php';
require_once 'BibLink.php';
require_once 'Export.php';

class PublicationView extends View {
	
	private $publication;
	private $template;


	public function __construct(PublicationModel $model, $template = 'dev') {

		$this -> publication = $model -> getPublication();
		$this -> template = './templates/'.$template.'/publication.html';
	}


	public function getContent() {
		return parent::getContent($this -> template);
	}


	public function viewPageTitle() {
		return $this -> publication -> getTitle();
	}


	public function viewMetaTags() {

		$string = '';

		foreach (MetaTags::getStyles() as $style) {
			$string .= MetaTags::getPublicationsMetaTags($this -> publication, $style);
		}

		return $string;
	}


	public function viewTitle() {
		return $this -> publication -> getTitle();
	}


	public function viewAuthors($separator = ' and ') {

		$string = '';
		$url = '?p=author&amp;id=';

		foreach ($this -> publication -> getAuthors() as $author) {
			$string .= '<a href ="'.$url.$author -> getId().'">'
							.$author -> getName().'</a>'.$separator;
		}

		return substr($string, 0, -(strlen($separator)));	
	}


	public function viewPublishDate() {
		return $this -> publication -> getMonth().'.'.$this -> publication -> getYear();
	}


	public function viewType() {
		return $this -> publication -> getType();
	}


	public function viewAbstract() {
		return $this -> publication -> getAbstract();
	}


	public function viewReferences() {
		// TODO: Implementation
		return 'TODO';
	}


	public function viewKeyTerms($separator = ', ') {

		$string = '';
		$url = '?p=browse&amp;by=key_term&amp;id=';

		foreach ($this -> publication -> getKeyTerms() as $key_term) {
			$string .= '<a href="'.$url.$key_term -> getId().'">'
								.$key_term -> getName().'</a>'.$separator;
		}

		return substr($string, 0, -(strlen($separator)));
	}


	public function viewBibLinks() {

		$string = '';

		foreach (BibLink::getServices() as $service) {
			$string .= '<li><a href="'.BibLink::getPublicationsLink($this -> publication, $service).'" target="_blank">'.$service.'</a></li>';
		}

		return $string;
	}


	public function viewExport() {
		$string = '';

		foreach (Export::getStyles() as $style) {
			$string .= '<li><a href="'.Export::getPublicationsExport($this -> publication, $style).'" target="_blank">'.$style.' (TODO)</a></li>';
		}

		return $string;
	}
		
}
