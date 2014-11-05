<?php

$authors = $publication -> getAuthors();
$num = count($authors);

if ($num < 1) {
	$citation .= 'unknown author, ';
}
else {
	$citation .= $authors[0] -> getFirstName(true).' '
		 			.$authors[0] -> getLastName();

	for ($i=1; $i < $num; $i++) { 
		if ($i == $num - 1) {
			$citation .= ' and '.$authors[$num-1] -> getFirstName(true).' '
		 				.$authors[$num-1] -> getLastName();
		}
		else {
			$citation .= ', '.$authors[$i] -> getFirstName(true).' '
	 	 				.$authors[$i] -> getLastName();
	 	}
	}
}

$citation .= ', <i>"'.$publication -> getTitle().'"</i>, '
			.$publication -> getMonth().'.'
			.$publication -> getYear();
