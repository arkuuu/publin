<?php

require 'class/Database.php';
require 'class/Publication.php';
require 'class/Author.php';

$db = new Database('localhost', 'root', 'root', 'dev');

if (!isset($_GET['id'])) {
	$id = 1;
}
else {
	$id = $_GET['id'];
}

$data = $db -> getAuthors(array('id' => $id));

$author = new Author($data[0], $db);	// TODO: error if id cannot be found

$page_title = $author -> getName().' | publin';

?>



<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?php echo $page_title; ?></title>
</head>
<body>

	<p><a href="index.php">back</a></p>
	<h1><?php echo $author -> getName(); ?></h1>
	<p>Personal webpage: <a href="http://<?php echo $author -> getWebsite(); ?>" target="_blank"><?php echo $author -> getWebsite(); ?></a></p>

	<h2>Contact</h2>
	<p><?php echo $author -> getContact(); ?></p>

	<h2>Text</h2>
	<p><?php echo $author -> getText(); ?></p>

	<h2>Publications</h2>
	<ul>
		<?php
		foreach ($author -> getPublications() as $publ) {
			echo '<li><a href="publication.php?id='.$publ -> getId().'">'.$publ -> getTitle().'</a> by '.$publ -> getAuthorsString().', '.$publ -> getMonth().'.'.$publ -> getYear().'</li>'."\n";
		}
		?>
	</ul>

	<h2>Search in</h2>
	<ul>
		<li><a href="<?php echo $author -> getBibLink('google'); ?>" target="_blank">Google Scholar</a></li>
		<li><a href="<?php echo $author -> getBibLink('base'); ?>" target="_blank">BASE</a></li>
	</ul>

</body>
</html>

