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
MESSAGE HANDLE SYSTEM
*/

ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

require('../../classes/akb_mysqli.class.php');
require('../../classes/akb_main.class.php');

// Re initialize the DB and Runtime Class
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);


$main->useFile('./system/controller/processors/icon_parser_processor.php');

// Initialize the session
if (isset($_COOKIE['PHPSESSID']) && $_COOKIE['PHPSESSID'] != 'deleted')
{
	if ($_COOKIE['PHPSESSID'] == '0')
	{
		$_SESSION['angemeldet'] = false;
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
			$_SESSION['angemeldet'] = false;
		}
		else
		{
			$_SESSION['angemeldet'] = true;
			$main->useFile('./system/controller/security/permission_system.php');
			while($ownID = mysqli_fetch_object($checkUserbyQuery))
			{
				$actualUserID	=	$ownID->id;
				$style_string 	=	'msgOwner';
			}

		}
	}
}
else
{
	$_SESSION['angemeldet'] = false;
}

$main->useFile('./system/classes/akb_chat.class.php');
$chat = new Chat($db, $connection, $main);

if (isset($_SESSION['angemeldet']) && $_SESSION['angemeldet'] == true)
{
	setlocale(LC_ALL, null);
	setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu');

	/*
	MESSAGE SEND HANDLER BEGIN
	*/
	if (isset($_GET['ajaxSend']) && $_GET['ajaxSend'] == 'chatMessage' && isset($_POST['chatMsgContent']) && !empty($_POST['chatMsgContent']))
	{
		$chat->RegisterPublicMessage($_POST['chatMsgContent']);
	}
	/*
	MESSAGE SEND HANDLER END
	*/

	/*
	MESSAGE PULL HANDLER BEGIN
	*/
	if (isset($_GET['ajaxSend']) && $_GET['ajaxSend'] == 'chatMessagePull' && isset($_POST['latestID']) && !empty($_POST['latestID']))
	{
		$newChatRow = '';
		$latestID = mysqli_real_escape_string($connection, $_POST['latestID']);
		if ($latestID != "undefined")
		{
			$extraQuery = "AND userid NOT IN (SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))";
		}
		else
		{
			$extraQuery = "";
		}

		$getLastID = $db->query("SELECT id, userid, content, time_posted FROM (SELECT * FROM $db->table_chat_public ORDER BY id DESC LIMIT 50) $db->table_chat_public WHERE id > '" . $latestID . "' $extraQuery ORDER BY id ASC");
		while ($lastID = mysqli_fetch_object($getLastID))
		{
			$rowID = $lastID->id;
			$user_posted_id = $lastID->userid;
			$chat_content = $lastID->content;
			$chat_posted = $lastID->time_posted;
			$getUserData = $db->query("SELECT username, avatar, login_status FROM $db->table_accdata WHERE account_id=('" . $user_posted_id . "')");
			while ($userData = mysqli_fetch_object($getUserData))
			{
				$username = $userData->username;
				$useravatar = $userData->avatar;
			}
				$useravatar = $main->checkUserAvatar($useravatar);
				
				

			if($user_posted_id == $actualUserID) $stringInsert	=	$style_string; else $stringInsert	=	'';
			
			$getOnline = $db->query("SELECT online FROM $db->table_sessions WHERE id=('" . $user_posted_id . "')");
			while ($online = mysqli_fetch_object($getOnline))
			{
				$getonlineStatus = $online->online;
			}

			if ($getonlineStatus == '1')
			{
				$onlineStatus = '<img src="./images/3Dart/newOnline.png" class="chat_onlineStatus">';
			}
			else
			{
				$onlineStatus = '<img src="./images/3Dart/newOffline.png" class="chat_onlineStatus">';
			}

			$time_posted = $main->convertTime($chat_posted);

			if (isset($_POST['parseEmoticons']) && $_POST['parseEmoticons'] == 'true')
			{
				$chat_content = emoticons($chat_content);
			}


			$image_info = getimagesize($main->getRoot().$useravatar);
			$image_type = $image_info[2];
			

			if ($image_type == IMAGETYPE_PNG)
			{
				$pngClass = ' avatar_png';
			}
			else
			{
				$pngClass = '';
			}

			$newChatRow.= '<div class="chatRow '.$stringInsert.'" id="' . $rowID . '">
  <div class="chatRow_container">
  <div><a href="?page=Profile&amp;User=' . $user_posted_id . '"><b>' . $username . '</b></a>
  <img src="'.$useravatar.'">
    </div>
  </div>
  	<div class="chat_mainMsg">
		' . $chat_content . '
	</div>
	<div class="chatTime">' . $time_posted . '</div>
</div>';
		}

		$content = str_replace(array(
			'\r\n',
			'\r',
			'\n'
		) , "<br />", $newChatRow);
		echo $newChatRow;
	}

	
/* Private Chat  
				START */
				
// Private Loader

	if (isset($_GET['ajaxSend']) && $_GET['ajaxSend'] == 'loadPrv' && isset($_POST['prv_id']) && !empty($_POST['prv_id']))
	{
		$newChatRow = '';
		$prv_id = mysqli_real_escape_string($connection, $_POST['prv_id']);

		// Debugging

		/*if($prv_id != "undefined") {
		$extraQuery = "AND userid NOT IN (SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."'))";
		} else {
		$extraQuery = "";
		}*/
		$getKey = $db->query("SELECT room_key FROM $db->table_chat_rooms WHERE user_1 IN (('" . $prv_id . "'), (SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))) AND user_2 IN (('" . $prv_id . "'), (SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')))");
		while ($key = mysqli_fetch_object($getKey))
		{
			$room_key = $key->room_key;
		}
		
		// If no key exists
		if(!isset($room_key) || mysqli_num_rows($getKey) < '1') {
		
		$counter = 0;
		
		for($i = 0; $i < 1; $i++)
		{
		  if($i == 0)
		  {
			$phrase = "WHERE user_1 = ('".$prv_id."') AND user_2 = (SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))";
			
			$keyCheck = $db->query("SELECT room_key FROM $db->table_chat_rooms $phrase");
			while ($key = mysqli_fetch_object($getKey))
			{
				$counter++;
			}
		  }
		  
		  if($i == 1)
		  {
			$phrase = "WHERE user_1 = (SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')) AND user_2 = ('".$prv_id."')";
			
			$keyCheck = $db->query("SELECT room_key FROM $db->table_chat_rooms $phrase");
			while ($key = mysqli_fetch_object($getKey))
			{
				$counter++;
			}
		  }
		}
		
		if($counter == '0') 
		{
		
			$string	=	$prv_id.$_SESSION['ID'];
			$key	=	mysqli_real_escape_string($connection, md5($string));
			
			$insertKey = $db->query("INSERT INTO $db->table_chat_rooms (room_key, user_1, user_2) VALUES (('".$key."'), ('" . $prv_id . "'), (SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')))");
			if($insertKey)
			{
				$room_key	=	$key;
			}
			else
			{
				$room_key	=	"";
			}
			
		}	
		}

		$getLastID = $db->query("SELECT id, content, time_posted, room_key, userid FROM (SELECT * FROM $db->table_chat_private ORDER BY id DESC LIMIT 50) $db->table_chat_private WHERE room_key = '" . $room_key . "' ORDER BY id ASC");
		while ($lastID = mysqli_fetch_object($getLastID))
		{
			$rowID = $lastID->id;
			$user_posted_id = $lastID->userid;
			$chat_content = $lastID->content;
			$chat_posted = $lastID->time_posted;
			$room_key = $lastID->room_key;
			$getUserData = $db->query("SELECT username, avatar, login_status FROM $db->table_accdata WHERE account_id=('" . $user_posted_id . "')");
			while ($userData = mysqli_fetch_object($getUserData))
			{
				$username = $userData->username;
				$useravatar = $userData->avatar;
			}
			
			$useravatar = $main->checkUserAvatar($useravatar);
			
			if($user_posted_id == $actualUserID) $stringInsert	=	$style_string; else $stringInsert	=	'';

			$getOnline = $db->query("SELECT online FROM $db->table_sessions WHERE id=('" . $user_posted_id . "')");
			while ($online = mysqli_fetch_object($getOnline))
			{
				$getonlineStatus = $online->online;
			}

			if ($getonlineStatus == '1')
			{
				$onlineStatus = '<img src="./images/3Dart/newOnline.png" class="chat_onlineStatus">';
			}
			else
			{
				$onlineStatus = '<img src="./images/3Dart/newOffline.png" class="chat_onlineStatus">';
			}
			
			$time_posted = $main->convertTime($chat_posted);

			if (isset($_POST['parseEmoticons']) && $_POST['parseEmoticons'] == 'true')
			{
				$chat_content = emoticons($chat_content);
			}


			$image_info = getimagesize($useravatar);
			$image_type = $image_info[2];
			if ($image_type == IMAGETYPE_PNG)
			{
				$pngClass = ' avatar_png';
			}
			else
			{
				$pngClass = '';
			}

			$newChatRow.= '<div class="chatRow '.$stringInsert.'" id="' . $rowID . '">
  <div class="chatRow_container">
  <div><a href="?page=Profile&amp;User=' . $user_posted_id . '"><b>' . $username . '</b></a>
  <img src="'.$useravatar.'">
    </div>
  </div>
  	<div class="chat_mainMsg">
		' . $chat_content . '
	</div>
	<div class="chatTime">' . $time_posted . '</div>
</div>';
		}

		$content = str_replace(array(
			'\r\n',
			'\r',
			'\n'
		) , "<br />", $newChatRow);
		echo $newChatRow;
		
}
	
// Private loader END


// Private Chat Send Handler START

	if (isset($_GET['ajaxSend']) && $_GET['ajaxSend'] == 'chatPrivateMessage' && isset($_POST['prv_id']) && !empty($_POST['prv_id']) && isset($_POST['chatMsgContent']) && !empty($_POST['chatMsgContent']) && isset($_POST['parseEmoticons']) && !empty($_POST['parseEmoticons']))
	{
		$newChatRow = '';
		$prv_id = mysqli_real_escape_string($connection, $_POST['prv_id']);
		$content = mysqli_real_escape_string($connection, $_POST['chatMsgContent']);

		// Debugging

		if ($prv_id != "undefined")
		{
			$extraQuery = "AND userid NOT IN (SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))";
		}
		else
		{
			$extraQuery = "";
		}

		$getKey = $db->query("SELECT room_key FROM $db->table_chat_rooms WHERE user_1 IN (('" . $prv_id . "'), (SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))) AND user_2 IN (('" . $prv_id . "'), (SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')))");
		while ($key = mysqli_fetch_object($getKey))
		{
			$room_key = $key->room_key;
		}

		$lastActivity = time();
		$db->query("UPDATE $db->table_sessions SET online='1', last_activity=('" . $lastActivity . "') WHERE sid=('" . $_SESSION['ID'] . "')");
		$getUserData = $db->query("SELECT account_id, username, avatar FROM $db->table_accdata WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))") or die(mysql_error());
		while ($userData = mysqli_fetch_object($getUserData))
		{
			$userid = $userData->account_id;
			$username = $userData->username;
			$useravatar = $userData->avatar;
		}
		
		$useravatar = $main->checkUserAvatar($useravatar);

		if($userid == $actualUserID) $stringInsert	=	$style_string; else $stringInsert	=	'';
		
		$getOnline = $db->query("SELECT online FROM $db->table_sessions WHERE sid=('" . $_SESSION['ID'] . "')");
		while ($online = mysqli_fetch_object($getOnline))
		{
			$getonlineStatus = $online->online;
		}

		if ($getonlineStatus == '1')
		{
			$onlineStatus = '<img src="./images/3Dart/newOnline.png" class="chat_onlineStatus">';
		}
		else
		{
			$onlineStatus = '<img src="./images/3Dart/newOffline.png" class="chat_onlineStatus">';
		}

		$getLastID = $db->query("SELECT id FROM $db->table_chat_private ORDER BY id DESC LIMIT 1");
		while ($lastID = mysqli_fetch_object($getLastID))
		{
			$rowID = $lastID->id;
		}

		if (empty($rowID))
		{
			$rowID = '0';
		}

		$rowID++;
		$messageContent = $_POST['chatMsgContent'];
		$messageContent = htmlspecialchars($messageContent, ENT_QUOTES);
		$messageContent = nl2br($messageContent);
		$messageContent = str_replace(array(
			'\r\n',
			'\r',
			'\n'
		) , "<br />", $messageContent);
		$messageContent = preg_replace('{^(<br(\s*/)?>|ANDnbsp;)+}i', '', $messageContent);
		$messageContent = preg_replace('{(<br(\s*/)?>|ANDnbsp;)+$}i', '', $messageContent);
		$insertTime = time();
		$messageContent_insert = mysqli_real_escape_string($connection, $messageContent);
		$addMessage = $db->query("INSERT INTO $db->table_chat_private (room_key, userid, content, time_posted) VALUES ('" . $room_key . "', '" . $userid . "', '" . $messageContent_insert . "', '" . $insertTime . "')");
		if ($addMessage)
		{		
			$time_posted = $main->convertTime($insertTime);

			if (isset($_POST['parseEmoticons']) && $_POST['parseEmoticons'] == 'true')
			{
				$messageContent = emoticons($messageContent);
			}

			
			$image_info = getimagesize($main->getRoot() . $useravatar);
			$image_type = $image_info[2];
			if ($image_type == IMAGETYPE_PNG)
			{
				$pngClass = ' avatar_png';
			}
			else
			{
				$pngClass = '';
			}

			$newChatRow = '<div class="chatRow '.$stringInsert.'" id="' . $rowID . '">
  <div class="chatRow_container">
  <div><a href="?page=Profile&amp;User=' . $userid . '"><b>' . $username . '</b></a>
  <img src="'.$useravatar.'">
    </div>
  </div>
	<div class="chat_mainMsg">
		' . $messageContent . '
	</div>
	<div class="chatTime">' . $time_posted . '</div>
</div>';
			echo $newChatRow;
		}
		else
		{
			throw new HttpException(500, "Database Error");
		}
	}
	
// Private Chat Send Handler END


// Private Chat Pull Handler START

if (isset($_GET['ajaxLoad']) && $_GET['ajaxLoad'] == 'privateMessage' && isset($_POST['prv_id']) && !empty($_POST['prv_id']) && isset($_POST['latestID']) && !empty($_POST['latestID']))
	{
		$newChatRow = '';
		$prv_id	=	mysqli_real_escape_string($connection, $_POST['prv_id']);
		$latestID = mysqli_real_escape_string($connection, $_POST['latestID']);
		
		if ($latestID != "undefined")
		{
			$extraQuery = "AND userid NOT IN (SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))";
		}
		else
		{
			$extraQuery = "";
		}

		$getKey = $db->query("SELECT room_key FROM $db->table_chat_rooms WHERE user_1 IN (('" . $prv_id . "'), (SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))) AND user_2 IN (('" . $prv_id . "'), (SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')))");
		while ($key = mysqli_fetch_object($getKey))
		{
			$room_key = $key->room_key;
		}
		
		$getLastID = $db->query("SELECT id, userid, content, time_posted FROM (SELECT * FROM $db->table_chat_private ORDER BY id DESC LIMIT 50) $db->table_chat_public WHERE id > '" . $latestID . "' AND room_key = ('".$room_key."') $extraQuery ORDER BY id ASC");
		while ($lastID = mysqli_fetch_object($getLastID))
		{
			$rowID = $lastID->id;
			$user_posted_id = $lastID->userid;
			$chat_content = $lastID->content;
			$chat_posted = $lastID->time_posted;
			$getUserData = $db->query("SELECT username, avatar, login_status FROM $db->table_accdata WHERE account_id=('" . $user_posted_id . "')");
			while ($userData = mysqli_fetch_object($getUserData))
			{
				$username = $userData->username;
				$useravatar = $userData->avatar;
			}
			
			$useravatar = $main->checkUserAvatar($useravatar);

			if($user_posted_id == $actualUserID) $stringInsert	=	$style_string; else $stringInsert	=	'';
			
			$getOnline = $db->query("SELECT online FROM $db->table_sessions WHERE id=('" . $user_posted_id . "')");
			while ($online = mysqli_fetch_object($getOnline))
			{
				$getonlineStatus = $online->online;
			}

			if ($getonlineStatus == '1')
			{
				$onlineStatus = '<img src="./images/3Dart/newOnline.png" class="chat_onlineStatus">';
			}
			else
			{
				$onlineStatus = '<img src="./images/3Dart/newOffline.png" class="chat_onlineStatus">';
			}
			
			$time_posted = $main->convertTime($chat_posted);

			if (isset($_POST['parseEmoticons']) && $_POST['parseEmoticons'] == 'true')
			{
				$chat_content = emoticons($chat_content);
			}


			$image_info = getimagesize($main->getRoot() . $useravatar);
			$image_type = $image_info[2];
			
			if ($image_type == IMAGETYPE_PNG)
			{
				$pngClass = ' avatar_png';
			}
			else
			{
				$pngClass = '';
			}

			$newChatRow.= '<div class="chatRow '.$stringInsert.'" id="' . $rowID . '">
  <div class="chatRow_container">
  <div><a href="?page=Profile&amp;User=' . $user_posted_id . '"><b>' . $username . '</b></a>
  <img src="'.$useravatar.'">
    </div>
  </div>
  	<div class="chat_mainMsg">
		' . $chat_content . '
	</div>
	<div class="chatTime">' . $time_posted . '</div>
</div>';
		}

		$content = str_replace(array(
			'\r\n',
			'\r',
			'\n'
		) , "<br />", $newChatRow);
		echo $newChatRow;
	}

// Private Chat Pull Handler END


// Emoticon State Processor

	if (isset($_GET['ajaxSend']) && $_GET['ajaxSend'] == 'changeEmoticonsState' && isset($_POST['checkboxID']) && !empty($_POST['checkboxID']))
	{
		$checkboxID = mysqli_real_escape_string($connection, $_POST['checkboxID']);
		$getLastID = $db->query("UPDATE $db->table_accdata SET chat_emoticons=CASE WHEN chat_emoticons<1 THEN '1' ELSE '0' END WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))");
	}
	
// Sidebar State Processor

	if (isset($_GET['save']) && $_GET['save'] == 'sidebarState' && isset($_POST['sidebarState']))
	{
	
		$sidebarState = mysqli_real_escape_string($connection, $_POST['sidebarState']);
		
		if(empty($sidebarState)) $sidebarState = '0';
		if($sidebarState < 0) $sidebarState = '0';
		
		if($sidebarState > 1) $sidebarState = '1';
		
		if($sidebarState == '0' || $sidebarState == '1')
		{
			$db->query("UPDATE $db->table_accdata SET chat_sidebar_state='".$sidebarState."' WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))");
		}
	}
}
else
{
	echo 'false';
}

?>