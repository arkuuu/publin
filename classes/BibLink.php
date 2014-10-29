<?php

require_once 'Author.php';
require_once 'Publication.php';

abstract class BibLink {
	

	public static function getServices() {
		return array('Google', 'Base');
	}
	

	public static function getAuthorsLink(Author $author, $service) {

		switch ($service) {
			case 'Google':
				return 'http://scholar.google.com/scholar?q='
						.urlencode('"'.$author -> getFirstName().' '.$author -> getLastName().'"');
				break;

			case 'Base':
				return 'http://www.base-search.net/Search/Results?type0[]=aut&lookfor0[]='
						.urlencode('"'.$author -> getFirstName().' '.$author -> getLastName().'"');
				break;

			default:
				return 'unknown service, not defined';
				break;
		}
	}


	public static function getPublicationsLink(Publication $publication, $service) {

		switch ($service) {
			case 'Google':
				return 'http://scholar.google.com/scholar?q=allintitle:'
						.urlencode('"'.$publication -> getTitle().'"');
				break;

			case 'Base':
				return 'http://www.base-search.net/Search/Results?lookfor=tit:'
						.urlencode('"'.$publication -> getTitle().'"');
				break;

			default:
				return 'unknown service, not defined';
				break;
		}
	}
	
}
