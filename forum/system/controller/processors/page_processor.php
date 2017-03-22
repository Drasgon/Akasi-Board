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

global $langGlobal, $totalQueries, $totalQueryTime, $totalGenerateTime;

// Re initialize the DB and Runtime Class
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);


$portalActive  = (isset($_GET['page']) && $_GET['page'] == 'Portal' || !isset($_GET['page'])) ? 'class="active"' : '';
$forumActive   = (isset($_GET['page']) && $_GET['page'] == 'Index' || (isset($_GET['page']) && $_GET['page'] == 'Thread')) ? 'class="active"' : '';
$membersActive = (isset($_GET['page']) && $_GET['page'] == 'Members') ? 'class="active"' : '';
$galleryActive = (isset($_GET['page']) && $_GET['page'] == 'Gallery') ? 'class="active"' : '';
?>  
  <div class="page scene_element" id="page">
    <div class="header scene_element scene_element--fadein">
		<div class="infoline_header">
			<ul class="infoline_content">
				<!--<li class="socialbar">
					<ul>
						<li>
						<a href="#"><div class="icons" id="socialfb" title="Facebook" ></div></a>
						<li>
						<a href="#"><div class="icons" id="socialyt" title="Youtube"></div></a>
						</li>
						<li>
						<a href="#"><div class="icons" id="socialdb" title="Dropbox"></div></a>
						</li>
						<li>
						<a href="#"><div class="icons" id="socialpizza" title="Pizza!"></div></a>
						</li>
					</ul>
				</li>-->
				
				<?php
				if (isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == true) {
				
				echo '
				
				<li class="infoline_optionsbar">
					<ul class="infoline_userOptions">
						<li>
						</li>
						<li>
						</li>
					</ul>
				</li>
				<div class="infoline_contentright">
				<li class="infoline_messages">
					<a href="?page=Notes" class="notification_text">
						Benachrichtigungen ('; $main->useFile('./system/controller/processors/message_processor.php'); echo ')
					</a>
				</li>';
				
		
				} else {
				
				$optionsvisitor = '
				
				<li class="infoline_optionsbar">
					<ul class="infoline_userOptions">
						<li>
							<a href="?page=Login" class="no-smoothstate">Anmelden</a>
						</li>
						<li>
							<a href="?page=Register">Registrieren</a>
						</li>   
					</ul>  
				</li>
				<div class="infoline_contentright">';
				
				
				echo $optionsvisitor;
				}
				
				if ($main->checkSessionAccess('ADMIN') || $main->checkSessionAccess('MOD')) {
					$adminbutton = '
					<li class="infoline_adminoptions">
						<a href="?page=Admin&amp;Tab=Verify">
						<div class="icons" id="adminicon"></div> Administration	
						</a>
					</li>';
						echo $adminbutton;
				} 
				
				$dates = array("SO", "MO", "DI", "MI", "DO", "FR", "SA");
				
				setlocale(LC_ALL, null);
				setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu');
				$sTStamp    = time();
				$serverTime = strftime("%d. %m. %Y - %H:%M:%S", $sTStamp);
				
				$day = date( "w", $sTStamp);
				$day = $dates[$day];
				
				$infotime = '
				
				<li class="infoline_serverTime">
				
				'.$day.', <span id="serverTime">' . $serverTime . '</span>
				</li></div>';
				echo $infotime;
				
				?>
			</ul>
		</div>
	<div class="service_panel_frame">
	<?php
	// Container for the ServicePanel
	echo '<div class="pageHeadline gradient-sleak">';
	
	// Show service panel
		$main->useFile('./system/controller/processors/service_panel.php');

	// If client is not logged in, show draggable loginpanel.
	if (!isset($_SESSION['STATUS']) || $_SESSION['STATUS'] == false) {
    
    $main->useFile('./system/interface/loginPanel.php');
}

	// Close ServicePanel
	echo '</div>';
	?>
	</div>
	
    <span id="refresh" class="refreshCon"></span>	  
