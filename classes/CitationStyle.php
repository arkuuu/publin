<?php

abstract class CitationStyle {


	public function getStyles() {
		return array();
	}


	public function getCitation(Publication $publication, $style = '') {

		$citation = '';

		switch ($style) {
			case 'value':
				# code...
				break;
			
			default:
				$citation .= $publication -> getTitle().' by ';

				foreach ($publication -> getAuthors() as $author) {
				 	$citation .= $author -> getName().', '
				}
				
				$citation .= $publication -> getMonth().'.'.$publication -> getYear();
				break;
		}

		return $citation;
	}

}
