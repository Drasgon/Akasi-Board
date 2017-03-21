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

/*
	Process the uploaded image
*/

if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);

$main->useFile('./system/classes/akb_gallery.class.php', 1);
if (!isset($gallery) || $gallery == NULL)
	$gallery = new Gallery($db, $connection);
	
	
	


if(isset($_GET['page']) && $_GET['page'] == 'Gallery' && isset($_GET['action']) && $_GET['action'] != 'submitImageForm')
{

	//Use SimpleImage Class
	$main->useFile('./system/classes/akb_simple_image.class.php');

	// Set max file size ( in Bytes )
	$megabyte		=	1048576;
	$maxSizeTotal	=	$megabyte*5;

	// Accepted extensions
	$file_exts = array("jpg", "bmp", "jpeg", "gif", "png");
	$upload_exts = explode(".", $_FILES["image"]["name"]);
	$upload_exts = end($upload_exts);

	// If everything went correct, show next part of the upload form
	if ((($_FILES["image"]["type"] == "image/gif")
	|| ($_FILES["image"]["type"] == "image/jpeg")
	|| ($_FILES["image"]["type"] == "image/png")
	|| ($_FILES["image"]["type"] == "image/x-png")
	|| ($_FILES["image"]["type"] == "image/pjpeg"))
	&& ($_FILES["image"]["size"] < $maxSizeTotal)
	&& in_array($upload_exts, $file_exts))
	{

	$userData = $main->getUserdata($_SESSION['ID'], 'sid');
	$username = $userData['name'];
	$account_id	  = $userData['account_id'];

	$saveto = $gallery->tempDirectory;
	$savetoPublic = $gallery->publicDirectory;

	if(!$main->checkImage($gallery->userBase))
	{
		mkdir($gallery->userBase, 0777);
		
	if (!$main->checkImage($saveto)) 
		mkdir($saveto, 0777);

	if(!$main->checkImage($savetoPublic))
		mkdir($savetoPublic, 0777);
	}

	move_uploaded_file($_FILES["image"]["tmp_name"], $saveto . $_FILES["image"]["name"]);
	$image = new SimpleImage();
	$image->load($saveto . $_FILES["image"]["name"]);


	$filename  = basename($_FILES['image']['name']);
	$extension = pathinfo($saveto . $filename, PATHINFO_EXTENSION);
	$newFilename       = md5($filename).'.'.$extension;
	$newName = mysqli_real_escape_string($GLOBALS['connection'], 'img-' . $newFilename);

	$multiplier = time();

	// Check directory for availability of file name
	while($main->checkImage($saveto . $newName))
	{

		$multiplier .= rand(1, 256);

		$newFilename       = md5($filename.$multiplier).'.'.$extension;
		$newName = mysqli_real_escape_string($GLOBALS['connection'], 'img-' . $newFilename);

	}
	
	$checkDb = $db->query("SELECT img_name FROM $db->table_gallery_directory WHERE img_name='".$newName."'");
	
	// Check database for availability of file name
	while(mysqli_fetch_object($checkDb))
	{
		
		$multiplier .= rand(1, 256);

		$newFilename       = md5($filename.$multiplier).'.'.$extension;
		$newName = mysqli_real_escape_string($GLOBALS['connection'], 'img-' . $newFilename);
		
	}

	

	// Reserve image ID and add it to the temp table
	$maxData = $db->query("SELECT MAX(id) AS maxData FROM $db->table_gallery_data");
	$maxData = mysqli_fetch_object($maxData);
	$maxData = $maxData->maxData;
	$maxData++;
	$maxDir = $db->query("SELECT MAX(id) AS maxDir FROM $db->table_gallery_directory");
	$maxDir = mysqli_fetch_object($maxDir);
	$maxDir = $maxDir->maxDir;
	$maxDir++;

			$imageId = max($maxDir, $maxData);

	
	$addToDb = $db->query("INSERT INTO $db->table_gallery_directory (id, img_name, uploader_id) VALUES ('".$imageId."', '".$newName."', '".$account_id."')");

	$image->save($saveto . $newName, $extension);
	
	$newThumbnailName = mysqli_real_escape_string($GLOBALS['connection'], 'thumb-' . $newFilename);
	
		$imageX = $image->getWidth();
		$imageY = $image->getHeight();
		
		$ratio = $imageX / $imageY;
		$imageYNew = $gallery->thumbTargetY;
		$imageXNew = $imageYNew * $ratio;
	
	$image->resize($imageXNew, $imageYNew);
	$image->save($saveto . $newThumbnailName, $extension, 65);
	
	$addToDb = $db->query("INSERT INTO $db->table_gallery_thumb (id, img_name) VALUES ('".$imageId."', '".$newThumbnailName."')");
	
		unlink($saveto . $_FILES["image"]["name"]);


	$gallery_body = '';
	$gallery_body .= '
	<script type="text/javascript"> 

	function stopRKey(evt) { 
	  var evt = (evt) ? evt : ((event) ? event : null); 
	  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null); 
	  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;} 
	} 

	document.onkeypress = stopRKey; 

	</script>	
	<div class="galleryBody">
		<div class="galleryUploadPreview">
			<p>Vorschaugrafik. Endgröße bleibt erhalten ('.$imageX.' x '.$imageY.')</p>
			<img src="'.$saveto.$newThumbnailName.'" class="galleryUploadPreviewImage">
		</div>
		
		<div class="uploadForm_process">
			<form method="POST" action="?page=Gallery&action=submitImageForm">
				<div class="galleryUpload-formRow">
					<p class="galleryUpload-formRowText">Titel</p>
					<input type="text" placeholder="Titel" name="image_title" class="image_title">
				</div>
				<div class="galleryUpload-formRow">
					<p class="galleryUpload-formRowText">Beschreibung (Optional)</p>
					<textarea placeholder="Beschreibung" name="image_desc" class="image_desc" id="image_upload_desc"></textarea>
				</div>
				<div class="galleryUpload-formRow-select">
					<p class="galleryUpload-formRowText">Thema</p>
					<label>
					<select name="image_theme">';
						$themes = $gallery->getOption('theme');
						
						$i = 0;
						foreach($themes as $themeIndex)
						{
							$gallery_body .= '<option value="'.$i.'" >'.$themeIndex.'</option>';
							$i++;
						}
		$gallery_body .= '
					</select>
					</label>
				</div>
				<div class="galleryUpload-formRow-select">
					<p class="galleryUpload-formRowText">Bewertung</p>
					<label>
					<select name="image_rating">';
						$rating = $gallery->getOption('rating');
						
						$i = 0;
						foreach($rating as $ratingIndex)
						{
							$gallery_body .= '<option value="'.$i.'" >'.$ratingIndex.'</option>';
							$i++;
						}
		$gallery_body .='
					</select>
					</label>
				</div>
				<div class="galleryUpload-formRow-select">
					<p class="galleryUpload-formRowText">Kategorie</p>
					<label>
					<select name="image_category">';
						$category = $gallery->getOption('category');
						
						$i = 0;
						foreach($category as $categoryIndex)
						{
							$gallery_body .= '<option value="'.$i.'" >'.$categoryIndex.'</option>';
							$i++;
						}
		$gallery_body .='
					</select>			
					</label>
				</div>
			<br>
				<input type="submit">
			</form>
		</div>
	</div>
	';
	
	echo $gallery_body;
		
	} else {

		echo 'Unable to process the uploaded image! <br> File size: ' . $_FILES["image"]["size"] .' <br> Image name: '. $_FILES["image"]["name"];
		
	}
} else {

	if(isset($_GET['page']) && $_GET['page'] == 'Gallery' && isset($_GET['action']) && $_GET['action'] == 'submitImageForm')
	$main->useFile('./system/controller/processors/gallery_upload_processor_finalize.php');
	
}
?>