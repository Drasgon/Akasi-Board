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

global $langGlobal;

// Re initialize the DB and Runtime Class
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);

if (isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == true) {
    
    
    $userData = $main->getUserdata($_SESSION['ID'], "sid");
        
        $accID_sP    = $userData['account_id'];
        $username_sP = $userData['name'];
		$_SESSION['avatar'] = $userData['avatar'];
		if(!$main->checkImage($_SESSION['avatar']))
		{
			$_SESSION['avatar'] = $main->getDefaultAvatar();
		}
        
        $profileClass = (isset($_GET['page']) && $_GET['page'] == 'Profile' && (isset($_GET['Tab']) && $_GET['Tab'] == 'Edit') && !isset($_GET['User'])) ? 'userPanelOptionBarImage-active' : 'userPanelOptionBarImage';
        $pmClass      = (isset($_GET['page']) && $_GET['page'] == 'Message') ? 'userPanelOptionBarImage-active' : 'userPanelOptionBarImage';
        $cCClass      = (isset($_GET['page']) && $_GET['page'] == 'Account') ? 'userPanelOptionBarImage-active' : 'userPanelOptionBarImage';
        $adminClass   = (isset($_GET['page']) && $_GET['page'] == 'Admin') ? 'userPanelOptionBarImage-active' : 'userPanelOptionBarImage';
        
        
        $sPDisplay = '
<div class="ServicePanel">
	<div class="innerPageHeadline">
		<p class="logout_servicepanel">	
			<a id="openLogout">	
				' . $langGlobal['sPLogout'] . '
			</a>
		</p>
		<p class="welcome">
			' . $langGlobal['sPWelcome'] . ', <a href="?page=Profile&amp;User=' . $accID_sP . '">' . $username_sP . '</a>.
		</p>
	</div>

	<div class="service_panel_content">

		<div class="UserImg">
			<a href="?page=Account&Tab=account&subPage=avatar">
			  <img src="' . $_SESSION["avatar"] . '"  height="120px" class="UserImage">
			</a>
		</div>

		<div class="userPanelOptionBar">
			<ul class="UserOptions">
				<li>
					<a href="?page=Profile&amp;Tab=Edit">
						<div class="icons" id="profiledit"></div>
						<p>
						' . $langGlobal['sPProfileEdit'] . '
						</p>
					</a>
				</li>
				<li>
					<a href="?page=Message&amp;Tab=Inbox">
						<div class="icons" id="messages"></div>
						<p>
						' . $langGlobal['sPMessages'] . '
						</p>
					</a>
				</li>
				<li>
					<a href="?page=Account&amp;Tab=Edit">
						<div class="icons" id="accountpanel"></div>
						<p>
						' . $langGlobal['sPControlCenter'] . '
						</p>
					</a>
				</li>';
				$sPDisplay .= '
			</ul>  
		</div>
	</div>
</div>
<div class="search">
	  <form method="POST" action="?page=Search">
		<input type="text" placeholder="Suchen ..." name="q" maxlength="'.$main->serverConfig('max_search_length').'">
		<input type="submit" class="submit" value="Suchen">
	  </form>
	</div>';

        echo $sPDisplay;
		
} else {

$defaultAvatar = $main->getDefaultAvatar();
    
$visitorPanel = '

<div class="ServicePanel">
	<div class="innerPageHeadline">
		<p class="welcome">
			' . $langGlobal['sPNotlogin_status'] . '
		</p>
		</div>
		
	<div class="service_panel_content">
		<div class="UserImg">
			<img src="'.$defaultAvatar.'" alt width="120px" height="120px" class="UserImage">
		</div>
		
		<div class="userPanelOptionBar">
			<ul class="UserOptions">
				<li>
				</li>
				<li>
				</li>
			</ul>  
		</div>
	</div>
</div>
	<div class="search">
	  <form method="GET" action="?page=Search">
		<input type="text" placeholder="Suchen ...">
		<input type="submit" class="submit" value="Suchen" name="q" id="q">
	  </form>
	</div>';
    
    echo $visitorPanel;
    
}
?>