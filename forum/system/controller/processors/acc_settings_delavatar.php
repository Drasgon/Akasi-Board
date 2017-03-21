<?php
function deleteAvatar() {

if (!isset($db) || $db == NULL)
	{
		$db = NEW Database();
		$connection = $db->mysqli_db_connect();
	}
	if (!isset($main) || $main == NULL)
		$main = new Board($db, $connection);
	
	
	$oldAvatar = $db->query("SELECT avatar FROM (".$db->table_accdata.") WHERE account_id=(SELECT id FROM (".$db->table_accounts.") WHERE sid=('".$_SESSION['ID']."'))");
	while($oldAvatar_fetch = mysqli_fetch_object($oldAvatar)) {
			$oldAvatar_path = $oldAvatar_fetch->avatar;
	}

	if($oldAvatar_path != './images/avatars/default.jpg' && $oldAvatar_path != './images/avatars/default.png') {
		if($main->checkImage($oldAvatar_path)) unlink($oldAvatar_path);
		
		$deleteAvatar = $db->query("UPDATE (".$db->table_accdata.") SET avatar=DEFAULT WHERE account_id=(SELECT id FROM (".$db->table_accounts.") WHERE sid=('".$_SESSION['ID']."'))");
	}
	
	
if(isset($deleteAvatar) && !$deleteAvatar) {
$avatarUpdateError_fatal = 'Unautorisierter Löschversuch des Avatars.';
		throwError_cc($avatarUpdateError_fatal);
		return;
} else {
$success_status = true;
	if($success_status == true) {
	$changeSuccess = 'Ihre Änderung(en) wurde(n) erfolgreich übernommen!';
	throwSuccess($changeSuccess);
	}
}
	
}
?>