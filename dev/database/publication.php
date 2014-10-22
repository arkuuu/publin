<?php

require 'class/Database.php';
require 'class/Publication.php';
require 'class/Author.php';

$db = new Database('localhost', 'root', 'root', 'dev');

if (!isset($_GET['id'])) {
	$id = 2;
}
else {
	$id = $_GET['id'];
}

$data = $db -> getPublications(array('id' => $id));

$publ = new Publication($data[0], $db);	// TODO: error if id cannot be found


$page_title = $publ -> getTitle().' | publin';

?>



<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?php echo $page_title; ?></title>
</head>
<body>

	<p><a href="index.php">back</a></p>
	<h1><?php echo $publ -> getTitle(); ?></h1>
	<p>by <?php echo $publ -> getAuthorsString(); ?>
		<br/>in TODO JOURNAL
		<br/> <?php echo $publ -> getMonth().'.'.$publ -> getYear(); ?></p>

	<h2>Abstract</h2>
	<p><?php echo $publ -> getAbstractText(); ?></p>

	<h2>References</h2>
	<ul>
		<li>TODO</li>
	</ul>

</body>
</html>
