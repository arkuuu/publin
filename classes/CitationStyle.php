<?php

/**
 * Handles citation styles.
 *
 * TODO: comment
 * TODO: implement
 */
abstract class CitationStyle {

	/**
	 * Returns an array with all supported styles.
	 *
	 * @return	array
	 */
	public static function getStyles() {
		return array();
	}


	/**
	 * Returns the citation for given publication and given style.
	 *
	 * @param	Publication		$publication	The publication
	 * @param	string			$style		The style
	 *
	 * @return	string
	 */	
	public static function getCitation(Publication $publication, $style = '') {

		switch ($style) {
			case 'value':
				// TODO: implement
				break;
			
			default:
				$citation = $publication -> getTitle().' by ';

				foreach ($publication -> getAuthors() as $author) {
				 	$citation .= $author -> getName().', ';
				}
				
				$citation .= $publication -> getMonth().'.'.$publication -> getYear();
				break;
		}

		return $citation;
	}

}
