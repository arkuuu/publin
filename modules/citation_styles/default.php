<?php

/* create list of authors */
$authors = $publication -> getAuthors();
$num = count($authors);

if ($num < 1) {
	$citation .= 'unknown author';
}
else {
	$i = 1;
	foreach ($authors as $author) {
		if ($i == 1) {
			/* first author */
			$citation .= $author -> getFirstName(true).' '
			 			.$author -> getLastName();
		}
		else if ($i > 5) {
			/* break with "et al." if too many authors */
			$citation .= ' et al.';
			break;
		}
		else if ($i == $num) {
			/* last author */
			$citation .= ' and '.$author -> getFirstName(true).' '
	 	 				.$author -> getLastName();
		}

		else {
			/* all other authors */
			$citation .= ', '.$author -> getFirstName(true).' '
	 	 				.$author -> getLastName();
		}
		$i++;
	}
}

/* the rest */
$citation .= ', <i>"'.$publication -> getTitle().'"</i>, '
			.$publication -> getMonth().'.'
			.$publication -> getYear();
