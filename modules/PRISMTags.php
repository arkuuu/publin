<?php


namespace publin\modules;

use publin\src\Publication;

class PRISMTags {

	public function export(Publication $publication) {

		// TODO: html encode everything!
		$result = '';

		if ($publication->getTitle()) {
			$result .= '<meta name="prism.title" content="'.$publication->getTitle().'" />'."\n";
		}
//		foreach ($publication->getAuthors() as $author) {
//			$result .= '<meta name="DC.creator" content="'.$author->getFirstName().' '.$author->getLastName().'" />'."\n";
//		}
		if ($publication->getDatePublished('Y-m-d')) {
			$result .= '<meta name="prism.publicationDate" content="'.$publication->getDatePublished('Y-m-d').'" />'."\n";
			$result .= '<meta name="prism.publicationYear" content="'.$publication->getDatePublished('Y').'" />'."\n";
		}
		if ($publication->getJournalName()) {
			$result .= '<meta name="prism.publicationName" content="'.$publication->getJournalName().'" />'."\n";
		}
		if ($publication->getBookName()) {
			$result .= '<meta name="prism.publicationName" content="'.$publication->getBookName().'" />'."\n";
		}
		if ($publication->getVolume()) {
			$result .= '<meta name="prism.volume" content="'.$publication->getVolume().'" />'."\n";
		}
		if ($publication->getNumber()) {
			$result .= '<meta name="prism.number" content="'.$publication->getNumber().'" />'."\n";
		}
		if ($publication->getFirstPage()) {
			$result .= '<meta name="prism.startingPage" content="'.$publication->getFirstPage().'" />'."\n";
		}
		if ($publication->getLastPage()) {
			$result .= '<meta name="prism.endingPage" content="'.$publication->getLastPage().'" />'."\n";
		}
		if (false) {
			// TODO: link to pdf
			$result .= '<meta name="prism.url" content="'.false.'" />'."\n";;
		}
		if (false) {
			$result .= '<meta name="prism.issn" content="'.false.'" />'."\n";;
		}
//		if (false) {
//			$result .= '<meta name="citation_isbn" content="'.false.'" />'."\n";;
//		}
//		if ($publication->getInstitution()) {
//			// using DC.publisher for institution, too
//			$result .= '<meta name="DC.publisher" content="'.$publication->getInstitution().'" />'."\n";;
//		}
//		if ($publication->getPublisherName()) {
//			$result .= '<meta name="DC.publisher" content="'.$publication->getPublisherName().'" />'."\n";;
//		}
		if ($publication->getDoi()) {
			$result .= '<meta name="prism.doi" content="'.$publication->getDoi().'" />'."\n";;
		}

		return $result;
	}
}