</div>


    <div class="main scene_element scene_element--fadeinleft">
		<div class="mainNavigation">
			<div class="mainNavigationInner">
				<ul>
					<li <?php echo $portalActive; ?> >
						<a href="?page=Portal" id="activeChanger">
							<p>
								<div class="icons" id="navPortal"></div>
								<p>
								<?php echo $langGlobal['portal']; ?>
								</p>
							</p>
						</a>
					</li>
            
					<li <?php echo $forumActive; ?> >
						<a href="?page=Index" id="activeChanger">	
							<p>
								<div class="icons" id="navForum"></div>
								<p>
								<?php echo $langGlobal['forum']; ?>
								</p>
							</p>
						</a>
					</li>
					
					<li <?php echo $membersActive; ?> >
						<a href="?page=Members" id="activeChanger">
							<p> 
								<div class="icons" id="navMembers"></div>
								<p>
								<?php echo $langGlobal['members']; ?>
								</p>
							</p>
						</a>
					</li>
					
					<li <?php echo $galleryActive; ?> >
						<a href="?page=Gallery" id="activeChanger">
							<p> 
								<div class="icons" id="navGallery"></div>
								<p>
								<?php echo $langGlobal['gallery_string']; ?>
								</p>
							</p>
						</a>
					</li>
				</ul>
			</div>
		</div>
		<div class="mainInner">
		
	    <div class="locationRoute">
	<?php
$main->useFile('./system/controller/processors/breadcrumbs_processor.php');
?>
		</div>
		<?php
if (isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == true) {
    
    $new_savedThread = '';
	$data_available = FALSE;
    
    $checkForSaved = $db->query("SELECT token, board_id, title, content FROM $db->table_thread_saves WHERE user_id=(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))");
    
    if (mysqli_num_rows($checkForSaved) >= 1) {
        
        $new_savedThread .= '
	<div class="userInfobox" id="saved_threads_container">
	  <div class="userInfobox_inner">
			<div class="icons" id="userInfobox_img"></div>
			<div class="icons" id="delete_saved_threads" title="Alle gespeicherten Themen löschen"></div>
	<div class="userInfobox_content">
		<p>Sie haben noch gespeicherte Themen:</p> 
		<ul>';
        
        while ($savedThreads = mysqli_fetch_object($checkForSaved)) {
			
            $token    = $savedThreads->token;
            $board_id = $savedThreads->board_id;
            $title    = $savedThreads->title;
            $content  = $savedThreads->content;
			
			if(!empty($title) && !empty($content))
			{
				$new_savedThread .= '
				
				<li><a href="?page=Index&boardview=' . $board_id . '&form=threadAdd&token=' . $token . '">' . $board_id . ': ' . $title . '</a> <div class="icons" id="delsavthread_scaled" title="Gespeichertes Thema löschen"></div></li>
				';
				
				$data_available = TRUE;
			}
			else
				$data_available = FALSE;
        }
        
        $new_savedThread .= '
	</ul>
	</div>
	</div>
	</div>';
        
		if($data_available == TRUE)
			echo $new_savedThread;
    }
    
    $new_savedPost = '';
    
    $checkForSavedPosts = $db->query("SELECT token, thread_id, content FROM $db->table_post_saves WHERE user_id=(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))");
    
    if (mysqli_num_rows($checkForSavedPosts) >= 1) {
        
		
		$data_available = FALSE;
		
        $new_savedPost .= '
	<div class="userInfobox" id="saved_posts_container">
	  <div class="userInfobox_inner">
			<div class="icons" id="new_thread_icon"></div>
	<div class="userInfobox_content">
		<p>Sie haben noch gespeicherte Beiträge:</p> 
		<ul>';
        
        while ($savedPosts = mysqli_fetch_object($checkForSavedPosts)) {
            
            $token     = $savedPosts->token;
            $thread_id = $savedPosts->thread_id;
            $content   = $savedPosts->content;
            
            $getThreadName = $db->query("SELECT title FROM $db->table_thread WHERE id=('" . $thread_id . "')");
            while ($threadName = mysqli_fetch_object($getThreadName)) {
                
                $thread_name = $threadName->title;
                
            }
            
			if(!empty($token) && !empty($thread_id) && !empty($content))
			{
				$new_savedPost .= '
				
				<li><a href="?page=Index&threadID=' . $thread_id . '&form=postAdd&token=' . $token . '">' . $thread_name . '</a> <img src="./images/icons/delete_scaled.png" class="saved_post_delete_selected" id="' . $token . '" title="Gespeichertes Thema löschen" width=20 height=20></li>

				';
				
				$data_available = TRUE;
			}
			else
				$data_available = FALSE;
        }
        
        $new_savedPost .= '
	</ul>
			<div class="icons" id="delete_saved_posts" title="Alle gespeicherten Beiträge löschen"></div>
		</div>
	</div>
	</div>';
        
		if($data_available == TRUE)
			echo $new_savedPost;
    }
}

