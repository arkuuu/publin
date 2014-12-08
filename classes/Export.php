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
		// TODO
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

		$file = './modules/export/'.$format.'.php';
		$export = '';

		if (file_exists($file)) {			
			include $file;
		}
		else {
			// TODO: show error
			$export = 'no export file for '.$format;
		}

		return $export;
	}	
}
