<?php

require_once 'classes/Controller.php';

if (!isset($_GET['p'])) {
	$p = 'start';
}
else {
	$p = $_GET['p'];
}

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

$controller = new Controller();
echo $controller -> run($p, $id, $by);

?>
