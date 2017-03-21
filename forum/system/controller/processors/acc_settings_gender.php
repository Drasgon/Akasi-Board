<?php
function changeGender() {

if (!isset($db) || $db == NULL)
	{
		$db = NEW Database();
		$connection = $db->mysqli_db_connect();
	}
	if (!isset($main) || $main == NULL)
		$main = new Board($db, $connection);

	$value = mysqli_real_escape_string($GLOBALS['connection'], $_POST['changeGender']);
	
if ((isset($value) && empty($value)) || !isset($value))  {
$genderUpdateError_selection .= 'Sie müssen mindestens eine Auswahl treffen!';
throwError($genderUpdateError_selection);
return;
} else {

if ($value >= 4 || $value == '0')  {
$genderUpdateError_values = 'Ungültige Angaben!';
throwError($genderUpdateError_values);
return;
}
	
	if (! isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
$client_ip = $_SERVER['REMOTE_ADDR'];
}
else {
$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
$_SESSION['ID'] = session_id();	
	$update_gender = $db->query("UPDATE $db->table_accdata SET gender=('".$value."') WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."'))");

	if(!$update_gender) {
	$genderUpdateError_fatal .= 'Krititscher Fehler beim Update des Geschlechts.';
		throwError($genderUpdateError_fatal);
		return;
	} else {
	$success_status = true;
	if($success_status == true) {
	$changeSuccess = 'Ihre Änderung(en) wurde(n) erfolgreich übernommen!';
	throwSuccess($changeSuccess);
		}
	}
} }
?>