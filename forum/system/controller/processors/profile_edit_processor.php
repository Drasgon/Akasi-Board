<?php
/*
Profile edit handler START
*/
function edit_profile() {

if (isset($_SESSION['angemeldet']) && $_SESSION['angemeldet'] == true) {

	if (!isset($db) || $db == NULL)
	{
		$db = NEW Database();
		$connection = $db->mysqli_db_connect();
	}
	if (!isset($main) || $main == NULL)
		$main = new Board($db, $connection);
	
	$location = mysqli_real_escape_string($GLOBALS['connection'], $_POST['location']);
	$hobbies = mysqli_real_escape_string($GLOBALS['connection'], $_POST['hobbies']);
	$about = mysqli_real_escape_string($GLOBALS['connection'], $_POST['about']);
	$msngr_skype = mysqli_real_escape_string($GLOBALS['connection'], $_POST['msngr_skype']);
	$msngr_icq = mysqli_real_escape_string($GLOBALS['connection'], $_POST['msngr_icq']);
	$sn_facebook = mysqli_real_escape_string($GLOBALS['connection'], $_POST['sn_facebook']);
	$sn_twitter = mysqli_real_escape_string($GLOBALS['connection'], $_POST['sn_twitter']);
	$sn_googleplus = mysqli_real_escape_string($GLOBALS['connection'], $_POST['sn_googleplus']);
	$sn_tumblr = mysqli_real_escape_string($GLOBALS['connection'], $_POST['sn_tumblr']);
	
	$updateProfile = $db->query("SELECT id FROM $db->table_profile WHERE id=(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))");

	if(mysqli_num_rows($updateProfile) <= 0) {
	
	$updateProfile = $db->query("INSERT INTO $db->table_profile (id, location, hobbies, about, msngr_skype, msngr_icq, sn_facebook, sn_twitter, sn_googleplus, sn_tumblr) VALUES (
	(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')), 
	('".$location."'), 
	('".$hobbies."'), 
	('".$about."'), 
	('".$msngr_skype."'), 
	('".$msngr_icq."'), 
	('".$sn_facebook."'), 
	('".$sn_twitter."'), 
	('".$sn_googleplus."'), 
	('".$sn_tumblr."')
		 )");
	}
	
	if(mysqli_num_rows($updateProfile) >= 1) {
	
	$updateProfile = $db->query("UPDATE $db->table_profile SET
	location=('".$location."')
	, hobbies=('".$hobbies."')
	, about=('".$about."')
	, msngr_skype=('".$msngr_skype."')
	, msngr_icq=('".$msngr_icq."')
	, sn_facebook=('".$sn_facebook."')
	, sn_twitter=('".$sn_twitter."')
	, sn_googleplus=('".$sn_googleplus."')
	, sn_tumblr=('".$sn_tumblr."') 
		WHERE id=(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))");
	}
	
	if(!$updateProfile) {
		return false;
	} else {
		return true;
	}

} else  {
require('./system/interface/errorpage_cc.php');
$errorMsg = 'Sie verfügen nicht übr die erforderlichen Zugriffsrechte um diese Seite zu besuchen.';
throwError($errorMsg);
}
}
?>