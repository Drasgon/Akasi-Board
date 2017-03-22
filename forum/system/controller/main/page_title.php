<?php
// Re initialize the DB and Runtime Class
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);

$appName = 'Bane of the Legion';

$page = "default";
if(isset($_GET["page"]))
$page = $_GET["page"];

$errorString = 'Fehler';

switch($page) {
	case 'Portal':
			$location = 'Portal';
		break;
		
	case 'Index':
			$location = 'Forum';
		break;
		
	case 'Members':
			$location = 'Mitglieder';
		break;
	case 'Gallery':
			if(!isset($_GET['Image']))
			{
				$location = 'Galerie';
			} else {
				$image = mysqli_real_escape_string($GLOBALS['connection'], $_GET['Image']);
				$getImageName = $db->query("SELECT img_display_name FROM $db->table_gallery_data WHERE id=('".$image."')");
				
				$imgName = mysqli_fetch_object($getImageName)->img_display_name;
				
				$location = $imgName;
			}
		break;
		
	case 'Account':
			$location = 'Kontrollzentrum';
		break;
		
	case 'Message':
			$location = 'Nachrichten';
		break;
		
	case 'Notes':
			$location = 'Benachrichtigungen';
		break;
		
	case 'tos':
			$location = 'Nutzungsbestimmungen';
		break;
		
	case 'contact':
			$location = 'Impressum';
		break;
		
	case 'PrivacyPolicy':
			$location = 'Datenschutzerklärung';
		break;
		
	case 'Profile':
		if(isset($_GET['User'])) { 
			$secureUser = mysqli_real_escape_string($GLOBALS['connection'], $_GET['User']);
			$getUser = $main->getUserdata($secureUser);
			
			if(!$getUser || $getUser['accepted'] == 0) {
				$location = 'Profil eines Users';
			}
			else
			{
				$location = $getUser['name'];
			}
			
		}
	if(isset($_GET['Tab']) && $_GET['Tab'] == 'Edit') { 
			$location = 'Profil bearbeiten';
		}
		break;
		
	case 'Register':
		$page_img = './images/3Dart/register.png';
			if(isset($_GET['subRegister']) && $_GET['subRegister'] == 'registerForm') { 
				$location = 'Registrierung';
			} else {
				$location = 'Registrierung - Nutzungsbestimmungen';
			}
		break;
		
	case 'Admin':
			$location = 'Administration';
		break;
		
	default: 
			$location = 'Portal';
		break;
}

if (isset($page) && $page == "Index") {

	if (isset($_GET['boardview'])) {
		
		$boardID = mysqli_real_escape_string($GLOBALS['connection'], $_GET['boardview']);
		
		$fetchBoard = $db->query("SELECT title FROM $db->table_boards WHERE id=('".$boardID."')");
		if($board = mysqli_fetch_object($fetchBoard))
		{
			if($main->checkBoardPermission($boardID, 1) == false)
				$location = 'Error';
			else
				$location = $board->title;
			
		}
		else
			$location = $errorString;
	}
			
	if (isset($_GET['threadID'])) {
		
		$threadID = mysqli_real_escape_string($GLOBALS['connection'], $_GET['threadID']);
		
		$fetchThread = $db->query("SELECT title, main_forum_id FROM $db->table_thread WHERE id=('".$threadID."')");
		if($thread = mysqli_fetch_object($fetchThread))
		{
			$boardID = $thread->main_forum_id;
			
			if($main->checkBoardPermission($boardID, 1) == false)
				$location = 'Error';
			else
				$location = $thread->title;
			
		}
		else
			$location = $errorString;
	}

}


if(!isset($location) || (isset($location) && empty($location)))
	$location = $errorString;

$titleDisplay = '
	<title>'.$location.' - '.$appName.'</title>
';

echo $titleDisplay;
?>