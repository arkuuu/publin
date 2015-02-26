<?php


namespace publin\modules;

use publin\src\Publication;

class PRISMTags {

	public function export(Publication $publication) {

		// http://www.prismstandard.org/specifications/3.0/PRISM_Basic_Metadata_3.0.pdf
		// http://www.prismstandard.org/specifications/3.0/PRISM_Dublin_Core_Metadata_3.0.pdf
		// http://www.mendeley.com/import/information-for-publishers/
		// TODO: html encode everything!
		$result = '';

//		if ($publication->getTitle()) {
//			// TODO: really prism.title? Isn't it part of the dc subset of prism?
//			$result .= '<meta name="prism.title" content="'.$publication->getTitle().'" />'."\n";
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
		if ($publication->getEdition()) {
			$result .= '<meta name="prism.edition" content="'.$publication->getEdition().'" />'."\n";
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
			// TODO: issn
			$result .= '<meta name="prism.issn" content="'.false.'" />'."\n";;
		}
		if (false) {
			// TODO: copyright
			$result .= '<meta name="prism.copyright" content="'.false.'" />'."\n";;
		}
		if ($publication->getIsbn()) {
			$result .= '<meta name="prism.isbn" content="'.$publication->getIsbn().'" />'."\n";;
		}
		if ($publication->getInstitution()) {
			// TODO: check if this is valid
			$result .= '<meta name="prism.organization" content="'.$publication->getInstitution().'" />'."\n";;
		}
		if ($publication->getDoi()) {
			$result .= '<meta name="prism.doi" content="'.$publication->getDoi().'" />'."\n";;
		}

		return $result;
	}
}
