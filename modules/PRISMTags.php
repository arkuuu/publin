<?php


namespace publin\modules;

use publin\src\Publication;

class PRISMTags {

	public function export(Publication $publication) {

		// http://www.prismstandard.org/specifications/3.0/PRISM_Basic_Metadata_3.0.pdf
		// http://www.prismstandard.org/specifications/3.0/PRISM_Dublin_Core_Metadata_3.0.pdf
		// http://www.mendeley.com/import/information-for-publishers/
		// NOTE: PRISM tags are not valid HTML5

		$fields = array();
		//$fields[] = array('prism.title', $publication->getTitle()); // TODO: valid? Isn't it part of the dc subset of prism?
		$fields[] = array('prism.publicationDate', $publication->getDatePublished('Y-m-d'));
		$fields[] = array('prism.publicationYear', $publication->getDatePublished('Y'));
		$fields[] = array('prism.publicationName', $publication->getJournal());
		$fields[] = array('prism.publicationName', $publication->getBooktitle());
		$fields[] = array('prism.volume', $publication->getVolume());
		$fields[] = array('prism.number', $publication->getNumber());
		$fields[] = array('prism.edition', $publication->getEdition());
		$fields[] = array('prism.startingPage', $publication->getFirstPage());
		$fields[] = array('prism.endingPage', $publication->getLastPage());
		//$fields[] = array('prism.url', false); // TODO: link to pdf
		//$fields[] = array('prism.issn', false); // TODO
		$fields[] = array('prism.isbn', $publication->getIsbn());
		//$fields[] = array('prism.copyright', false); // TODO
		$fields[] = array('prism.organization', $publication->getInstitution()); // TODO: check if valid usage
		$fields[] = array('prism.doi', $publication->getDoi());

		$result = '';
		foreach ($fields as $field) {
			if ($field[1]) {
				$result .= '<meta name="'.$field[0].'" content="'.htmlspecialchars($field[1]).'" />'."\n";
			}
		}

		return $result;
	}
}
