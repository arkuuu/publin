<?php

require_once 'Author.php';
require_once 'Publication.php';

abstract class Export {
	

	public function getStyles() {
		return array('BibTeX', 'XML');
	}
	

	public function getPublicationsExport(Publication $publication, $style) {

		switch ($style) {
			case 'BibTeX':
				return 'TODO';
				break;

			case 'XML':
				return 'TODO';
				break;

			default:
				return 'unknown export style, not defined';
				break;
		}
	}
	
}
