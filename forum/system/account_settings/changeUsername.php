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
$getInitial = $db->query("SELECT username FROM $db->table_accdata WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."'))");
while($initialContentQuery = mysqli_fetch_object($getInitial)) {
	$initialContent = $initialContentQuery->username;
}

$success_status = '';
$accSettingsContainer = '
<div class="account_settingsContainer">
<div class="account_settingsContainerInner">
<div class="tabTitle">
<p>
<div class="icons" id="profiledit"></div>
Usernamen bearbeiten
</p>
</div>

<div class="account_settingsInner">
<fieldset>
<legend>
Usernamen bearbeiten
</legend>
<form method="POST" action="'.$main->getURI().'&usernameAction=update">

<input type="text" name="changeUsername" value="'.$initialContent.'">
<input type="submit" name="submit" value="Senden" class="accSettingsSubmit">

        </form>
		
<div class="changeInformation">	
<p>
Folgendes ist bei dem Ändern des Usernamens zu beachten:
</p>
	<ul>
		<li>
		Der Username muss mindestens 3 Zeichen und darf maximal 13 Zeichen lang sein.
		</li>
		<li>
		Zeichen von a - Z sowie Zahlen von 0 - 9 sind erlaubt.
		</li>
		<li>
		Sie können ihren Usernamen alle 365 Tage ändern. [ Zurzeit deaktiviert ]
		</li>
		<li>
		Anstößige sowie gewaltverherrlichende oder verspottende Usernamen sind strengstens Verboten. Bei Verstoß ist mit Strafen in Form einer temporären Account Sperre oder/und Strafpunkten zu rechnen.
		</li>
	</ul>
</div>

<font class="sign_changeFailed">';

	if(isset($_GET['usernameAction']) && $_GET['usernameAction'] == 'update') {
		changeUsername();
	}
	
function changeUsername() {

// Re initialize the DB
if(!isset($db) || $db = NULL || !isset($connection) || $connection = NULL)
{
	$db         = new Database();
	$connection = $db->mysqli_db_connect();
}

if(!empty($_POST['changeUsername'])) {

	$value = mysqli_real_escape_string($GLOBALS['connection'], $_POST['changeUsername']);
	
	$_SESSION['ID'] = session_id();
	
	if (! isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
$client_ip = $_SERVER['REMOTE_ADDR'];
}
else {
$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}


if (strlen($value) < 3)  {
$avatarUpdateError_fatal .= 'Der Username muss mindestens 3 Zeichen lang sein';
		throwError_cc($avatarUpdateError_fatal);
		return;
};

if (strlen($value) > 13) {
$avatarUpdateError_fatal .= 'Username ist zu lang, es sind maximal 13 Zeichen erlaubt';
		throwError_cc($avatarUpdateError_fatal);
		return;
};

if (strlen(trim($value)) == 0) {
$avatarUpdateError_fatal .= 'Ihr Titel darf nicht ausschließlich aus Leerzeichen bestehen!';
		throwError_cc($avatarUpdateError_fatal);
		return;
};
	
	$update_username = $db->query("UPDATE $db->table_accounts SET username=('".$value."') WHERE sid=('".$_SESSION['ID']."')");

	if(!$update_username) {
		$avatarUpdateError_fatal .= 'Krititscher Fehler beim Update des Usernamen.';
		throwError_cc($avatarUpdateError_fatal);
		return;
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

$accSettingsContainer .= '</font>
</fieldset>
</div>
</div>
</div>';

if($success_status == false && !isset($_GET['usernameAction'])) {
echo $accSettingsContainer;
}
?>