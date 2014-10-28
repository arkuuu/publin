<?php

require_once 'classes/View.php';
require_once 'classes/GenericView.php';



if (!isset($_GET['p'])) {
	$page = null;
}
else {
	$page = $_GET['p'];
}



$view = new GenericView($page);

echo $view -> display();

?>
