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
	GALLERY CHANGE SAVER
*/

ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

include('../../classes/akb_mysqli.class.php');
include('../../classes/akb_main.class.php');

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


// Initialize the session
if (isset($_COOKIE['PHPSESSID']) && $_COOKIE['PHPSESSID'] != 'deleted')
{
	if ($_COOKIE['PHPSESSID'] == '0')
	{
		$_SESSION['STATUS'] = false;
		setcookie('PHPSESSID', '', time() - 3600);
		return;
	}
	else
	{
		session_start();
		$_SESSION['ID'] = mysqli_real_escape_string($connection, $_COOKIE['PHPSESSID']);
		$checkUserbyQuery = $db->query("SELECT sid, id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')");
		if (mysqli_num_rows($checkUserbyQuery) == 0 || mysqli_num_rows($checkUserbyQuery) > 1)
		{
			setcookie('PHPSESSID', '', time() - 3600);
			$_SESSION['STATUS'] = false;
		}
		else
		{
			$_SESSION['STATUS'] = true;
			require ('../security/permission_system.php');
			while($ownID = mysqli_fetch_object($checkUserbyQuery))
			{
				$actualUserID	=	$ownID->id;
				$ownColor		=	"#E2EEED";
				$style_string 	=	'style="background-color:'.$ownColor.'"';
			}

		}
	}
}
else
{
	$_SESSION['STATUS'] = false;
}


if(isset($_POST['galleryPage']) && isset($_POST['type']) && $_POST['type'] == 'change' && isset($_POST['changeType']) && isset($_POST['value']))
{
	$changeType = $_POST['changeType'];
	$value = mysqli_real_escape_string($connection, $_POST['value']);
	$imageId = mysqli_real_escape_string($connection, $_POST['galleryPage']);
	
	$userId   = $_SESSION['USERID'];
	
	$imgData = $gallery->imageData($imageId, "id");
	$imgOwner = $imgData['uploaded_by_id'];
	
	if($userId != $imgOwner)
	{
		echo "Zugriff verweigert!";
	}
	else
	{
		if($gallery->checkArrayKey($changeType, $value))
		{
			$db->query("UPDATE ".$db->table_gallery_data." SET ".$changeType."=".$value." WHERE id=".$imageId."");
		} else {
			echo 'false';
		}
	}
}

?>