<?php


namespace publin\modules;

use publin\src\Publication;

class HighwirePressTags {

	public function export(Publication $publication) {

		// http://www.mendeley.com/import/information-for-publishers/

		$keywords = '';
		foreach ($publication->getKeywords() as $keyword) {
			if ($keyword->getName()) {
				$keywords .= $keyword->getName().'; ';
			}
		}
		$keywords = substr($keywords, 0, -2);

		$fields = array();
		$fields[] = array('citation_title', $publication->getTitle());
		foreach ($publication->getAuthors() as $author) {
			if ($author->getLastName() && $author->getFirstName()) {
				$fields[] = array('citation_author', $author->getLastName().', '.$author->getFirstName());
			}
		}
		$fields[] = array('citation_publication_date', $publication->getDatePublished('Y/m/d'));
		$fields[] = array('citation_journal_title', $publication->getJournalName());
		$fields[] = array('citation_conference_title', $publication->getBookName());
		$fields[] = array('citation_volume', $publication->getVolume());
		if ($publication->getTypeName() == 'techreport') {
			$fields[] = array('citation_technical_report_number', $publication->getNumber());
		}
		else {
			$fields[] = array('citation_issue', $publication->getNumber());
		}
		$fields[] = array('citation_firstpage', $publication->getFirstPage());
		$fields[] = array('citation_lastpage', $publication->getLastPage());
		//$fields[] = array('citation_pdf_url', false); // TODO: link to pdf
		//$fields[] = array('citation_issn', false); // TODO
		$fields[] = array('citation_isbn', $publication->getIsbn());
		$fields[] = array('citation_publisher', $publication->getPublisherName());
		if ($publication->getTypeName() == 'techreport') {
			$fields[] = array('citation_technical_report_institution', $publication->getInstitution());
		}
		else if (in_array($publication->getTypeName(), array('phdthesis', 'masterthesis'))) {
			$fields[] = array('citation_dissertation_institution', $publication->getInstitution());
		}
		$fields[] = array('citation_doi', $publication->getDoi());
		$fields[] = array('citation_keywords', $keywords);

		$result = '';
		foreach ($fields as $field) {
			if ($field[1]) {
				$result .= '<meta name="'.$field[0].'" content="'.htmlspecialchars($field[1]).'" />'."\n";
			}
		}

		return $result;
	}
}
