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
	GALLERY LOADER
*/

ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

global $langGlobal;

include('../../classes/akb_mysqli.class.php');
include('../../classes/akb_main.class.php');

if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);

if (!isset($gallery) || $gallery == NULL)
{
	$main->useFile('./system/classes/akb_gallery.class.php', 1);
	$gallery = new Gallery($db, $connection, $main);
}

// Initialize the session
$main->useFile('./system/auth/auth.php');

// Use language specific lang file.
$main->useFile('./system/controller/processors/lang_processor.php', 1);

if(isset($_POST['galleryPage']) && !empty($_POST['galleryPage']))
{
	$images = '';
	
	$results_per_page = '25';
	$results_per_page = $results_per_page + '1';
	
	if(isset($_POST['galleryPage']) && !empty($_POST['galleryPage']))
	{
		$limit_start	=	$_POST['galleryPage'] * $results_per_page	-	$results_per_page;
	} else
	{
		$limit_start	=	'0';
	}

	
	$imageQuery = $db->query("SELECT gallery.id, gallery.img_name, gallery.img_display_name, gallery.img_description, gallery.uploaded_by_id, accdata.username FROM $db->table_gallery_data gallery, $db->table_accdata accdata WHERE gallery.id AND accdata.account_id=gallery.uploaded_by_id ORDER BY gallery.id DESC LIMIT ".mysqli_real_escape_string($GLOBALS['connection'], $limit_start).", ".mysqli_real_escape_string($GLOBALS['connection'], $results_per_page)."") or die(mysqli_error($GLOBALS['connection']));
	while($processImages = mysqli_fetch_object($imageQuery))
	{
	
		$image_id				=	$processImages->id;
		$image_name				=	$processImages->img_name;
		$image_display_name		=	$processImages->img_display_name;
		$image_description		=	$processImages->img_description;
		$image_uploader_id		=	$processImages->uploaded_by_id;
		$image_uploader_name	=	$processImages->username;
		
		
			$actualUser = $main->getUsername($image_uploader_id);
			$imagePath  = $gallery->getUserDir($actualUser);
			
			$imageParts = explode("-", $image_name);
			$image_base = $imageParts[1];
			
			$image_name = "thumb-".$image_base;
			
			$image_split = explode('.', $imagePath . $image_name);
            $image_name  = $image_split[1];
            $image_ext   = $image_split[2];
			
			$img_src = $gallery->validateImage(".".$image_name . '.' . $image_ext);
	
			$images .= '
				<div class="galleryRow" id="'.$image_id.'">
					<a href="?page=Gallery&Image='.$image_id.'" title="'.$image_description.'">
					<img src="' . $img_src . '">
					<p>
						<a href="?page=Gallery&Image='.$image_id.'">
							'.$image_display_name.'
						</a>
					</p>
					<p>';
						$images .= $langGlobal['gallery_uploaded_by'];
						$images .= '
						<a href="?page=Profile&User='.$image_uploader_id.'">
							'.$image_uploader_name.'
						</a>
					</p>
					</a>
				</div>';
	}
	
	if(!mysqli_num_rows($imageQuery) >= 1)
			$images .= "Diese Seite ist leer!";

	echo $images;
	
}

?>