<?php

require_once 'BibLink.php';

class AuthorView extends View {

	private $author;
	private $template;


	public function __construct(AuthorModel $model, $template = 'dev') {
		
		$this -> author = $model -> getAuthor();
		$this -> template = './templates/'.$template.'/author.html';
	}


	public function getContent() {
		return parent::getContent($this -> template);
	}


	public function viewPageTitle() {
		return $this -> author -> getName();
	}


	public function viewName() {
		return $this -> author -> getName();
	}


	public function viewWebsite() {
		return '<a href="http://'.$this -> author -> getWebsite().'" target="_blank">'.$this -> author -> getWebsite().'</a>';
	}


	public function viewContact() {
		return $this -> author -> getContact();
	}


	public function viewText() {
		return $this -> author -> getText();
	}


	public function viewPublications() {

		$string = '';
		$url = '?p=publication&amp;id=';

		foreach ($this -> author -> getPublications() as $publ) {
			$string .= '<li><a href="'.$url.$publ -> getId().'">'.$publ -> getTitle().'</a> by ';
			foreach ($publ -> getAuthors() as $author) {
				$string .= $author -> getName().', ';
			}

			$string .= $publ -> getMonth().'.'.$publ -> getYear().'</li>'."\n";
		}

		return $string;
	}


	public function viewBibLinks() {

		$string = '';

		foreach (BibLink::getServices() as $service) {
			$string .= '<li><a href="'.BibLink::getAuthorsLink($this -> author, $service).'" target="_blank">'.$service.'</a></li>';
		}
		return $string;
	}

}
