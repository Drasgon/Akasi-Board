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
$getInitial = $db->query("SELECT user_title FROM $db->table_accdata WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."'))");
while($initialContentQuery = mysqli_fetch_object($getInitial)) {
	$initialContent = $initialContentQuery->user_title;
}

$success_status = '';
$accSettingsContainer = '
<div class="account_settingsContainer">
<div class="account_settingsContainerInner">
<div class="tabTitle">
<p>
<div class="icons" id="profiledit"></div>
Titel bearbeiten
</p>
</div>

<div class="account_settingsInner">
<fieldset>
<legend>
Titel bearbeiten
</legend>
<form method="POST" action="'.$main->getURI().'&titleAction=update">

<input type="text" name="changeTitle" value="'.$initialContent.'">
<input type="submit" name="submit" value="Senden" class="accSettingsSubmit">

        </form>
		
<div class="changeInformation">	
<p>
Folgendes ist bei dem Ändern des Titels zu beachten:
</p>
	<ul>
		<li>
		Zeichen von a - Z sowie Zahlen von 0 - 9 sind erlaubt.
		</li>
		<li>
		Sie können ihren Titel alle 31 Tage ändern.
		</li>
		<li>
		Anstößige sowie gewaltverherrlichende oder verspottende Titel sind strengstens Verboten. Bei Verstoß ist mit Strafen in Form einer temporären Accountsperre sowie Strafpunkten zu rechnen.
		</li>
		<li>
		Forenleitende Funktionstitel wie z. B. Administrator oder Moderator sind auch in abgeleiteter Form nicht zulässig.
		</li>
	</ul>
</div>
		
<font class="sign_changeFailed">';

	if(isset($_GET['titleAction']) && $_GET['titleAction'] == 'update') {
		changeTitle();
	}
	
function changeTitle() {

// Re initialize the DB
if(!isset($db) || $db = NULL || !isset($connection) || $connection = NULL)
{
	$db         = new Database();
	$connection = $db->mysqli_db_connect();
}

if(!empty($_POST['changeTitle'])) {

	$value = mysqli_real_escape_string($GLOBALS['connection'], $_POST['changeTitle']);
	
	$_SESSION['ID'] = session_id();
	
	if (! isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
$client_ip = $_SERVER['REMOTE_ADDR'];
}
else {
$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}


if (strlen($value) < 3)  {
$avatarUpdateError_fatal .= 'Ihr Titel muss mindestens 3 Zeichen besitzen.';
		throwError_cc($avatarUpdateError_fatal);
		return;
};

if (strlen($value) > 20) {
$avatarUpdateError_fatal .= 'Ihr Titel darf maximal 20 Zeichen besitzen.';
		throwError_cc($avatarUpdateError_fatal);
		return;
};

if (strlen(trim($value)) == 0) {
$avatarUpdateError_fatal .= 'Ihr Titel darf nicht ausschließlich aus Leerzeichen bestehen!';
		throwError_cc($avatarUpdateError_fatal);
		return;
};
	
	$update_signature = $db->query("UPDATE $db->table_accdata SET user_title=('".$value."') WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."'))");

	if(!$update_signature) {
		$avatarUpdateError_fatal .= 'Krititscher Fehler beim Update des Titels.';
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
$titleUpdateError_fatal = 'Sie haben nicht alle erforderlichen Informationen eingegeben!';
		throwError_cc($titleUpdateError_fatal);
		return;
}
}

$accSettingsContainer .= '</font>
</fieldset>
</div>
</div>
</div>';

if($success_status == false && !isset($_GET['titleAction'])) {
echo $accSettingsContainer;
}
?>