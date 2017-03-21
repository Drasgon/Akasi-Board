<?php
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);
	
	
	$default_template = 2;
	
if(!isset($_SESSION['angemeldet']) || $_SESSION['angemeldet'] == false) {

	$layout_file = '<link rel=stylesheet href="./css/akb-style-'.$default_template.'.css" media="screen">';
	
} else {

	$getActualTemplate = $db->query("SELECT design_template FROM $db->table_accdata WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."'))") or die(mysql_error());
		while($actualTemplate = mysqli_fetch_object($getActualTemplate)) {
			$template = $actualTemplate->design_template;
			}
			
			
	
	switch($template) {
	case 1:
		$layout_file = '<link rel=stylesheet href="./css/akb-style-1.css" media="screen">';
	break;
	case 2:
		$layout_file = '<link rel=stylesheet href="./css/akb-style-2.css" media="screen">';
	break;
	default:
		$layout_file = '<link rel=stylesheet href="./css/akb-style-'.$default_template.'.css" media="screen">';
	break;
	
}
}
	
	echo $layout_file;
?>