<?php

require_once '../../src/Database.php';
require_once '../../src/Auth.php';

$db = new Database();
$auth = new Auth($db);

// $auth -> logout();
var_dump($auth -> checkLoginStatus());
$auth -> validateLogin('Arne', 'test');
var_dump($auth -> checkLoginStatus());
var_dump($auth -> checkPermission('publication_submit'));

if ($auth -> checkLoginStatus()) {
	echo '<br />Hello '.$_SESSION['user'] -> getName();
}
else {
	echo '<br/>Hello Guest';
}
