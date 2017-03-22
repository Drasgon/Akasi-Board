<?php
/*
Copyright (C) 2015  Alexander Bretzke

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);


$success_status = '';
$accSettingsContainer = '
<script defer src="./javascript/changeAvatar.js"></script>

<div class="account_settingsContainer">
<div class="account_settingsContainerInner">
<div class="tabTitle">
<p>
<div class="icons" id="avatar_default"></div>
Avatar ändern
</p>
</div>

<div class="account_settingsInner">
	<center>
		<fieldset>
		<legend>
		Avatar ändern
		</legend>
		<form method="post" action="'.$main->getURI().'&action=uploadAvatar" enctype="multipart/form-data" id="avatarForm" class="no-smoothstate">
			<div id="image-cropper">
			  <!-- The preview container is needed for background image to work -->
			  <div class="cropit-image-preview-container">
				<div class="cropit-image-preview"></div>
			  </div>
			  
			  <input type="range" class="cropit-image-zoom-input" />
			  
			  <input type="file" name="file" id="file" accept="image/*" onchange="initializeChange(this);" class="cropit-image-input">
			  <input type="hidden" name="image-data" class="hidden-image-data" />
			  <input type="submit" name="submit" value="Avatar hochladen" class="accSettingsSubmit avatarSubmit">
			</div>

				</form>';
			if(isset($_GET['action']) && $_GET['action'] == 'updateAvatarBorder') {
				$main->useFile('./system/controller/processors/acc_settings_avatar_border.php');
					changeAvatarBorder();
			}
			else
			if(isset($_GET['action']) && $_GET['action'] == 'uploadAvatar' && isset($_FILES["file"])) {
				$main->useFile('./system/classes/akb_simple_image.class.php');
				$main->useFile('./system/controller/processors/acc_settings_avatar.php');
					changeAvatar();
			} elseif(isset($_GET['action']) && $_GET['action'] == 'uploadAvatar' && !isset($_FILES["file"]))
			{
				
				$avatarUpdateError_fatal = 'Fehler beim Update des Avatars.<br>Eventuell wird das Dateiformat nicht unterstützt oder die hochgeladene Datei war fehlerhaft!';
					throwError_cc($avatarUpdateError_fatal);
			}
			if(isset($_GET['action']) && $_GET['action'] == 'deleteAvatar') {
				$main->useFile('./system/controller/processors/acc_settings_delavatar.php');
					deleteAvatar();
			}
			
			$data = $main->getUserData($_SESSION['ID'], 'sid');
			$avatar_border = $data['avatar_border'];
			$parts = explode(',', $avatar_border);
			$r = $parts[0];
			$g = $parts[1];
			$b = $parts[2];
			$a = $parts[3]*100;
			

		$accSettingsContainer .= '

		<h2>Rahmen ändern</h2>
		
		<form method="post" action="'.$main->getURI().'&action=updateAvatarBorder" enctype="multipart/form-data" style="position:relative; padding-bottom:50px;" onreset="changeBorder()">
				
				<p>
					<h3>Red:</h3>
					<br />
					<input type="range" min=0 max=255 value='.$r.' name="r" id="r" onchange="changeBorder()">
				</p>
				<p>
					<h3>Green:</h3>
					<br />
					<input type="range" min=0 max=255 value='.$g.' name="g" id="g" onchange="changeBorder()">
				</p>
				<p>
					<h3>Blue:</h3>
					<br />
					<input type="range" min=0 max=255 value='.$b.' name="b" id="b" onchange="changeBorder()">
				</p>
				<p>
					<h3>Alpha:</h3>
					<br />
					<input type="range" min=0 max=100 value='.$a.' name="a" id="a" onchange="changeBorder()">
				</p>
				
		
				<input type="submit" name="submit" value="Rahmen ändern" class="accSettingsSubmit avatarSubmit">
				<input type="reset" name="reset" value="Zurücksetzen" class="accSettingsSubmit avatarSubmit_reset">
		</form>

	</center>
		<h2>Ihr derzeitiger Avatar</h2><br />
		<br />
		<img src="'.$_SESSION['avatar'].'" style="border: 5px solid rgba('.$avatar_border.')" id="avatar_preview">
		<br />

				<form method="post" action="'.$main->getURI().'&action=deleteAvatar" enctype="multipart/form-data">
				<input type="submit" name="submit" value="Avatar löschen" class="accSettingsSubmit">
				</form>

<div class="changeInformation">	
<p>
Folgendes ist vor dem Hochladen eines Avatars zu beachten:
</p>
	<ul>
		<li>
		Das hochgeladene Bild muss eins der folgenden Formate besitzen: JPG / JPEG / PNG / BMP
		</li>
		<li>
		Die maximale Dateigröße beträgt 5MB
		</li>
		<li>
		Das zugeschnittene Bild wird nach dem Upload auf eine Größe von 225 x 225 Pixeln skaliert, um auch bei größerer Ansicht eine hohe Qualität zu erzielen.
		</li>
		<li>
		Durch den Upload stimmen Sie unseren Richtlinien zu, dass wir keine Haftung für die hochgeladene Grafik übernehmen.
		</li>
		<li>
		Sie müssen das Urheberrecht für das Bild besitzen, bzw. der Urheber sein. Die Administration übernimmt keine Haftung für enstandene Schäden durch Zuwiderhandlung.
		</li>
	</ul>
</div>	

</fieldset>

</div>
</div>
</div>';

if($success_status == false && !isset($_GET['action'])) {
echo $accSettingsContainer;
}
?>