<?php

require_once 'classes/Database.php';
require_once 'classes/BrowseModel.php';
require_once 'classes/View.php';
require_once 'classes/BrowseView.php';

$db = new Database('localhost', 'root', 'root', 'dev');

if (!isset($_GET['id'])) {
	$id = null;
}
else {
	$id = $_GET['id'];
}

if (!isset($_GET['by'])) {
	$by = null;
}
else {
	$by = $_GET['by'];
}

$model = new BrowseModel($by, $id, $db);

$view = new BrowseView($model);

echo $view -> display();

?>
