<?php
// Re initialize the DB
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);

$success_status = false;
$accSettingsContainer = '<div class="account_settingsContainer">
<div class="account_settingsContainerInner">
<div class="tabTitle">
<p>
<div class="icons" id="security"></div>
Passwort ändern
</p>
</div>

<div class="account_settingsInner">
<fieldset>
<legend>
Passwort ändern
</legend>
<form method="POST" action="'.$main->getURI().'&changePassword=update" enctype="multipart/form-data" class="changePasword">

<p>Aktuelles Passwort</p>
<input type="password" name="changePassword_actual" placeholder="Aktuelles Passwort">
<div class="newPassword_container">
<p>Neues Passwort</p>
<input type="password" name="changePassword" placeholder="Neues Passwort">
<p>Neues Passwort bestätigen</p>
<input type="password" name="changePassword_safe" placeholder="Neues Passwort">
</div>
<br>
<input type="submit" name="submit" value="Senden" class="accSettingsSubmit">
</form>

<div class="changeInformation">	
<p>
Folgendes ist bei dem Ändern des Passwortes zu beachten:
</p>
	<ul>
		<li>
		Es sind sämtliche Zeichen erlaubt.
		</li>
		<li>
		Sie müssen ihre Identität bestätigen, indem sie das aktuelle Passwort angeben.
		</li>
		<li>
		Die Felder "Neues Passwort" und "Neues Passwort bestätigen" müssen das selbe Passwort beinhalten.
		</li>
		<li>
		Ihnen wird eine Sicherheitsmail an die angegebene E-Mail Adresse zugesandt, um maximalen Schutz zu gewährleisten. [ Zurzeit deaktiviert ]
		</li>
		<li>
		Das Passwort kann beliebig oft geändert werden.
		</li>
	</ul>
</div>

<font class="sign_changeFailed">';

	if(isset($_GET['changePassword']) && $_GET['changePassword'] == 'update') {
		changePassword();
	}
	
function changePassword() {

// Re initialize the DB
if(!isset($db) || $db = NULL || !isset($connection) || $connection = NULL)
{
	$db         = new Database();
	$connection = $db->mysqli_db_connect();
}

if(!empty($_POST['changePassword_actual']) || !empty($_POST['changePassword_actual']) || !empty($_POST['changePassword_actual'])) {

	$value_actual = mysqli_real_escape_string($GLOBALS['connection'], $_POST['changePassword_actual']);
	$value = mysqli_real_escape_string($GLOBALS['connection'], $_POST['changePassword']);
	$value_safe = mysqli_real_escape_string($GLOBALS['connection'], $_POST['changePassword_safe']);
	$password = $value;
	
	$_SESSION['ID'] = session_id();
	
	if (! isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
$client_ip = $_SERVER['REMOTE_ADDR'];
}
else {
$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}


$get_passValues = $db->query("SELECT extra_val,crypt_level FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."')");
while ($passValues = mysqli_fetch_object($get_passValues)) {
	
	$extra_val = $passValues->extra_val;
	$crypt_level = $passValues->crypt_level;

}

$main->useFile('./system/controller/crypt/md5_pass_change_gen.php');


$actualPW = $db->query("SELECT pass_hash FROM $db->table_accounts WHERE pass_hash=('" . $pass_hash_actual . "') AND sid=('".$_SESSION['ID']."')") or die(mysql_error());
if (mysqli_num_rows($actualPW) == 0) {
	$db->query("INSERT INTO $db->table_accountlogs (message_fail, account_id, sid, user_ip) VALUES ('Passwortänderung fehlgeschlagen. Falsches aktuelles Passwort', (SELECT id FROM $db->table_accounts WHERE sid= ('" . $_SESSION['ID'] . "')), ('" . $_SESSION['ID'] . "'), ('" . $client_ip . "'))") or die(mysql_error());
	$passUpdateError_fatal = 'Das aktuelle Passwort ist falsch!';
		throwError_cc($passUpdateError_fatal);
		return;
}

if ($value != $value_safe) {
$passUpdateError_fatal = 'Die Passwörter stimmen nicht überein';
		throwError_cc($passUpdateError_fatal);
		return;
};

if (strlen($value) < 6) {
$passUpdateError_fatal = 'Ihr Passwort muss mindestens 6 Zeichen lang sein';
		throwError_cc($passUpdateError_fatal);
		return;
};

if (strlen($value) >34) {
$passUpdateError_fatal = 'Passwort ist zu lang, es sind maximal 34 Zeichen erlaubt';
		throwError_cc($passUpdateError_fatal);
		return;
};

if (mysqli_num_rows($actualPW) == 1) {
	$password = $value;
$main->useFile('./system/controller/crypt/md5_hash_gen.php');

	
	$update_password = $db->query("UPDATE $db->table_accounts SET pass_hash=('".$pass_hash_final."'), extra_val=('".$time_visual."'), crypt_level=('".$rand_val."') WHERE sid=('".$_SESSION['ID']."')");
	$update_password = $db->query("INSERT INTO $db->table_accountlogs (message, account_id, sid, user_ip) VALUES ('Passwort erfolgreich geändert', (SELECT id FROM $db->table_accounts WHERE sid= ('" . $_SESSION['ID'] . "')), ('" . $_SESSION['ID'] . "'), ('" . $client_ip . "'))") or die(mysql_error());

	if(!$update_password) {
	$passUpdateError_fatal = 'Kritischer Fehler beim Ändern des Passworts!<br>Versuchen Sie es später erneut.<br>Sollte das Problem weiterhin bestehen bleiben, wenden Sie sich an die Administration.';
		throwError_cc($passUpdateError_fatal);
		return;
	} else {
		$success_status = true;
	if($success_status == true) {
	$changeSuccess = 'Ihre Änderung(en) wurde(n) erfolgreich übernommen!';
	throwSuccess($changeSuccess);
		}
	}
	}
} else {
$passUpdateError_fatal = 'Sie haben nicht alle erforderlichen Informationen eingegeben!';
		throwError_cc($passUpdateError_fatal);
		return;
}
}
$accSettingsContainer .= '
</font>
</fieldset>
</div>
</div>
</div>';

if($success_status == false && !isset($_GET['changePassword'])) {
echo $accSettingsContainer;
}

?>