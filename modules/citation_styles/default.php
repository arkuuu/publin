<?php

/* create list of authors */
$authors = $publication -> getAuthors();
$num = count($authors);

if ($num > 0) {
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
	$citation .= ': ';
}

/* the title */
$citation .= '<a href="'.$publication_url.$publication -> getId().'">'
			.$publication -> getTitle().'</a>';

/* show journal or booktitle */
if ($publication -> getJournalName()) {
	$citation .= ', in: <i>'.$publication -> getJournalName().'</i>';
}
else if ($publication -> getBookName()) {
	$citation .= ', in: <i>'.$publication -> getBookName().'</i>';
}

/* shows the publish date */
if ($publication -> getDatePublished('Y, F')) {
	$citation .= ', '.$publication -> getDatePublished('Y, F');
}

