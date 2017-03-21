<?php
function changeSignature() {

if (!isset($db) || $db == NULL)
	{
		$db = NEW Database();
		$connection = $db->mysqli_db_connect();
	}
	if (!isset($main) || $main == NULL)
		$main = new Board($db, $connection);

if(!empty($_POST['signatureEditArea'])) {

	$value = mysqli_real_escape_string($GLOBALS['connection'], $_POST['signatureEditArea']);
	
	
	
	if (! isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
$client_ip = $_SERVER['REMOTE_ADDR'];
}
else {
$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}

if (str_word_count($value) >= 300) {
$passUpdateError_fatal = 'Ihre Signatur darf maximal 300 Wörter besitzen.';
		throwError_cc($passUpdateError_fatal);
return;
};

if (strlen(trim($value)) == 0) {
$passUpdateError_fatal = 'Ihre Signatur darf nicht ausschließlich aus Leerzeichen bestehen!';
		throwError_cc($passUpdateError_fatal);
return;
};
$_SESSION['ID'] = session_id();
	$update_signature = $db->query("UPDATE $db->table_accdata SET signature=('".$value."') WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."'))");

	if(!isset($update_signature) || (isset($update_signature) && $update_signature == false)) {
		$passUpdateError_fatal = 'Kritischer Fehler beim Ändern der Signatur!<br>Versuchen Sie es später erneut.<br>Sollte das Problem weiterhin bestehen bleiben, wenden Sie sich an die Administration.';
		throwError_cc($passUpdateError_fatal);
	} else {
		$success_status = true;
	if($success_status == true) {
	$changeSuccess = 'Ihre Änderung(en) wurde(n) erfolgreich übernommen!';
	throwSuccess($changeSuccess);
		}
	}
} else {
$passUpdateError_fatal = 'Sie haben nicht alle erforderlichen Informationen eingegeben!';
		throwError_cc($passUpdateError_fatal);
		return;
}
}
?>