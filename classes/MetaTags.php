<?php

abstract class MetaTags {


	public function getStyles() {
		return array('highwire');
	}


	public function getPublicationsMetaTags(Publication $publication, $type) {
		// TODO: Implementation
		switch ($type) {
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
