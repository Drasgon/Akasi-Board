<?php
// Re initialize the DB
if (!isset($db) || $db == NULL)
{
	$db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
	$main = new Board($db, $connection);

$getInitial = $db->query("SELECT signature FROM $db->table_accdata WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."'))") or die(mysql_error());
while($initialContentQuery = mysqli_fetch_object($getInitial)) {
	$initialContent = $initialContentQuery->signature;
}

$success_status = false;
$accSettingsContainer = '<div class="account_settingsContainer">
<div class="account_settingsContainerInner">
<div class="tabTitle">
<p>
<div class="icons" id="messages"></div>
Signatur bearbeiten
</p>
</div>

<div class="account_settingsInner">
<fieldset>
<legend>
Signatur bearbeiten
</legend>
<form method="POST" action="'.$main->getURI().'&action=updateSignature" enctype="multipart/form-data">

<textarea type="hidden" id="signatureEditArea" name="signatureEditArea">'.$initialContent.'</textarea>
  <script type="text/javascript">
CKEDITOR.replace("signatureEditArea", { 
language: "de", 
enterMode : CKEDITOR.ENTER_BR
});
</script>
<input type="submit" name="submit" value="Senden" class="accSettingsSubmit">

        </form>
		
<div class="changeInformation">	
<p>
Folgendes ist bei der Modifikation der Signatur zu beachten:
</p>
	<ul>
		<li>
		Die maximale Wortanzahl beträgt 300 Wörter.
		</li>
		<li>
		Es sind jegliche Zeichen erlaubt.
		</li>
		<li>
		Sie müssen das Urherberrecht für eingefügte Bilder besitzen. Die Administration übernimmt keine Haftung für enstandene Schäden durch Zuwiderhandlung.
		</li>
	</ul>
</div>	

<font class="sign_changeFailed">';

	if(isset($_GET['action']) && $_GET['action'] == 'updateSignature') {
		$main->useFile('./system/controller/processors/acc_settings_signature.php');
			changeSignature();
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