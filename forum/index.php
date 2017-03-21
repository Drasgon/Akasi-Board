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

// Start output buffering for working session cookies
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
    ob_start("ob_gzhandler");
else
    ob_start();
	
	ini_set('display_startup_errors',1);
	ini_set('display_errors',1);
	error_reporting(-1);
	
	$totalGenerateTime = microtime(TRUE);
	
	
	
// Use maintenance mode
include('system/security/maintenance_mode.php');

// Include MySQL Data
include('system/classes/akb_mysqli.class.php');
include('system/classes/akb_main.class.php');

echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="de">
  <head prefix="og: http://ogp.me/ns#">
    <meta name=Content-Type content="text/html; charset=utf-8">
    <meta http-equiv=content-type content="text/html; charset=utf-8">
    <meta name=expires content=0>
    <meta name=viewport content="width=device-width, initial-scale=1">
    <meta http-equiv=Content-Language content="at, de, ch">
    <meta name=language content="deutsch, de, at, ch">
	<meta property="og:image:type" content="image/png">
	<meta property="og:image:width" content="200">
	<meta property="og:image:height" content="200">
	<meta property="og:site_name" content="Dignum-Aliorum"/>';
	
$totalQueries = 0;
$totalQueryTime = 0;
$globalLink;

// Build MySqli Connection
$db         = new Database();
$connection = $db->mysqli_db_connect();

$main = new Board($db, $connection);


//	Use control functions for logout etc. and build session.
$main->useFile('./system/controller/main/initializer.php');

// Use language specific lang file.
$main->useFile('./system/controller/processors/lang_processor.php', 1);

// Include dynamic title attribute system
$main->useFile('./system/controller/main/page_title.php');

// Include dynamic open-graph attribute system
$main->useFile('./system/controller/og/og_main.php');

// Include user specific CSS
$main->useFile('./system/controller/processors/css_processor.php');

if(isset($_GET['page']) && $_GET['page'] == 'contact')
	echo '<meta name="robots" content="noindex, nofollow" />';

echo '
  <link rel="SHORTCUT ICON" href="favicon.ico" type="image/x-icon">
  <!--[if lt IE 9]><script src="https://code.jquery.com/jquery-1.11.1.min.js"></script><![endif]-->
  <script src="./javascript/jquery-2.1.1.min.js"></script>
  <script src="./javascript/jquery-ui.js"></script>
  <script defer src="./javascript/jquery.nicescroll.min.js"></script>
  <script defer src="./javascript/ajax.js"></script>
  <script defer src="./javascript/parallax.js"></script>
  <script defer src="./javascript/jquery.cookie.js"></script>
  <script defer src="./javascript/ion.sound.js"></script>
  <script defer src="./javascript/frameControl.js"></script>
  <script src="./system/ckeditor/ckeditor.js"></script>
  <script src="./javascript/DragPosition.js"></script>
  
  </head>';


echo '<body id="body">
					<div class="background" id="background">
						<video autoplay="" loop="" poster="img/bg/sv-ol.jpg">
						  <source src="../img/bg/video/sv-ol.mp4" type="video/mp4">
						  <source src="../img/bg/video/sv-ol.webm" type="video/webm">
								Your browser does not support the video tag.
						</video>
					</div>';

// Use system for checking URL vality. [Disabled due to get key issues. Activated again, when fixed]
#$main->useFile('./system/controller/security/getParam_validate.php');
	// Execute URL check.
#	ValidateGet();

// Prepare error pages
require_once('./system/interface/errorpage.php');

// Include Preloader for loading all images at once, to reduce later loading times.
require('./system/interface/preload.php');

// Get client URL for later processing purposes.
$actualURL = $main->getURI();


/*######
#		|- Exclusive functions START -|
#		
#		If client is a valid and logged in user, 
#		show logout button(and frame as well) and set state of the Ajax messaging system.
*/ ######
if ((isset($_SESSION['angemeldet']) || $_SESSION['angemeldet'] = true) && isset($_SESSION['ID']))
{
    require('./system/interface/logoutFrame.php');
    
    // Check if user has set the messaging system on.
    $getactualAjaxSetting = $db->query("SELECT ajax_msg FROM $db->table_accdata WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))");
    while ($actualAjaxMsg = mysqli_fetch_object($getactualAjaxSetting)) {
        $ajaxMsg_settings = $actualAjaxMsg->ajax_msg;
    }
    
    // If state is set to 0 or 1, enable the Ajax messaging system -- Values: 0, 1 = Enabled, 2 = Disabled.
    if (isset($ajaxMsg_settings) && ($ajaxMsg_settings == '1' || $ajaxMsg_settings == '0')) {
        echo '<script src="./javascript/desktopNotification.js"></script><script defer src="./javascript/ajaxMsg.js"></script>';
    }
    
    /*######
    #		|- Custom cursor -|
    #		Set the state of the custom cursor.
    */ ######		
    
    // Check if user has set the cursor on.
	if(isset($_SESSION['angemeldet']) && $_SESSION['angemeldet'] == TRUE && !isset($_SESSION['cursor']))
	{
		$getactualCursor = $db->query("SELECT user_cursor FROM $db->table_accdata WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))") or die(mysqli_error($GLOBALS['connection']));
		
		while ($actualCursor = mysqli_fetch_object($getactualCursor)) {
			$_SESSION['cursor'] = $actualCursor->user_cursor;
		}
	}
    
    // 	If state is set to 1, enable cursor -- Values: 1 = Enabled, 2 = Disabled.
    if (isset($_SESSION['angemeldet']) && $_SESSION['angemeldet'] == TRUE && isset($_SESSION['cursor']) && $_SESSION['cursor'] == '1')
        // Inline HTML output to activate the cursor
        echo '<style>* { cursor: url(images/cursor/cursor_scaled.png), move;}</style>';

}
/*######
#		|- Exclusive functions END -|
*/ ######


	// Include the page processor, which processes all the dynamic content.
	$main->useFile('./system/controller/processors/page_processor.php');


/*######
#		|- Inline Ajax-handler START -|
#		This handler controls the state of the forum categories.
*/ ######
		// If received an valid ajax request.
		if (isset($_GET['ajaxSend']) && $_GET['ajaxSend'] == 'saveStatus' && isset($_POST['boardCat_row'])) {
			
			$main->useFile('./system/controller/processors/board_category_processor.php');
		}
/*######
#		Inline Ajax-handler END
*/ ######

	echo '</body></html>';

	// End of the output buffering. Also the end of all content. Cookies can't be set after this.
	ob_end_flush();
?>