/*  <---- Main Files ---->  */

$main->useFile('./system/controller/processors/page_container_processor.php');

/*  <---- Main Files END ---->  */


$counter = '0';

$footer         = '
	</div>
  </div>
  <footer class="footer">
	<table>
		<tbody>
			<tr>
			';
			
// Re initialize the DB
if(!isset($db) || $db == NULL) {
	$db         = new Database();
	$connection = $db->mysqli_db_connect();
}
			
$onlineList     = '';
$getOnlineUsers = $db->query("SELECT username, account_id FROM $db->table_accdata WHERE account_id= ANY (SELECT id FROM $db->table_sessions WHERE online='1')");
while ($onlineUsers = mysqli_fetch_object($getOnlineUsers)) {
    $onlineUsername = $onlineUsers->username;
    $onlineUserid   = $onlineUsers->account_id;
    
    if ($counter == '0') {
        $onlineList .= '<a href="?page=Profile&User=' . $onlineUserid . '">' . $onlineUsername . '</a>';
    } else {
        $onlineList .= ', <a href="?page=Profile&User=' . $onlineUserid . '">' . $onlineUsername . '</a>';
    }
    
    $counter++;
}
if (mysqli_num_rows($getOnlineUsers) == 1) {
    $listVal     = 'ist';
    $listVal_sec = 'registrierter';
    $listPoint   = ':';
}
if (mysqli_num_rows($getOnlineUsers) == 0) {
    $listVal     = 'sind';
    $listVal_sec = 'registrierte';
    $listPoint   = '';
}
if (mysqli_num_rows($getOnlineUsers) > 1) {
    $listVal     = 'sind';
    $listVal_sec = 'registrierte';
    $listPoint   = ':';
}

$about = $main->serverConfig("about");

	$software_title = $main->serverConfig("software_title");
	$software_version = $main->serverConfig("software_version");
	$database_version = $main->serverConfig("db_version");
	$software_author = $main->serverConfig("software_author");
	
$copyright_text = $main->serverConfig("copyright_text");
	$copyright_text = str_replace('%st', $software_title, $copyright_text);
	$copyright_text = str_replace('%sv', $software_version, $copyright_text);
	$copyright_text = str_replace('%dbv', $database_version, $copyright_text);
	$copyright_text = str_replace('%sa', $software_author, $copyright_text);
	
$footer .= '
<td>
	<div class="onlineList">
    <div class="onlineList_header">
      <h3>Neben Ihnen ' . $listVal . ' derzeit ' . $counter . ' ' . $listVal_sec . ' Nutzer online' . $listPoint . '</h3><br>
    </div>
	<div class="onlineList_main boardTitle">
	' . $onlineList . '
	</div>
  </div>
</td>

<td>
<div class="aboutus">
	<h3>Über die Seite</h3>
	<p>'.$about.'</p>
</div>
</td>

<td>
 <div class="useful_links">
	<h3>Nützliche Links</h3>
	<ul>
		<li>
			<a href="https://www.google.de/" target="_blank">Google</a>
		</li>
		<li>
			<a href="https://www.facebook.com" target="_blank">Facebook</a>
		</li>
	</ul>
</div> 
</td> 
  
<td>
<div class="footerList">
	<ul>
		
		<li>
			<a href="?page=PrivacyPolicy">
				<div class="icons" id="privatePolicy"></div>  ' . $langGlobal['privacyPolicy'] . '
			</a>
		</li>
		
		
		<li>
			<a href="?page=tos">
				<div class="icons" id="termofuse"></div>  ' . $langGlobal['termsofuse'] . '
			</a>
		</li>
		
		
		<li>
			<a href="?page=contact">
				<div class="icons" id="contact"></div>  ' . $langGlobal['contact'] . '
			</a>
		</li>
		
	</ul>
</div>
</td>
	
			</tr>
		</tbody>
	</table>
	
<p class="footerText">
		' . $copyright_text . '<br />
		Query execution time:'.(round($totalQueryTime * 1000, 3)).' ms - Queries fired: '.$totalQueries.' <br>
		Page generated in: '.(round((microtime(TRUE) - $totalGenerateTime) * 1000, 3)).' ms
</p>
  </footer>';
echo $footer.'</div>';
?>