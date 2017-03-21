<?php
// Re initialize the DB and Runtime Class
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);
	
	
require('./system/auth/auth.php');

if (isset ($_GET['action']) && $_GET['action'] == 'logout') {
if (isset($_SESSION['angemeldet']) && $_SESSION['angemeldet']) {
$main->useFile('./system/controller/sessions/logout.php');
Logout();
} else {
echo 'Ungültiger Logoutversuch.';
}
}
?>