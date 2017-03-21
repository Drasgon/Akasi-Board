<?php
function changeAvatar() {

	$avatarUpdateError_fatal = '';

	if (!isset($db) || $db == NULL)
	{
		$db = NEW Database();
		$connection = $db->mysqli_db_connect();
	}
	if (!isset($main) || $main == NULL)
		$main = new Board($db, $connection);

		
		
	// SETUP
		$saveto = './images/avatars/';
	// SETUP END
	
		
	// Base64 HANDLER START
		
		$imgContents = $_POST['image-data'];
			
		$randomName = md5($_POST['image-data'] . ':' . time());
		list($type, $imgContents) = explode(';', $imgContents);
		list(, $imgContents)      = explode(',', $imgContents);
		$imgContents = base64_decode($imgContents);
		$imgSavepath = $main->getRoot().$saveto.'tmp/';
		$imgNewFilename = $randomName.'.png';
		$imgSavefilepath = $imgSavepath.$imgNewFilename;
		
		if(!file_exists($imgSavepath))
			mkdir($imgSavepath);
		
		$saveImage = file_put_contents($imgSavefilepath, $imgContents);
		if($saveImage)
		{
            $image_split = explode('.', $imgNewFilename);
            $image_base  = $image_split[0];
            $image_ext   = $image_split[1];
			
			$image_size = filesize($imgSavefilepath);
		}
	
	// Base64 HANDLER END


if ((($image_ext == "gif")
|| ($image_ext == "jpeg")
|| ($image_ext == "png")
|| ($image_ext == "pjpeg"))
&& ($image_size < 5000000))
{


	if(isset($image_ext)) {
	  switch($image_ext)
		{
			case "gif": $src = imagecreatefromgif($imgSavefilepath); imagealphablending($src, true); break;
			case "jpeg": // Both regular and progressive jpegs
			case "pjpeg": $src = imagecreatefromjpeg($imgSavefilepath); break;
			case "png": $src = imagecreatefrompng($imgSavefilepath); imagealphablending($src, true); break;
		}
	}
	
	$filename  = basename($imgSavefilepath);
	$extension = pathinfo($imgSavefilepath, PATHINFO_EXTENSION);
		
	$image = new SimpleImage();

	$image->load($imgSavefilepath);

	$imageX = $image->getWidth();
	$imageY = $image->getHeight();

	if($imageY > '150' || $imageX > '150' && $_FILES['image']['type'] != 'image/gif') {
		$image->resizeToWidth(150, $extension);
	}

	$newFilename       = md5($filename).'.'.$extension;
	$newName = mysqli_real_escape_string($GLOBALS['connection'], 'avatar-' . $newFilename);

	$increment = '0';

	while(file_exists($saveto . $newName)) {

	$increment++;

	$newFilename       = md5($filename.$increment).'.'.$extension;
	}
	
	$newName = mysqli_real_escape_string($GLOBALS['connection'], 'avatar-' . $newFilename);

	$image->save($saveto . $newName, $extension);
	$oldAvatar = $db->query("SELECT avatar FROM $db->table_accdata WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."'))");
	while($oldAvatar_fetch = mysqli_fetch_object($oldAvatar)) {
		$oldAvatar_path = $oldAvatar_fetch->avatar;
	}

	if($oldAvatar_path != './images/avatars/default.jpg' && $oldAvatar_path != './images/avatars/default.png' && file_exists($oldAvatar_path)) {
		unlink($oldAvatar_path);
	}

	unlink($imgSavefilepath);

	$updateAvatar = $db->query("UPDATE $db->table_accdata SET avatar=('".$saveto . $newName."') WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."'))");

	if(!$updateAvatar) {
	$avatarUpdateError_fatal = 'Krititscher Fehler beim Update des Avatars.';
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
		$avatarUpdateError_fatal = 'Fehler beim Update des Avatars.<br>Eventuell wird das Dateiformat nicht unterstützt oder die hochgeladene Datei war fehlerhaft!';
			throwError_cc($avatarUpdateError_fatal);
		return;
}
}
?>