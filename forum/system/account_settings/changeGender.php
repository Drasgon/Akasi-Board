<?php
// Re initialize the DB
if (!isset($db) || $db == NULL)
{
	$db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
	$main = new Board($db, $connection);

$success_status = '';
$accSettingsContainer = '<div class="account_settingsContainer">
<div class="account_settingsContainerInner">
	<div class="tabTitle">
		<p>
			<div class="icons" id="gender_undefined"></div>
				Geschlecht ändern
		</p>
	</div>

<div class="account_settingsInner">
<fieldset>
	<legend>
		Geschlecht ändern
	</legend>
<form method="POST" action="'.$main->getURI().'&action=updateGender" class="update_gender">';

$getactualGender = $db->query("SELECT gender FROM $db->table_accdata WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."'))");
while($actualGender = mysqli_fetch_object($getactualGender)) {
	$gender = $actualGender->gender;
}

switch($gender) {
	case 1:
		$gendervalue1 = 'checked="checked"';
		$gendervalue2 = '';
		$gendervalue3 = '';
		break;
	case 2:
		$gendervalue1 = '';
		$gendervalue2 = 'checked="checked"';
		$gendervalue3 = '';
		break;
	case 3:
		$gendervalue1 = '';
		$gendervalue2 = '';
		$gendervalue3 = 'checked="checked"';
		break;
	default:
		$gendervalue1 = '';
		$gendervalue2 = '';
		$gendervalue3 = '';
		break;
}

$accSettingsContainer .= '
<input '.$gendervalue1.' type="radio" name="changeGender" id="changeGender_1" value="1"><label for="changeGender_1"><div class="icons" id="gender_undefined"></div> <i>Keine Angabe</i></label><br><br>
<input '.$gendervalue2.' type="radio" name="changeGender" id="changeGender_2" value="2"><label for="changeGender_2"><div class="icons" id="gender_female"></div> <i>Weiblich</i></label><br><br>
<input '.$gendervalue3.' type="radio" name="changeGender" id="changeGender_3" value="3"><label for="changeGender_3"><div class="icons" id="gender_male"></div> <i>Männlich</i></label><br><br>';


$accSettingsContainer .= '<input type="submit" name="submit" value="Senden" class="accSettingsSubmit">

        </form>
		
		<font class="sign_changeFailed">';

	if(isset($_GET['action']) && $_GET['action'] == 'updateGender') {
		$main->useFile('./system/controller/processors/acc_settings_gender.php');
			changeGender();
	}

$accSettingsContainer .= '</font>
</fieldset>
</div>
</div>
</div>';

if($success_status == false && !isset($_GET['action'])) {
echo $accSettingsContainer;
}
?>