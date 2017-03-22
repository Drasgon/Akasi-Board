<?php
// Re initialize the DB and Runtime Class
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);
	
if(!isset($_SESSION['USERACCESS']) || !isset($_SESSION['MODACCESS']) || !isset($_SESSION['ADMINACCESS'])) {

if(isset($GLOBALS['connection'])) {
	$connection = $GLOBALS['connection'];
}

$getLevel = $db->query("SELECT account_level FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."')");
while($level = mysqli_fetch_object($getLevel)) {
	$account_level = $level->account_level;
}

// USER
if($account_level == '1') {
	$_SESSION['USERACCESS'] 	= true;
	$_SESSION['MODACCESS'] 		= false;
	$_SESSION['ADMINACCESS'] 	= false;
	$_SESSION['accountLevelDisplay'] = 'User';
}

// MODERATOR
if($account_level == '2') {
	$_SESSION['USERACCESS'] 	= true;
	$_SESSION['MODACCESS'] 		= true;
	$_SESSION['ADMINACCESS'] 	= false;
	$_SESSION['accountLevelDisplay'] = 'Moderator';
}

// ADMINISTRATOR
if($account_level == '3') {
	$_SESSION['USERACCESS'] 	= true;
	$_SESSION['MODACCESS'] 		= true;
	$_SESSION['ADMINACCESS']	= true;
	$_SESSION['accountLevelDisplay'] = 'Administrator';
}

}
?>