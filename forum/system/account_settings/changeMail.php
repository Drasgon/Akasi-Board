<?php
// Re initialize the DB
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);

$_SESSION['ID'] = session_id();
$getInitial = $db->query("SELECT email FROM $db->table_accdata WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."'))");
while($initialContentQuery = mysqli_fetch_object($getInitial)) {
	$initialContent = $initialContentQuery->email;
}

$success_status = '';
$accSettingsContainer = '<div class="account_settingsContainer">
<div class="account_settingsContainerInner">
<div class="tabTitle">
<p>
<div class="icons" id="messages"></div>
E-Mail bearbeiten
</p>
</div>

<div class="account_settingsInner">
<fieldset>
<legend>
E-Mail bearbeiten
</legend>
<form method="POST" action="'.$main->getURI().'&mailAction=update" enctype="multipart/form-data">

<input type="text" name="changeMail" value="'.$initialContent.'">
<input type="submit" name="submit" value="Senden" class="accSettingsSubmit">

        </form>
		
<div class="changeInformation">	
<p>
Folgendes ist bei dem Ändern der E-Mail Adresse zu beachten:
</p>
	<ul>
		<li>
		Es sind alle Mail Provider erlaubt.
		</li>
		<li>
		Durch die Änderung der E-Mail stimmen Sie zu, dass ihnen eine Verifizierungsmail zugesandt wird, um die Existenz der Adresse zu bestätigen.
		</li>
		<li>
		Sie erhalten keine Werbemails bzw. Newsletter von uns, außer sie stimmen dem ausdrücklich durch eine Kennzeichnung des dafür vorgesehenen Feldes zu.
		</li>
	</ul>
</div>
		
<font class="sign_changeFailed">';

	if(isset($_GET['mailAction']) && $_GET['mailAction'] == 'update') {
		changeMail();
	}
	
function changeMail() {

// Re initialize the DB
if(!isset($db) || $db = NULL || !isset($connection) || $connection = NULL)
{
	$db         = new Database();
	$connection = $db->mysqli_db_connect();
}

if(!empty($_POST['changeMail'])) {

	$value = mysqli_real_escape_string($GLOBALS['connection'], $_POST['changeMail']);
	
	$_SESSION['ID'] = session_id();
	
	if (! isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
$client_ip = $_SERVER['REMOTE_ADDR'];
}
else {
$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}


if (strlen($value) < 6)  {
$avatarUpdateError_fatal .= 'Fehler beim Ändern der E-Mail.<br>Überprüfen Sie die Schreibweise der Adresse und versuchen Sie es erneut.';
		throwError_cc($avatarUpdateError_fatal);
		return;
};

if (strlen($value) > 254) {
$avatarUpdateError_fatal .= 'Die angegebene E-Mail Adresse ist zu lang.';
		throwError_cc($avatarUpdateError_fatal);
		return;
};

if (strlen(trim($value)) == 0) {
$avatarUpdateError_fatal .= 'Fehler beim Ändern der E-Mail.<br>Überprüfen Sie die Schreibweise der Adresse und versuchen Sie es erneut.';
		throwError_cc($avatarUpdateError_fatal);
		return;
};
	
	$update_signature = $db->query("UPDATE $db->table_accdata SET email=('".$value."') WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."'))");
	$update_signature = $db->query("UPDATE $db->table_accounts SET email=('".$value."') WHERE sid=('".$_SESSION['ID']."')");

	if(!$update_signature) {
		$avatarUpdateError_fatal .= 'Fehler beim Ändern der E-Mail.<br>Überprüfen Sie die Schreibweise der Adresse und versuchen Sie es erneut.';
		throwError_cc($avatarUpdateError_fatal);
		return;
	} else {
	$changeSuccess = 'Ihre Änderung(en) wurde(n) erfolgreich übernommen!';
	throwSuccess($changeSuccess);
	}
} else {
$passUpdateError_fatal = 'Sie haben nicht alle erforderlichen Informationen eingegeben!';
		throwError_cc($passUpdateError_fatal);
		return;
}
}

$accSettingsContainer .= '</font>
</fieldset>
</div>
</div>
</div>';

if($success_status == false && !isset($_GET['mailAction'])) {
echo $accSettingsContainer;
}

?>