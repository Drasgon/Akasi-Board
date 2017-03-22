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
class Chat

	{
	private $_database;
	private $_link;
	private $_session;
	private $_main;


	public function __construct($database, $link, $main)
		{
		
			$this->_database = $database;
			$this->_link = $link;
			$this->_main = $main;
			$this->_session = mysqli_real_escape_string($this->_link, $_SESSION['ID']);
		
			$checkUserbyQuery = $this->_database->query("SELECT sid, id FROM ".$this->_database->table_accounts." WHERE sid=('" . $_SESSION['ID'] . "')");
			if (mysqli_num_rows($checkUserbyQuery) == 0 || mysqli_num_rows($checkUserbyQuery) > 1)
			{
				setcookie('PHPSESSID', '', time() - 3600);
				$_SESSION['STATUS'] = false;
			}
			else
			{
				$_SESSION['STATUS'] = true;
				$this->_main->useFile('./system/controller/security/permission_system.php');
				while($ownID = mysqli_fetch_object($checkUserbyQuery))
				{
					$this->userID			=	$ownID->id;
					$this->style_string 	=	'msgOwner';
				}

			}
		
		}


	public function RegisterPublicMessage($string)
		{
		
			$lastActivity = time();
			$this->_database->query("UPDATE ".$this->_database->table_sessions." SET online='1', last_activity=('" . $lastActivity . "') WHERE sid=('" . $this->_session . "')");
			$getUserData = $this->_database->query("SELECT account_id, username, avatar FROM ".$this->_database->table_accdata." WHERE account_id=(SELECT id FROM ".$this->_database->table_accounts." WHERE sid=('" . $this->_session . "'))") or die(mysql_error());
			while ($userData = mysqli_fetch_object($getUserData))
				{
				$userid = $userData->account_id;
				$username = $userData->username;
				$useravatar = $userData->avatar;
				}

			if ($userid == $this->userID) $stringInsert = $this->style_string;
			  else $stringInsert = '';
			$getOnline = $this->_database->query("SELECT online FROM ".$this->_database->table_sessions." WHERE sid=('" . $this->_session . "')");
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

			$getLastID = $this->_database->query("SELECT id FROM ".$this->_database->table_chat_public." ORDER BY id DESC LIMIT 1");
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
			$messageContent_insert = mysqli_real_escape_string($this->_link, $messageContent);
			$addMessage = $this->_database->query("INSERT INTO ".$this->_database->table_chat_public." (userid, content, time_posted) VALUES ('" . $userid . "', '" . $messageContent_insert . "', '" . $insertTime . "')");
			if ($addMessage)
				{
				if (date('Y-m-d', $insertTime) == date('Y-m-d'))
					{
					$time_posted = strftime('Heute, %H:%M', $insertTime);
					}
				elseif (date('Y-m-d', $insertTime) == date('Y-m-d', strtotime("Yesterday")))
					{
					$time_posted = strftime('Gestern, %H:%M', $insertTime);
					}
				elseif (date('Y-m-d', $insertTime) < date('Y-m-d', strtotime("Yesterday")))
					{
					$time_posted = strftime("%A, %d %B %Y %H:%M", $insertTime);
					}

				if (isset($_POST['parseEmoticons']) && $_POST['parseEmoticons'] == 'true')
					{
					$messageContent = emoticons($messageContent);
					}

				$image_info = getimagesize($this->_main->getRoot().$useravatar);
				$image_type = $image_info[2];
				if ($image_type == IMAGETYPE_PNG)
					{
					$pngClass = ' avatar_png';
					}
				  else
					{
					$pngClass = '';
					}

				$newChatRow =  '<div class="chatRow ' . $stringInsert . '" id="' . $rowID . '">
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
	}

?>