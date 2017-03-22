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

	$chatMode = true;
	$list_var = '';
	$defaultVolume = '0,5';

	$collectUsers = $db->query("SELECT account_id, username FROM $db->table_accdata WHERE account_id NOT IN (SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')) ORDER BY account_id DESC LIMIT 0,30");

	while ($format_users = mysqli_fetch_object($collectUsers))
		{
		$accountID = $format_users->account_id;
		$username = $format_users->username;
		$list_var.= '
								<li class="chat_prv" id="' . $accountID . '">
									' . $username . '
								</li>';
		}

	if ($chatMode == true)
		{
		$chatJS = '<script defer src="./javascript/chat.js"></script>';
		}

		
	if(isset($_COOKIE['chat_notify_volume'])) $chatVolume = ($_COOKIE['chat_notify_volume'] * 100) . "%";
	if(!isset($chatVolume)) $chatVolume = $defaultVolume;

	$getChatEmoticon_state = $db->query("SELECT chat_emoticons FROM $db->table_accdata WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')) ");

	while ($emoticon_state = mysqli_fetch_object($getChatEmoticon_state))
		{
		$state = $emoticon_state->chat_emoticons;
		switch ($state)
			{
		case '0':
			$emoticonState = '';
			break;

		case '1':
			$emoticonState = 'checked="checked"';
			break;
			}
		}
		
								$getSidebarState = $db->query("SELECT chat_sidebar_state FROM $db->table_accdata WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."')) ");
								while($sidebarState = mysqli_fetch_object($getSidebarState)) {
									$state = $sidebarState->chat_sidebar_state;
									
									switch($state) {
										case '0':
											$state_first	=	'style="display:none"';
											$state_second	=	'style="display:block; width:100%;"';
											break;
										case '1':
											$state_first	=	'style="display:block; width:30%;"';
											$state_second	=	'style="display:block; width:70%;"';
											break;
									}
								}

	$chat	=	'';

	$chat	.=	'
		<div class="portal_chat portal_container">
			<div class="portal_chat_inner">
				<div class="portal_ConHeader catHeaderOuter" id="portal_cat2">
					Chat
				</div>
				<div class="portal_ConMain">
				  <div class="chat_right_sidebar" '.$state_first.'>
				  <h3 class="catHeaderOuter">
						Allgemeinchats
				   </h3>
				   <ul>
						<li class="public_chats" id="1">
							Allgemein
						</li>
						<li class="public_chats" id="2">
							Technischer Support
						</li>
						<li class="public_chats" id="3">
							Tratsch-Ecke
						</li>
				   </ul>
				   <h3 class="catHeaderOuter">
						Privatchats
				   </h3>
					<ul>
					'.$list_var.'
					</ul>
				  </div>
					<div class="chat_msgField" '.$state_second.'>
					  <div class="chat_headerOverlay" '.$state_second.'>
						</div>
					<p class="chat_welcomeMsg">
						-- Willkommen zum AkasiBoard Chat --<br>-- Status: Online | Bereit -- <br>-- Version: 0.7.2 Dev-Release --
					</p>
					</div>
						<form method="POST" id="portal_chat">
					<div class="chat_msgInput">
					  <textarea type="text" class="chatInput" id="chatMsg_add" name="chatMsg_add" onkeypress="if(event.keyCode == 13 && !event.shiftKey){ $(\'#portal_chat\').submit();event.preventDefault(); };" onkeyup="checkInput($(this).val());"></textarea>
						'.$chatJS.'
					</div>
							<input type="submit" class="LogoutSubmitTrue chat_submit" disabled="true">
							<input type="button" class="LogoutSubmitTrue chat_submit" id="toggleSidebar" value="Toggle Sidebar">
							<!--<input type="range" id="chat_notify_volume" min="0" max="1" value="'.$chatVolume.'" step="0.01" onchange="rangevalue.value=value*100+\'%\'">
							<output id="rangevalue">'.$chatVolume.'</output>-->
							<span class="chat_emoticons_setting"><input type="checkbox" id="parseEmoticons" name="parseEmoticons"
								'.$emoticonState.'><label for="parseEmoticons">Smileys anzeigen</label></span>
						</form>
				</div>
			</div>
		</div>';
		
	echo $chat;