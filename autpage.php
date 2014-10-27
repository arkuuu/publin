<?php

require_once 'classes/Database.php';
require_once 'classes/AuthorModel.php';
require_once 'classes/View.php';
require_once 'classes/AuthorView.php';

$db = new Database('localhost', 'root', 'root', 'dev');

if (!isset($_GET['id'])) {
	$id = 2;
}
else {
	$id = $_GET['id'];
}

$model = new AuthorModel($id, $db);

$view = new AuthorView($model -> getAuthor());

echo $view -> display();

?>
