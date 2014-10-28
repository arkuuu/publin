<?php

require_once 'MetaTags.php';
require_once 'BibLink.php';
require_once 'Export.php';

class PublicationView implements View {
	
	private $publication;
	private $template;


	public function __construct(Publication $publication, $template = 'dev') {
		$this -> publication = $publication;
		$this -> template = './templates/'.$template.'/publication.html';
	}


	private function viewPageTitle() {
		return 'publin | '.$this -> publication -> getTitle();
	}


	private function viewMetaTags() {

		$tags_string = '';

		foreach (MetaTags::getStyles() as $style) {
			$tags_string .= MetaTags::getPublicationsMetaTags($this -> publication, $style);
		}

		return $tags_string;
	}


	private function viewTitle() {
		return $this -> publication -> getTitle();
	}


	private function viewAuthors($separator = ' and ') {

		$url = 'autpage.php?id=';
		$authors_string = '';

		foreach ($this -> publication -> getAuthors() as $author) {
			$authors_string .= '<a href ="'.$url.$author -> getId().'">'
							.$author -> getName().'</a>'.$separator;
		}

		return substr($authors_string, 0, -(strlen($separator)));			

	}


	private function viewPublishDate() {
		return $this -> publication -> getMonth().'.'.$this -> publication -> getYear();
	}


	private function viewType() {
		return $this -> publication -> getType();
	}


	private function viewAbstract() {
		return $this -> publication -> getAbstract();
	}


	private function viewReferences() {
		// TODO: Implementation
		return 'TODO';
	}


	private function viewKeyTerms($separator = ', ') {

		$url = 'dev/database/index.php?action=browseKeyTerm&id=';
		$key_terms_string = '';

		foreach ($this -> publication -> getKeyTerms() as $key_term) {
			$key_terms_string .= '<a href="'.$url.$key_term -> getId().'">'
								.$key_term -> getName().'</a>'.$separator;
		}

		return substr($key_terms_string, 0, -(strlen($separator)));
	}


	private function viewBibLinks() {

		$bib_links_string = '';

		foreach (BibLink::getServices() as $service) {
			$bib_links_string .= '<li><a href="'.BibLink::getPublicationsLink($this -> publication, $service).'" target="_blank">'.$service.'</a></li>';
		}

		return $bib_links_string;
	}


	private function viewExport() {
		$export_string = '';

		foreach (Export::getStyles() as $style) {
			$export_string .= '<li><a href="'.Export::getPublicationsExport($this -> publication, $style).'" target="_blank">'.$style.' (TODO)</a></li>';
		}

		return $export_string;
	}


	public function display() {

	    $file = $this -> template;  
	    $exists = file_exists($file);  

		if ($exists){  

			ob_start();  

			include $file;  
			$output = ob_get_contents();  
			ob_end_clean();  

			return $output;  
		}  
	    else {  
	        // Template-File existiert nicht-> Fehlermeldung.  
	        return 'could not find template';  
	    }
	}
		
}
