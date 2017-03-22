<?php
function changeDisplay() {

	if (!isset($db) || $db == NULL)
	{
		$db = NEW Database();
		$connection = $db->mysqli_db_connect();
	}
	if (!isset($main) || $main == NULL)
		$main = new Board($db, $connection);

	$value_emoticons = mysqli_real_escape_string($GLOBALS['connection'], $_POST['changeGeneralSettings_emoticons']);
	$value_cursor 	 = mysqli_real_escape_string($GLOBALS['connection'], $_POST['changeGeneralSettings_cursor']);
	$value_ajaxMsg 	 = mysqli_real_escape_string($GLOBALS['connection'], $_POST['changeGeneralSettings_ajaxMsg']);
	$value_template	 = mysqli_real_escape_string($GLOBALS['connection'], $_POST['css_template']);
	$value_lang		 = mysqli_real_escape_string($GLOBALS['connection'], $_POST['lang_template']);
	
	
	if (empty($value_emoticons) || empty($value_cursor) || empty($value_ajaxMsg) || empty($value_template) || empty($value_lang))  {
	$displayUpdateError_selection .= 'Mindestens eine der Angaben ist ungültig!';
	throwError($displayUpdateError_selection);
	return;
	} else {

	if ($value_emoticons >= '3 '
	|| $value_emoticons == '0'
	|| $value_cursor == '0'
	|| $value_cursor >= '3'
	|| $value_ajaxMsg == '0'
	|| $value_ajaxMsg >= '3'
	)  {
	$displayUpdateError_values = 'Mindestens eine der Angaben ist ungültig!';
	throwError($displayUpdateError_values);
	return;
	}
		
		if (! isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	$client_ip = $_SERVER['REMOTE_ADDR'];
	}
	else {
	$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}

		$update_emoticons = $db->query("UPDATE $db->table_accdata SET emoticons=('".$value_emoticons."') WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."'))");
		$update_cursor	  = $db->query("UPDATE $db->table_accdata SET user_cursor=('".$value_cursor."') WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."'))");
		$update_ajax	  = $db->query("UPDATE $db->table_accdata SET ajax_msg=('".$value_ajaxMsg."') WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."'))");
		$update_template  = $db->query("UPDATE $db->table_accdata SET design_template=('".$value_template."'), language=('".$value_lang."') WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."'))");
		
		$_SESSION['cursor'] = $value_cursor;

		
		
		if(!$update_emoticons || !$update_cursor || !$update_ajax || !$update_template) {
		$displayUpdateError_fatal = 'Krititscher Fehler beim Übernehmen der Einstellungen.';
			throwError($displayUpdateError_fatal);
			return;
		} else {
		$success_status = true;
		if($success_status == true) {
		unset($_SESSION['language']);
		$changeSuccess = 'Ihre Änderung(en) wurde(n) erfolgreich übernommen!';
		throwSuccess($changeSuccess);
			}
		}
	}
}
?>