<?php


namespace publin\modules;

use publin\src\Publication;

class DublinCoreTags {

	public function export(Publication $publication) {

		// TODO: html encode everything!
		$result = '';

		if ($publication->getTitle()) {
			$result .= '<meta name="DC.title" content="'.$publication->getTitle().'" />'."\n";
		}
		foreach ($publication->getAuthors() as $author) {
			$result .= '<meta name="DC.creator" content="'.$author->getFirstName().' '.$author->getLastName().'" />'."\n";
		}
		if ($publication->getDatePublished('Y/m/d')) {
			$result .= '<meta name="DC.issued" content="'.$publication->getDatePublished('Y/m/d').'" />'."\n";
		}
		if ($publication->getJournalName()) {
			$result .= '<meta name="DC.relation.ispartof" content="'.$publication->getJournalName().'" />'."\n";
		}
		if ($publication->getBookName()) {
			$result .= '<meta name="DC.relation.ispartof" content="'.$publication->getBookName().'" />'."\n";
		}
		if ($publication->getVolume()) {
			// non-standard tag recommend by Google Scholar
			$result .= '<meta name="DC.citation.volume" content="'.$publication->getVolume().'" />'."\n";
		}
		if ($publication->getNumber()) {
			// non-standard tag recommend by Google Scholar
			$result .= '<meta name="DC.citation.issue" content="'.$publication->getNumber().'" />'."\n";
		}
		if ($publication->getFirstPage()) {
			// non-standard tag recommend by Google Scholar
			$result .= '<meta name="DC.citation.spage" content="'.$publication->getFirstPage().'" />'."\n";
		}
		if ($publication->getLastPage()) {
			// non-standard tag recommend by Google Scholar
			$result .= '<meta name="DC.citation.epage" content="'.$publication->getLastPage().'" />'."\n";
		}
		if (false) {
			// TODO: link to pdf
			$result .= '<meta name="DC.identifier" content="'.false.'" />'."\n";
		}
		if (false) {
			$result .= '<meta name="citation_issn" content="'.false.'" />'."\n";
		}
		if (false) {
			$result .= '<meta name="citation_isbn" content="'.false.'" />'."\n";
		}
		if ($publication->getInstitution()) {
			// using DC.publisher for institution, too
			$result .= '<meta name="DC.publisher" content="'.$publication->getInstitution().'" />'."\n";
		}
		if ($publication->getPublisherName()) {
			$result .= '<meta name="DC.publisher" content="'.$publication->getPublisherName().'" />'."\n";
		}
		if ($publication->getDoi()) {
			$result .= '<meta name="DC.identifier" content="'.$publication->getDoi().'" />'."\n";
		}

		return $result;
	}
}
