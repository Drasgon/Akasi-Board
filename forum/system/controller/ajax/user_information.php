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
User (Frame) Information System
*/

include('../../classes/akb_mysqli.class.php');
include('../../classes/akb_main.class.php');

if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);

	/*  <---- Neccessary files ---->  */
	
	$main->useFile('./system/controller/processors/icon_parser_processor.php');


$db->query("SET NAMES utf8");

setlocale(LC_ALL, null);
setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu');

/*
   User load handler START
*/

if (isset($_GET['ajaxSend']) && $_GET['ajaxSend'] == 'getuserinfo' && isset($_POST['loadData']) && !empty($_POST['loadData'])) {
	
	$data = mysqli_real_escape_string($connection, $_POST['loadData']);
	
	$getUser = $db->query("SELECT username, gender, avatar, post_counter, user_title, messenger_skype FROM $db->table_accdata WHERE account_id=('".$data."') LIMIT 1");
		while($user = mysqli_fetch_object($getUser)) {
			$username = $user->username;
			$gender = $user->gender;
			$avatar = $user->avatar;
			$post_counter = $user->post_counter;
			$user_title = $user->user_title;
			$messenger_skype = $user->messenger_skype;
		}
	$getOnline = $db->query("SELECT online FROM $db->table_sessions WHERE id=('".$data."') LIMIT 1");
		while($user = mysqli_fetch_object($getOnline)) {
			$online = $user->online;
		}
		
	if($messenger_skype == NULL) {
		$messenger_skype = '';
	} else {
		$messenger_skype = 'Skype: '.$messenger_skype.'<br><br><a href="skype:'.$messenger_skype.'?userinfo" title="Profil aufrufen"><img src="http://mystatus.skype.com/'.$messenger_skype.'" alt="" width="90"></a>';
	}
		
		switch($online) {
			case '0':
				$onlineIcon = './images/icons/offlineS.png';
				break;
			case '1':
				$onlineIcon = './images/icons/onlineS.png';
				break;
		}
		
			switch($gender) {
			
			case 1:
				$gender = './images/icons/undefinedGender.png';
				break;
			case 2:
				$gender = './images/icons/female.png';
				break;
			case 3:
				$gender = './images/icons/male.png';
				break;
			}
		
echo '
<div class="userFrame_attached gradient-sleak" id="userFrame_attached_'.$data.'">
  <div class="userFrame_attached_inner">
    	<div class="userInformation">
      <ul>
        <li class="userAvatar_profile">
		<img src="'.$avatar.'" width="150">
        </li>
        <li class="DetailUserInfo">
			<div class="userCredits">
				<p>
				<img src="'.$onlineIcon.'">
				'.$username.'
				</p>
				<p>
				'.$user_title.'
				</p>
				<p>
				<img src="'.$gender.'">
				</p>
				<p>
				Beiträge: '.$post_counter.'
				</p>
				<p>
				'.$messenger_skype.'
				</p>
			</div>
        </li>
      </ul>
    </div>
  </div>
</div>';
}
?>