<?php
$db = new Database();
	$connection = $db->mysqli_db_connect();

if(!isset($main) || $main == NULL) $main = new Board($db, $connection);

if (!isset($gallery) || $gallery == NULL)
{
	$main->useFile('./system/classes/akb_gallery.class.php', 1);
	$gallery = new Gallery($db, $connection);
}

$appName = 'Bane of the Legion';
$type = 'Forum';

$page = "default";
if(isset($_GET["page"]))
$page = $_GET["page"];

$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$errorString = 'Fehler';
$errorDesc = 'Oh nein! Ein Fatal Schinken ist aufgetreten! Versuchen Sie es später erneut, oder lassen sie es gleich!';

switch($page) {
	case 'Portal':
			$title = 'Portal';
		break;
		
	case 'Index':
			$title = 'Forum';
		break;
		
	case 'Members':
			$title = 'Mitglieder';
		break;
	case 'Gallery':
			if(!isset($_GET['Image']))
			{
				$title = 'Galerie';
			} else {
				$image = mysqli_real_escape_string($GLOBALS['connection'], $_GET['Image']);
				$getImageName = $db->query("SELECT img_name,img_display_name, img_description, uploaded_by_id FROM $db->table_gallery_data WHERE id=('".$image."')");
				while($images = mysqli_fetch_object($getImageName))
				{
				$imgName = $images->img_display_name;
				$imgSrc  = $images->img_name;
				$description = $images->img_description;
				$uploaderId = $images->uploaded_by_id;
				}
				
				// Split filename to 2 separate parts. Name and extension
					$actualUser = $main->getUsername($uploaderId);
					$imagePath  = $gallery->getUserDir($actualUser);
					
					$imageParts = explode("-", $imgSrc);
					$image_base = $imageParts[1];
					
					$imgSrc = $imagePath."thumb-".$image_base;
					$imgSrc = substr($imgSrc, 1);
				
				$title = $imgName;
			}
		break;
		
	case 'Account':
			$title = 'Kontrollzentrum';
		break;
		
	case 'Message':
			$title = 'Nachrichten';
		break;
		
	case 'Notes':
			$title = 'Benachrichtigungen';
		break;
		
	case 'tos':
			$title = 'Nutzungsbestimmungen';
		break;
		
	case 'contact':
			$title = 'Impressum';
		break;
		
	case 'PrivacyPolicy':
			$title = 'Datenschutzerklärung';
		break;
		
	case 'Profile':
		if(isset($_GET['User'])) { 
			$secureUser = mysqli_real_escape_string($GLOBALS['connection'], $_GET['User']);
			$getUser = $main->getUserdata($secureUser);
			
			if($getUser) {
				$page_link = '/?page=Profile&User='.$secureUser.'';
				$title = $getUser["name"];
				$imgSrc = substr($getUser["avatar"], 2);
				
				$getProfile = $db->query("SELECT about FROM $db->table_profile WHERE id=('".$secureUser."') LIMIT 1");
				
					$profile = mysqli_fetch_object($getProfile);
					if($profile)
						$description = strip_tags($profile->about);
			}
			
		}
	if(isset($_GET['Tab']) && $_GET['Tab'] == 'Edit') { 
			$title = 'Profil bearbeiten';
		}
		break;
		
	case 'Register':
		$page_img = './images/3Dart/register.png';
			if(isset($_GET['subRegister']) && $_GET['subRegister'] == 'registerForm') { 
				$title = 'Registrierung';
			} else {
				$title = 'Registrierung - Nutzungsbestimmungen';
			}
		break;
		
	case 'Admin':
			$title = 'Administration';
		break;
		
	default: 
			$title = 'Portal';
		break;
}

if (isset($page) && $page == "Index") {

	if (isset($_GET['boardview'])) {
		$fetchBoard = $db->query("SELECT title FROM $db->table_boards WHERE id=('".mysqli_real_escape_string($GLOBALS['connection'], $_GET['boardview'])."')");
		if($board = mysqli_fetch_object($fetchBoard))
			$title = $board->title;
		else
			$title = $errorString;
	}
			
	if (isset($_GET['threadID'])) {
		$fetchThread = $db->query("SELECT a.title, b.text FROM $db->table_thread a,$db->table_thread_posts b WHERE a.id=('".mysqli_real_escape_string($GLOBALS['connection'], $_GET['threadID'])."') AND b.thread_id=('".mysqli_real_escape_string($GLOBALS['connection'], $_GET['threadID'])."')");
		if($thread = mysqli_fetch_object($fetchThread)) {
			$title = $thread->title;
			$description = $main->closetags($thread->text);
		}
		else
		{
			$title = $errorString;
			$description = $errorDesc;
		}
	}

}


$og_props = '
	<meta property="og:title" content="'.$title.'" />
	<meta property="og:url" content="'.$url.'" />
';
if(isset($imgSrc))
{

$imgSrc = 'http://baneofthelegion.de/forum/'.$imgSrc;

$og_props .= '
	<meta property="og:image" content="'.$imgSrc.'" />
';
}
else
$og_props .= '
	<meta property="og:image" content="http://baneofthelegion.de/forum/favicon.ico">
';

if(isset($description))
{
$target = strlen($description);
							if($target>100) {
							if(($newtarget = strpos($description, ' ', 100)) !== false ) {
							$target = $newtarget;
							} else {
							$target = 100;
							 }
							}
							$description = substr($description, 0,$target);
							$description = $main->closetags($description);
							
$og_props .= '<meta property="og:description" content=\''.$description.'\' />';
} else {
$og_props .= '<meta property="og:description" content="Das Akasi Board ist eine neuartige Forensoftware, welche die neuesten Web Technologien verwendet, um ihnen ein einzigartiges Surf-Erlebnis zu bieten."/>';
}

echo $og_props;
?>