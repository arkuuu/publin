<?php

require 'classes/Database.php';
require 'classes/PublicationModel.php';
require 'classes/View.php';
require 'classes/PublicationView.php';

$db = new Database('localhost', 'root', 'root', 'dev');

if (!isset($_GET['id'])) {
	$id = 2;
}
else {
	$id = $_GET['id'];
}

$model = new PublicationModel($id, $db);

$view = new PublicationView($model -> getPublication());

echo $view -> display();

?>
