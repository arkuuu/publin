<?php

require_once 'BibLink.php';

class AuthorView implements View {

	private $author;
	private $template;


	public function __construct(Author $author, $template = 'dev') {
		$this -> author = $author;
		$this -> template = './templates/'.$template.'/author.html';
	}


	private function viewPageTitle() {
		return 'publin | '.$this -> author -> getName();
	}


	private function viewName() {
		return $this -> author -> getName();
	}


	private function viewWebsite() {
		return '<a href="http://'.$this -> author -> getWebsite().'" target="_blank">'.$this -> author -> getWebsite().'</a>';
	}


	private function viewContact() {
		return $this -> author -> getContact();
	}


	private function viewText() {
		return $this -> author -> getText();
	}


	private function viewPublications() {

		$publications_string = '';

		foreach ($this -> author -> getPublications() as $publ) {
			$publications_string .= '<li><a href="publpage.php?id='.$publ -> getId().'">'.$publ -> getTitle().'</a> by ';
			foreach ($publ -> getAuthors() as $author) {
				$publications_string .= $author -> getName().', ';
			}

			$publications_string .= $publ -> getMonth().'.'.$publ -> getYear().'</li>'."\n";
		}

		return $publications_string;
	}


	private function viewBibLinks() {

		$bib_links_string = '';

		foreach (BibLink::getServices() as $service) {
			$bib_links_string .= '<li><a href="'.BibLink::getAuthorsLink($this -> author, $service).'" target="_blank">'.$service.'</a></li>';
		}
		return $bib_links_string;
	}


	public function display() {
		
		$file = $this -> template;

		if (file_exists($file)) {
			
			ob_start();
			include $file;
			$output = ob_get_contents();
			ob_end_clean();

			return $output;
		}
		else {
			return 'Could not find template';
		}
	}

}
