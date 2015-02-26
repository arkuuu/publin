<?php


namespace publin\modules;

use publin\src\Publication;

class HighwirePressTags {

	public function export(Publication $publication) {

		// TODO: html encode everything!
		// http://www.mendeley.com/import/information-for-publishers/

		$result = '';

		if ($publication->getTitle()) {
			$result .= '<meta name="citation_title" content="'.$publication->getTitle().'" />'."\n";
		}
		foreach ($publication->getAuthors() as $author) {
			if ($author->getLastName() && $author->getFirstName()) {
				$result .= '<meta name="citation_author" content="'.$author->getLastName().', '.$author->getFirstName().'" />'."\n";
			}
		}
		if ($publication->getDatePublished('Y/m/d')) {
			$result .= '<meta name="citation_publication_date" content="'.$publication->getDatePublished('Y/m/d').'" />'."\n";
		}
		if ($publication->getJournalName()) {
			$result .= '<meta name="citation_journal_title" content="'.$publication->getJournalName().'" />'."\n";
		}
		if ($publication->getBookName()) {
			$result .= '<meta name="citation_conference_title" content="'.$publication->getBookName().'" />'."\n";
		}
		if ($publication->getVolume()) {
			$result .= '<meta name="citation_volume" content="'.$publication->getVolume().'" />'."\n";
		}
		if ($publication->getNumber()) {
			if ($publication->getTypeName() == 'techreport') {
				$result .= '<meta name="citation_technical_report_number" content="'.$publication->getNumber().'" />'."\n";
			}
			else {
				$result .= '<meta name="citation_issue" content="'.$publication->getNumber().'" />'."\n";
			}
		}
		if ($publication->getFirstPage()) {
			$result .= '<meta name="citation_firstpage" content="'.$publication->getFirstPage().'" />'."\n";
		}
		if ($publication->getLastPage()) {
			$result .= '<meta name="citation_lastpage" content="'.$publication->getLastPage().'" />'."\n";
		}
		if (false) {
			$result .= '<meta name="citation_pdf_url" content="'.false.'" />'."\n";
		}
		if (false) {
			$result .= '<meta name="citation_issn" content="'.false.'" />'."\n";
		}
		if (false) {
			$result .= '<meta name="citation_isbn" content="'.false.'" />'."\n";
		}
		if ($publication->getInstitution()) {
			if ($publication->getTypeName() == 'techreport') {
				$result .= '<meta name="citation_technical_report_institution" content="'.false.'" />'."\n";
			}
			else if (in_array($publication->getTypeName(), array('phdthesis', 'masterthesis'))) {
				$result .= '<meta name="citation_dissertation_institution" content="'.$publication->getInstitution().'" />'."\n";
			}
			// TODO: what happens with institution if neither techreport nor thesis?
		}
		if ($publication->getPublisherName()) {
			$result .= '<meta name="citation_publisher" content="'.$publication->getPublisherName().'" />'."\n";
		}
		if ($publication->getDoi()) {
			$result .= '<meta name="citation_doi" content="'.$publication->getDoi().'" />'."\n";
		}

		return $result;
	}
}
