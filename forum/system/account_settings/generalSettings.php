<?php
if (!isset($db) || $db == NULL)
{
	$db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
	$main = new Board($db, $connection);
	

	if(isset($_GET['action']) && $_GET['action'] == 'updateDisplay') {
		$main->useFile('./system/controller/processors/acc_settings_generaldisplay.php');
			changeDisplay();
	}

$success_status = '';
$accSettingsContainer = '<div class="account_settingsContainer">
<div class="account_settingsContainerInner">
<div class="tabTitle">
<p>
<div class="icons" id="accountpanel"></div>
Anzeigeeinstellungen
</p>
</div>

<div class="account_settingsInner">
<form method="POST" action="'.$main->getURI().'&action=updateDisplay">
<fieldset>
<legend>
Foren Emoticons
</legend>';

$getactualSetting = $db->query("SELECT emoticons FROM $db->table_accdata WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."'))");
while($actualSetting = mysqli_fetch_object($getactualSetting)) {
	$emoticons_settings = $actualSetting->emoticons;
}

switch($emoticons_settings) {
	case '0':
		$emoticonvalue1 = 'checked="checked"';
		$emoticonvalue2 = '';
		break;
	case 1:
		$emoticonvalue1 = 'checked="checked"';
		$emoticonvalue2 = '';
		break;
	case 2:
		$emoticonvalue1 = '';
		$emoticonvalue2 = 'checked="checked"';
		break;
	default:
		$emoticonvalue1 = '';
		$emoticonvalue2 = '';
		break;
}

$accSettingsContainer .= '
<div class="accSettings_radioalign">
	<input '.$emoticonvalue1.' type="radio" name="changeGeneralSettings_emoticons" value="1" id="emoticons_1"><label for="emoticons_1"> Aktiviert ( Standard )</label>
</div>
<div class="accSettings_radioalign">
	<input '.$emoticonvalue2.' type="radio" name="changeGeneralSettings_emoticons" value="2" id="emoticons_2"><label for="emoticons_2"> Deaktiviert</label><br>
</div>';

$accSettingsContainer .= '	
<div class="changeInformation">	
<p>
Folgendes ist beim Ändern der Emoticons zu beachten :
</p>
	<ul>
		<li>
		Sie müssen mindestens eine Auswahl treffen.
		</li>
		<li>
		Das Deaktivieren führt zu einem Ausblenden der grafischen Emoticons.
		</li>
	</ul>
</div>	
</fieldset>


<fieldset>
<legend>
Foren Mauszeiger
</legend>';

$getactualCursor = $db->query("SELECT user_cursor FROM $db->table_accdata WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."'))") or die(mysql_error());
while($actualCursor = mysqli_fetch_object($getactualCursor)) {
	$cursor_settings = $actualCursor->user_cursor;
}

switch($cursor_settings) {
	case '0':
		$cursorvalue1 = 'checked="checked"';
		$cursorvalue2 = '';
		break;
	case 1:
		$cursorvalue1 = 'checked="checked"';
		$cursorvalue2 = '';
		break;
	case 2:
		$cursorvalue1 = '';
		$cursorvalue2 = 'checked="checked"';
		break;
	default:
		$cursorvalue1 = '';
		$cursorvalue2 = '';
		break;
}

$accSettingsContainer .= '
<div class="accSettings_radioalign">
	<input '.$cursorvalue1.' type="radio" name="changeGeneralSettings_cursor" value="1" id="forum_cursor_1">
	<label for="forum_cursor_1">
		 Aktiviert ( Standard )
	</label>
</div>
<div class="accSettings_radioalign">
	<input '.$cursorvalue2.' type="radio" name="changeGeneralSettings_cursor" value="2" id="forum_cursor_2">
	<label for="forum_cursor_2">
		 Deaktiviert
	</label>
</div>';


$accSettingsContainer .= '
<div class="changeInformation">	
<p>
Folgendes ist beim Ändern des Mauszeigers zu beachten :
</p>
	<ul>
		<li>
		Es besteht die Möglichkeit, dass Ihr Browser diese Funktion nicht unterstützt.
		</li>
	</ul>
</div>
</fieldset>


<fieldset>
<legend>
Automatische Benachrichtigungen
</legend>';

$getactualAjaxMsg = $db->query("SELECT ajax_msg FROM $db->table_accdata WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."'))") or die(mysql_error());
while($actualAjaxMsg = mysqli_fetch_object($getactualAjaxMsg)) {
	$ajaxMsg_settings = $actualAjaxMsg->ajax_msg;
}

switch($ajaxMsg_settings) {
	case '0':
		$ajaxMsg1 = 'checked="checked"';
		$ajaxMsg2 = '';
		break;
	case 1:
		$ajaxMsg1 = 'checked="checked"';
		$ajaxMsg2 = '';
		break;
	case 2:
		$ajaxMsg1 = '';
		$ajaxMsg2 = 'checked="checked"';
		break;
	default:
		$ajaxMsg1 = '';
		$ajaxMsg2 = '';
		break;
}

$accSettingsContainer .= '
<div class="accSettings_radioalign">
	<input '.$ajaxMsg1.' type="radio" name="changeGeneralSettings_ajaxMsg" value="1" id="subscription_notes_1">
	<label for="subscription_notes_1">
		Aktiviert ( Standard )
	</label>
</div>
<div class="accSettings_radioalign">
	<input '.$ajaxMsg2.' type="radio" name="changeGeneralSettings_ajaxMsg" value="2" id="subscription_notes_2">
	<label for="subscription_notes_2">
		Deaktiviert
	</label>
</div>';


$accSettingsContainer .= '
<p class="warning cc_warning">
	Das Benachrichtigungssystem funktioniert aktuell nicht wie erwartet. Es ist mit Problemen bei aktivem Status zu rechnen.
</p>
<div class="changeInformation">	
<p>
Folgendes ist beim Ändern der Benachrichtigungseinstellungen zu beachten :
</p>
	<ul>
		<li>
		Es besteht die Möglichkeit, dass Ihr Browser diese Funktion nicht unterstützt.
		</li>
		<li>
		Es entstehen höhere Verbindungsanforderungen.
		</li>
	</ul>
</div>
</fieldset>




<fieldset>
<legend>
Generelle Foreneinstellungen
</legend>';

$getActualTemplate = $db->query("SELECT design_template, language FROM $db->table_accdata WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."'))") or die(mysql_error());
while($actualTemplate = mysqli_fetch_object($getActualTemplate)) {
	$template = $actualTemplate->design_template;
	$lang 	  = $actualTemplate->language;
}

$val_max = 2;
$default_template = 2;

$accSettingsContainer .= '<div class="html_dropdown">Design: <select name="css_template" class="css_template_main">';

for($i = 1; $i <= $val_max; $i++) {

unset($default);
unset($selected);

switch($i) {
	case 1:
		$layout_name = 'Akasi Board';
	break;
	case 2:
		$layout_name = 'Bane of the Legion';
	break;
	default:
		$layout_name = 'Akasi Board';
	break;
}

if($i == $template) $selected = ' selected="selected" '; else $selected = '';
if($i == $default_template) $default = ' (Default)'; else $default = '';

	$accSettingsContainer .= '<option value="'.$i.'"'.$selected.'>'.$layout_name.''.$default.'</option>';
}

$accSettingsContainer .= '</select></div>';



$accSettingsContainer .= '<div class="html_dropdown">Sprache: <select name="lang_template" class="css_template_main">';

$val_max = 2;
$default_template = 1;

for($i = 1; $i <= $val_max; $i++) {

$default = '';
$selected = '';

switch($i) {
	case 1:
		$lang_name = 'Deutsch [ German ]';
	break;
	case 2:
		$lang_name = 'Englisch [ English / GB ]';
	break;
	default:
		$lang_name = 'Deutsch [ German ]';
	break;
}

if($i == $lang) $selected = ' selected="selected" ';
if($i == $default_template) $default = ' (Default)';

	$accSettingsContainer .= '<option value="'.$i.'"'.$selected.'>'.$lang_name.''.$default.'</option>';
	

}

$accSettingsContainer .= '</select></div>';


$accSettingsContainer .= '
<p class="warning cc_warning">
Das Sprachsystem befindet sich derzeit in Entwicklung. Daher können massive Probleme, bei der Darstellung, eine Folge der Sprachänderung sein.<br>Das gleiche gilt für die Auswahl eines anderen Designs.
</p>
<div class="changeInformation">	
<p>
Folgendes ist beim Ändern dieser Einstellungen zu beachten :
</p>
	<ul>
		<li>
		Sie müssen diese Seite unter Umständen Aktualisieren, damit die Änderungen sichtbar werden.
		</li>
		<li>
		Ein Ändern des Software Sprachpakets hat keinen Einfluss auf die von Usern verfassten Beiträge.
		</li>
	</ul>
</div>
</fieldset>



<input type="submit" name="submit" value="Senden" class="accSettingsSubmit displaySettingsSubmit">
</form>';

$accSettingsContainer .= '</font>
</div>
</div>
</div>';

if($success_status == false && !isset($_GET['action'])) {
	echo $accSettingsContainer;
}
?>