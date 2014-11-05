<?php

foreach ($publication -> getAuthors() as $author) {
 	$citation .= $author -> getFirstName(true).' '
 				.$author -> getLastName().', ';
}

$citation .= '"'.$publication -> getTitle().'", '
			.$publication -> getMonth().'.'
			.$publication -> getYear();
