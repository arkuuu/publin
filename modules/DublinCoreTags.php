<?php


namespace publin\modules;

use publin\src\Publication;

class DublinCoreTags {

	public function export(Publication $publication) {

		// http://www.mendeley.com/import/information-for-publishers/
		// NOTE: DublinCore tags are not valid HTML5

		// TODO: <link rel="schema.dcterms" href="http://purl.org/dc/terms/"> when using dcterms.*, e.g. abstract, bibliographic citation

		$result = '<link rel="schema.dc" href="http://purl.org/dc/elements/1.1/" />'."\n";
		$fields = $this->createFields($publication);
		foreach ($fields as $field) {
			if ($field[1]) {
				$result .= '<meta name="'.$field[0].'" content="'.htmlspecialchars($field[1]).'" />'."\n";
			}
		}

		return $result;
	}


	private function createFields(Publication $publication) {

		// NOTE: dc.citation.* are non standard tags recommended by Google Scholar
		$fields = array();
		foreach ($publication->getAuthors() as $author) {
			if ($author->getLastName() && $author->getFirstName()) {
				$fields[] = array('dc.creator', $author->getFirstName().' '.$author->getLastName());
			}
		}
		$fields[] = array('dc.title', $publication->getTitle());
		// TODO: not dcterms.issued and YYYY-MM-DD according to https://wiki.whatwg.org/wiki/MetaExtensions?
		$fields[] = array('dc.issued', $publication->getDatePublished('Y/m/d'));
		$fields[] = array('dc.relation.ispartof', $publication->getJournal());
		$fields[] = array('dc.relation.ispartof', $publication->getBooktitle());
		$fields[] = array('dc.citation.volume', $publication->getVolume());
		$fields[] = array('dc.citation.issue', $publication->getNumber());
		$fields[] = array('dc.citation.spage', $publication->getFirstPage());
		$fields[] = array('dc.citation.epage', $publication->getLastPage());
		//$fields[] = array('dc.identifier', 'todo'); // TODO: link to pdf
		$fields[] = array('dc.publisher', $publication->getInstitution()); // used for institution, too
		$fields[] = array('dc.publisher', $publication->getPublisher());
		$fields[] = array('dc.identifier', $publication->getDoi());

		return $fields;
	}
}
