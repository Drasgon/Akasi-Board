<?php
$appname = 'Bane of the Legion';

$page = "default";
if(isset($_GET['page']))
$page = $_GET['page'];

switch($page)
{
	case 'home':
		$location = 'Home';
	break;
	
	case 'members':
		$location = 'Mitglieder';
	break;
	
	case 'media':
		$location = 'Media';
	break;
	
	case 'aboutus':
		$location = "Über Uns";
	break;
			
	case 'contact':
		$location = "Impressum";
	break;
	
	default:
		$location = $appname;
	break;
			
}

if($location != $appname)
	$location = $location.' - '.$appname;

$titledisplay = '
	<title>'.$location.'</title>
';

echo $titledisplay;
?>