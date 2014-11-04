<?php

/**
 * Handles the meta tags.
 *
 * TODO: comment
 */
abstract class MetaTags {

	/**
	 * Returns an array with all supported styles.
	 *
	 * @return	array
	 */
	public static function getStyles() {
		return array('highwire');
	}


	/**
	 * Returns the meta tags for given publication and given style.
	 *
	 * @param	Publication		$publication	The publication
	 * @param	string			$style		The style
	 *
	 * @return	string
	 */	
	public static function getPublicationsMetaTags(Publication $publication, $style) {

		switch ($style) {
			case 'highwire':
				$tags = 
					'<meta name="citation_title" content="'.$publication -> getTitle().'" />'."\n\t".
					'<meta name="citation_publication_date" content ="'.$publication -> getYear().'" />'."\n\t".
					'<meta name="citation_online_date" content ="" />'."\n\t";
				foreach ($publication -> getAuthors() as $author) {
					$tags .= '<meta name="citation_author" content ="'.$author -> getName().'" />'."\n\t";
				}
				$tags .= '<meta name="citation_pdf_url" content ="" />'."\n";

				return $tags;
				break;

			case 'dublin_core':
				// TODO
				break;
			
			default:
				# code...
				break;
		}
	}

}
