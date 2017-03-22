<?php

global $langGlobal;

// Re initialize the DB and Runtime Class
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);
	
	
$location_sec = '';
$page_style = '';

$page = "default";
if(isset($_GET["page"]))
$page = $_GET["page"];

switch($page) {
	case 'Portal':
		$page = $langGlobal['portal'] ;
		$page_id = 'portalicon';
		$page_link = '?page=Portal';
		break;
	case 'Index':
		$page = $langGlobal['forum'] ;
		$page_id = 'forumicon';
		$page_link = '?page=Index';
		break;
	case 'Members':
		$page = $langGlobal['members'] ;
		$page_id = 'navMembers';
		$page_link = '?page=Members';
		break;
	case 'Gallery':
		$page = $langGlobal['gallery_string'] ;
		$page_id = 'navGallery';
		$page_link = '?page=Gallery';
		break;
	case 'Account':
		$page = $langGlobal['sPControlCenter'];
		$page_id = 'accountpanel';
		$page_link = '?page=Account&Tab=Edit';
		break;
	case 'Message':
		$page = $langGlobal['sPMessages'];
		$page_id = 'messages';
		$page_link = '?page=Message';
		break;
	case 'Notes':
		$page = 'Benachrichtigungen';
		$page_id = 'messages';
		$page_link = '?page=Notes';
		break;
	case 'tos':
		$page = 'Nutzungsbestimmungen';
		$page_id = 'termofuse';
		$page_link = '?page=tos';
		break;
	case 'contact':
		$page = 'Impressum';
		$page_id = 'contact';
		$page_link = '?page=contact';
		break;
	case 'PrivacyPolicy':
		$page = 'Datenschutzerklärung';
		$page_id = 'privatePolicy';
		$page_link = '?page=PrivacyPolicy';
		break;
	case 'Profile':
			if(isset($_GET['User'])) {
					$secureUser = mysqli_real_escape_string($GLOBALS['connection'], $_GET['User']);
					$getUser = $main->getUserdata($secureUser);
					
					if(!$getUser || $getUser['accepted'] == 0) {
						$page_link = '?page=Profile';
						$page_id = 'navMembers'; 
						$page = 'Profil eines Users';
					} else {
						$page_link = '?page=Profile&User='.$_GET['User'].'';
						$user = $getUser['name'];
						$avatar = $getUser['avatar'];
						
						$page = 'Profil von '.$user.'';
						$page_id = 'navMembers'; 
						$page_style = 'style="background-image: url('.$avatar.')"';
					}
				}
			if(isset($_GET['Tab']) && $_GET['Tab'] == 'Edit') { 
					$page = 'Profil bearbeiten';
					$page_id = 'profiledit';
					$page_link = '?page=Profile&Tab=Edit';
				}
			if(!(isset($_GET['Tab']) && $_GET['Tab'] == 'Edit') && !(isset($_GET['User'])))
			{
				$page_link = '?page=Profile';
				$page_id = 'navMembers'; 
				$page = 'Profil eines Users';
			}
		break;
	case 'Register':
		$page_id = 'register';
		if(isset($_GET['subRegister']) && $_GET['subRegister'] == 'registerForm') { 
					
					$page = 'Registrierung - Formular';
					$page_link = '?page=Register&subRegister=registerForm';
			} else {
					$page = 'Registrierung - Nutzungsbestimmungen';
					$page_link = '?page=Register';
			}
		break;
	case 'Admin':
		$page = 'Administration';
		$page_id = 'adminicon';
		$page_link = '?page=Admin';
		break;
		
	default: 
		$page = $langGlobal['portal'];
		$page_id = 'portalicon';
		$page_link = '?page=Portal';
		break;
}

 $location_start = '<a href="'.$page_link.'"><li><div class="icons" id="'.$page_id.'" '.$page_style.'></div><p>'.$page.'</p></li></a>';

if (isset($_GET["page"]) && $_GET["page"] == 'Index' && isset($_GET['boardview']) || isset($_GET['threadID'])) {

	if (isset($_GET['boardview'])) {
		
		$boardID = mysqli_real_escape_string($GLOBALS['connection'], $_GET['boardview']);
		
		$fetchBoard = $db->query("SELECT title FROM $db->table_boards WHERE id=('".$boardID."')");
		if ($board = mysqli_fetch_object($fetchBoard)) {
			$board_name = $board->title;
			
			if($main->checkBoardPermission($boardID, 1) == true)
				$location_start .= '<a href="?page=Index&boardview='.$_GET['boardview'].'"><li><div class="icons" id="forumicon"></div><p>'.$board_name.'</p></li></a>';
		}
	}
	if (isset($_GET['threadID'])) {
			$fetchBoard = $db->query("SELECT id, title FROM $db->table_boards WHERE id=(SELECT main_forum_id FROM $db->table_thread WHERE id=('".mysqli_real_escape_string($GLOBALS['connection'], $_GET['threadID'])."'))");
		if ($board = mysqli_fetch_object($fetchBoard)) {
			$boardID = $board->id;
			$board_name = $board->title;
			
			if($main->checkBoardPermission($boardID, 1) == true)
				$location_start .= '<a href="?page=Index&boardview='.$boardID.'"><li><div class="icons" id="forumicon"></div><p>'.$board_name.'</p></li></a>';
		}
		$fetchThread = $db->query("SELECT id,title,main_forum_id FROM $db->table_thread WHERE id=('".mysqli_real_escape_string($GLOBALS['connection'], $_GET['threadID'])."')");
		if ($thread = mysqli_fetch_object($fetchThread)) {
			$thread_id = $thread->id;
			$thread_name = $thread->title;
			$boardID = $thread->main_forum_id;
			$link = $main->buildThreadUrl($thread_id, $thread_name);
			
			if($main->checkBoardPermission($boardID, 1) == true)
				$location_start .= '<a href="'.$link.'"><li><div class="icons" id="threadicon"></div><p>'.$thread_name.'</p></li></a>';
		}
	}

}

if (isset($_GET['page']) && $_GET['page'] == 'Gallery' && isset($_GET['Image']))
{
	$getName = $db->query("SELECT img_display_name FROM $db->table_gallery_data WHERE id=('".mysqli_real_escape_string($GLOBALS['connection'], $_GET['Image'])."')");
	
	$name = mysqli_fetch_object($getName);
	$image = $name->img_display_name;
	$location_start .= '<a href="?page=Gallery&Image='.$_GET['Image'].'"><li><div class="icons" id="forumicon"></div><p>'.$image.'</p></li></a>';
}

$locationDisplay = '
	<ul class="locationCookies">
		'.$location_start.'
	</ul>
';

echo $locationDisplay;
?>