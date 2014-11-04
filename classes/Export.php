<?php

require_once 'Author.php';
require_once 'Publication.php';

/**
 * Handles the export formats.
 *
 * This should later be replaced by a modular system looking for export formats
 * found in the /export directory.
 *
 * TODO: comment
 * TODO: implement
 */
abstract class Export {
	
	/**
	 * Returns an array with all supported formats.
	 *
	 * @return	array
	 */
	public static function getFormats() {
		return array('BibTeX', 'XML');
	}
	
	/**
	 * TODO: comment
	 *
	 * @param	Publication		$publication	The publication
	 * @param	string			$format			The format
	 *
	 * @return	string
	 */	
	public static function getPublicationsExport(Publication $publication, $format) {

		switch ($format) {
			case 'BibTeX':
				return 'TODO';
				break;

			case 'XML':
				return 'TODO';
				break;

			default:
				return 'unknown export format, not defined';
				break;
		}
	}
	
}
