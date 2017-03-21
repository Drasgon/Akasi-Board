<?php

if (!isset($_COOKIE['akb_last_visit'])) {
    echo '
<div class="userInfobox">
	<div class="userInfobox_inner">
		<div class="userInfobox_img">
			<img src="./images/3Dart/information.png">
		</div>
		<div>
			'.$GLOBALS['portal_lang_welcome'].'
		</div>
	</div>
</div>';
    setcookie('akb_last_visit', time(), time() + (60 * 60 * 24 * 365));
}

if (!isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $client_ip = $_SERVER['REMOTE_ADDR'];
} //!isset( $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] )
else {
    $client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
?>

<div class="portal_base">
<!--<div class="portal_mask" title="Das Portal ist zurzeit gesperrt"></div>-->
<div class="portal_leftSidebar_outer column">
<div class="portal_leftSidebar">
	<div class="portal_userActions portal_container">
		<div class="portal_userActions_inner">
			<div class="portal_ConHeader catHeaderOuter" id="portal_cat1">
				Nutzeraktionen
			</div>
			<div class="portal_ConMain">
				
					<?php
if (!isset($_SESSION['angemeldet']) || $_SESSION['angemeldet'] == false) {
    echo '
					<ul>
				 <a href="?page=Login">
					<li>
					<div class="img_noRepeat img_20x20 login_icon"></div>
					'.$GLOBALS["sPLogin"].'
					</li>
				 </a>
				 <a href="?page=Register">
					<li>
					<div class="img_noRepeat img_20x20 register_icon"></div>
					'.$GLOBALS["sPRegister"].'
					</li>
				 </a>
				 <a href="?page=PrivacyPolicy">
					<li>
					<div class="img_noRepeat img_20x20 help_icon"></div>
					'.$GLOBALS["sPHelp"].'
					</li>
				 </a>
				 	</ul>';
} elseif (isset($_SESSION['angemeldet']) && $_SESSION['angemeldet'] == true) {
    
    $userActionBar = '';
    
    $userData = $main->getUserdata($_SESSION['ID'], "sid");
	
        $id           = $userData['account_id'];
        $username     = $userData['name'];
        $avatar       = $userData['avatar'];
        $post_counter = $userData['posts'];
		$profile_views= $userData['profile_views'];
        
    
		if(!$main->checkImage($avatar))
		{
			$avatar = $main->getDefaultAvatar();
		}
		$image_info = getimagesize($avatar);
		$image_type = $image_info[2];
	
	if ($image_type == IMAGETYPE_PNG) {
	$pngClass = 'avatar_png';
   } else {
	$pngClass = '';
   }
	
    $getuser = $db->query("SELECT registered_date FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')");
    while ($user = mysqli_fetch_object($getuser)) {
        $registered = $user->registered_date;
    }
	
        $registered = strftime("%A, %d %B %Y", $registered);
    
    
    $userActionBar .= '
					<div class="UserSidebar">
<div class="msgAuthor">
<p class="usernameMsg">
<a href="?page=Profile&amp;User=' . $id . '" title="Profil von Ian Tanthal aufrufen.">
<span>
' . $username . '
</span>
</a>
</p>
</div>
<div class="userAvatar">
<div class="UserAvatarMsg">
<img src="' . $avatar . '" class="'.$pngClass.'">
</div>
</div>
<div class="userMessenger">
</div>
</div>
		<ul class="portal_infoList">
			<li>
				<h3 class="portal_information_container">'.$GLOBALS["string_informations"].'</h3>
				<ul>
				  <li title="'.$registered.'">
					<div class="img_noRepeat img_20x20" id="profiledit"></div>
					' . $GLOBALS["portal_lang_registered_at"] . $registered . '
				  </li>
				  <li title="'.$profile_views.'">
					<div class="img_noRepeat img_20x20" id="contact"></div>
					' . $GLOBALS["portal_lang_profile_views"] . $profile_views . '
				  </li>
				  <li title="'.$post_counter.'">
					<div class="img_noRepeat img_20x20" id="thread_def"></div>
					' . $GLOBALS["portal_lang_profile_posts"] . $post_counter . '
				  </li>
				  <li title="n.A">
					<div class="img_noRepeat img_20x20" id="thread_new"></div>
					' . $GLOBALS["portal_lang_profile_activity"].'n.A
				  </li>
				  <li title="'.$client_ip.'">
					<div class="img_noRepeat img_20x20" id="network"></div>
					' . $GLOBALS["portal_lang_profile_ip"] . $client_ip . '
				  </li>
				</ul>
			</li>
			<li>
				<h3 class="portal_information_container">Funktionen</h3>
				<ul>
				 <a href>
					<li id="openLogout" title="Abmelden">
					<div class="img_noRepeat img_20x20" id="close"></div>
					Abmelden
					</li>
				 </a>
				 <a href="?page=Profile&Tab=Edit">
					<li title="Profil bearbeiten">
					<div class="img_noRepeat img_20x20" id="profiledit"></div>
					Profil bearbeiten
					</li>
				 </a>
				 <a href="?page=Message&Tab=Inbox">
					<li title="Private Nachrichten">
					<div class="img_noRepeat img_20x20" id="messages"></div>
					Private Nachrichten
					</li>
				 </a>
				 <a href="?page=Account&Tab=Edit">
					<li title="Kontrollzentrum">
					<div class="img_noRepeat img_20x20" id="accountpanel"></div>
					Kontrollzentrum
					</li>
				 </a>
				</ul>
			</li>
		</ul>
					';
    
    echo $userActionBar;
}
?>
					
			</div>
		</div>
	</div>
	
	<div class="portal_newUsers portal_container">
		<div class="portal_newUsers_inner">
			<div class="portal_ConHeader catHeaderOuter" id="portal_cat2">
				Neue Miglieder
			</div>
			<div class="portal_ConMain">
				<ul>
				<?php
$newUserCon = '';

$getNewUsers = $db->query("SELECT registered_date, username, id FROM $db->table_accounts WHERE accepted='1' ORDER BY registered_date DESC LIMIT 5");
while ($newUsers = mysqli_fetch_object($getNewUsers)) {
    $new_usernames = $newUsers->username;
    $new_userid    = $newUsers->id;
    
    $newUserCon .= '
						
					<a href="?page=Profile&User=' . $new_userid . '">
						<li class="userFrame" id="'.$new_userid.'">
							' . $new_usernames . '
						</li>
					</a>';
}
echo $newUserCon;
?>
				</ul>
			</div>
		</div>
	</div>
<?php
if (isset($_SESSION['angemeldet']) && $_SESSION['angemeldet'] == true) {
    echo '
		<div class="portal_userActions portal_container">
		<div class="portal_userActions_inner">
			<div class="portal_ConHeader catHeaderOuter" id="portal_cat1">
				Freunde
			</div>
			<div class="portal_ConMain">
				<ul>
				 
					<li>
							<span class="noOnlineUsers">Diese Funktion befindet sich derzeit in Entwicklung.</span>
						</li>
				 
				</ul>
			</div>
		</div>
	</div>
	';
}
?>
</div>
</div>

<div class="portal_center column">
<div class="portal_userActions portal_container">
   <div class="portal_userActions_inner">
      <div class="portal_ConHeader catHeaderOuter" id="portal_cat1">
         Newsticker
      </div>
      <div class="portal_ConMain">
         <div class="portal_news">
		 			<?php
										
				$getNewsID = $db->query("SELECT news_id FROM $db->table_portal_news LIMIT 1");
					while($newsID = mysqli_fetch_object($getNewsID)) {
						$id = $newsID->news_id;
					}
				
				$getNewsContent = $db->query("SELECT title, date_created, author_id FROM $db->table_thread WHERE id=('".$id."') ORDER BY id DESC LIMIT 1");
					while($newsContent = mysqli_fetch_object($getNewsContent)) {
						$title = $newsContent->title;
						$date_created = $newsContent->date_created;
						$author_id = $newsContent->author_id;
					}
					
	if (date('Y-m-d', $date_created) == date('Y-m-d')) {
        $date_created = strftime('<span class="timeRange">Heute</span>, %H:%M', $date_created);
    } elseif (date('Y-m-d', $date_created) == date('Y-m-d', strtotime("Yesterday"))) {
        $date_created = strftime('<span class="timeRange">Gestern</span>, %H:%M', $date_created);
    } elseif (date('Y-m-d', $date_created) <= date('Y-m-d', strtotime("Yesterday"))) {
        $date_created = strftime("%A, %d %B %Y %H:%M", $date_created);
    }
					
				$getAuthor = $db->query("SELECT username FROM $db->table_accdata WHERE account_id=('".$author_id."') LIMIT 1");
					while($author = mysqli_fetch_object($getAuthor)) {
						$username = $author->username;
					}
					
				$getContent = $db->query("SELECT text FROM $db->table_thread_posts WHERE thread_id=('".$id."') ORDER BY id ASC LIMIT 1");
					while($content = mysqli_fetch_object($getContent)) {
						$text = $content->text;
					}
				 $target = strlen($text);
					if($target>1200) {
					if(($newtarget = strpos($text, ' ', 1200)) !== false ) {
					$target = $newtarget;
					} else {
					$target = 1200;
					 }
					}
					$text = substr($text, 0,$target);
					$text = $main->closetags($text);
				$text .= '... <a href="?page=Index&threadID='.$id.'">[Weiterlesen]</a>';


		
		
			$postcontent ='
			<div class="portal_newsheader">
					<div class="img_noRepeat img_50x50 news_icon"></div>
				<div class="portal_newsheader_right">
					<h3 class="portal_news_subheader">'.$title.'</h3>
					<p class="portal_news_posted">'.$date_created.' von <a href="?page=Profile&User='.$author_id.'">'.$username.'</a></p>
				</div>
			</div>';
			$postcontent .='
			<div class="portal_news_main">
				'.$text.'
			</div>
			<div class="portal_news_footer">
			<ul class="largeButtons">
				<li>
				<a href="?page=Index&amp;threadID='.$id.'&form=postAdd" id="replyButton1" title="Antworten">
					<img src="images/icons/threadAdd-Msg.png" alt="">
					<span>
						Antworten
					</span>
				</a>
				</li>
			</ul>
			</div>';
			
			echo $postcontent;
			?>
      </div>
   </div>
</div>
	
	<?php
if (isset($_SESSION['angemeldet']) && $_SESSION['angemeldet'] == true) {
    
    require('./system/interface/ajaxChat.php');
    
}
?>
	
	<div class="portal_userActions portal_container">
		<div class="portal_userActions_inner">
			<div class="portal_ConHeader catHeaderOuter" id="portal_cat1">
				Die neuesten Beiträge
			</div>
			<div class="portal_ConMain">
				<?php
$initLastPosts = '
				<table class="tableList">
<thead>
<tr class="tableHead">
<th colspan="2">
<div>
<a href="#" disabled="disabled">
Thema		  	  
<div class="lP_descSort">
</div>

</a>
</div>
</th>
<th>
<div>
<a href="#" disabled="disabled">
Nutzerbewertung	  
<div class="lP_descSort">
</div>

</a>
</div>
</th>
<th>
<div>
<a href="#" disabled="disabled">
Antworten	  
<div class="lP_descSort">
</div>

</a>
</div>
</th>
<th>
<div>
<a href="#" disabled="disabled">
Ansichten
<div class="lP_descSort">
</div>

</a>
</div>
</th>
<th>
<div>
<a href="#" disabled="disabled">
Letzte Antwort 
<div class="lP_descSort">
</div>

</a>
</div>
</th>
</tr>
</thead>
<tbody>';


$getLatestPosts = $db->query("SELECT id, main_forum_id, title, date_created, last_replyTime, author_id, last_post_author_id, rating, rating_votes, posts, views FROM $db->table_thread WHERE id ORDER BY last_replyTime DESC LIMIT 10");

while ($latestPosts = mysqli_fetch_object($getLatestPosts)) {
    $latestID             = $latestPosts->id;
    $latestMainID         = $latestPosts->main_forum_id;
    $latestTitle          = $latestPosts->title;
    $latestCreated        = $latestPosts->date_created;
    $lastReply            = $latestPosts->last_replyTime;
    $authorID             = $latestPosts->author_id;
    $lastAuthorID         = $latestPosts->last_post_author_id;
    $threads_rating       = $latestPosts->last_post_author_id;
    $threads_rating_votes = $latestPosts->last_post_author_id;
    $threads_sub_views    = $latestPosts->views;
    $threads_sub_posts    = $latestPosts->posts;
    
    $actualTime = time();
    if (date('Y-m-d', $latestCreated) == date('Y-m-d')) {
        $lastThreadReplyF = strftime('<span class="timeRange">Heute</span>, %H:%M', $latestCreated);
    } elseif (date('Y-m-d', $latestCreated) == date('Y-m-d', strtotime("Yesterday"))) {
        $lastThreadReplyF = strftime('<span class="timeRange">Gestern</span>, %H:%M', $latestCreated);
    } elseif (date('Y-m-d', $latestCreated) <= date('Y-m-d', strtotime("Yesterday"))) {
        $lastThreadReplyF = strftime("%A, %d %B %Y %H:%M", $latestCreated);
    }
    
    $actualTime = time();
    if (date('Y-m-d', $lastReply) == date('Y-m-d')) {
        $threads_sub_last_reply_F = strftime('<span class="timeRange">Heute</span>, %H:%M', $lastReply);
    } elseif (date('Y-m-d', $lastReply) == date('Y-m-d', strtotime("Yesterday"))) {
        $threads_sub_last_reply_F = strftime('<span class="timeRange">Gestern</span>, %H:%M', $lastReply);
    } elseif (date('Y-m-d', $lastReply) <= date('Y-m-d', strtotime("Yesterday"))) {
        $threads_sub_last_reply_F = strftime("%A, %d %B %Y %H:%M", $lastReply);
    }
    
    
    $get_author    = "SELECT username FROM $db->table_accdata WHERE account_id='" . $authorID . "'";
    $author_result = $db->query($get_author);
    while ($threads_author_fetch = mysqli_fetch_object($author_result)) {
        $threads_author = $threads_author_fetch->username;
    }
    
    $get_last_author    = "SELECT username FROM $db->table_accdata WHERE account_id='" . $lastAuthorID . "'";
    $last_author_result = $db->query($get_last_author);
    while ($last_author_fetch = mysqli_fetch_object($last_author_result)) {
        $threads_participant_last = $last_author_fetch->username;
    }
    
    $threads_rating       = $latestPosts->rating;
    $threads_rating_votes = $latestPosts->rating_votes;
    
    $thread_rating_ = '';
			
            if ($threads_rating_votes != '0' && $threads_rating != '0') {
            $threads_rating_calc   = $threads_rating / $threads_rating_votes;
            $threads_rating_calced = round($threads_rating_calc, 0, PHP_ROUND_HALF_DOWN);
			
			for($i = 0; $i <= 4; $i++)
			{
				if($i < $threads_rating_calced) {
				
				$thread_rating_ .= '<div class="icons_small" id="rating"></div>';
				} else {
				
				$thread_rating_ .= '<div class="icons_small" id="rating_0"></div>';
				}
			}
			
			} else {
		
				for($i = 0; $i <= 4; $i++)
				{
					$thread_rating_ .= '<div class="icons_small" id="rating_0"></div>';
				}
			
			}
    
    
    if (!(isset($thread_sub_closed) && $threads_sub_closed == 0)) {
        $threads_sub_title_msg = '';
        
        if (isset($_SESSION['angemeldet']) && $_SESSION['angemeldet'] == true) {
            
            $_SESSION['ID'] = session_id();
            $threadID       = $latestID;
            $getUser        = $db->query("SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')");
            while ($userResult = mysqli_fetch_object($getUser)) {
                $userID = $userResult->id;
            }
            
            $secureThreadID = mysqli_real_escape_string($GLOBALS['connection'], $threadID);
            $secureUserID   = mysqli_real_escape_string($GLOBALS['connection'], $userID);
            
            /*$statusQuery = $db->query("SELECT account_id, thread_id, board_id FROM $db->table_forum_read WHERE account_id=('" . $secureUserID . "') AND thread_id=('" . $secureThreadID . "')");
            if (mysqli_num_rows($statusQuery) == 1) {
                $unread_status = false;
            } else {
                $unread_status = true;
            }*/
			
			$unread_status = $main->detectUnreadThread($threadID);
            
            if ($unread_status == true) {
                $threads_sub_icon = './images/icons/thread_def.png';
            } else {
                $threads_sub_icon = './images/icons/thread_new.png';
            }
        } else {
            $threads_sub_icon = './images/icons/thread_def.png';
        }
    } else {
		$threads_sub_icon = './images/icons/thread_def.png';
	}
    
    $initLastPosts .= '
<tr class="container-1">
<td class="columnIcon" style="width: 60px;">
<img src="' . $threads_sub_icon . '" alt="" width="25"  height="35" class="threads_img">
</td>
<td class="columnTopic">
<div class="topic">
<p>
<span class="Title">
<strong>
</strong>
</span>
<a href="?page=Index&amp;threadID=' . $latestID . '">
' . $latestTitle . '
</a>
</p>
</div>
<div class="statusDisplay">
<div class="statusDisplayIcons">
</div>
</div>
<p class="firstPost light">
Von
<a href="?page=Profile&amp;User=' . $authorID . '">
' . $threads_author . '
</a>
(' . $lastThreadReplyF . ')
</p>
</td>
<td class="columnRating" align="center">
' . $thread_rating_ . '
</td>
<td class="columnReplies">
' . $threads_sub_posts . '
</td>
<td class="columnViews hot">
' . $threads_sub_views . '
</td>
<td class="columnLastPost">
<div class="containerContentSmall">
<p>
Von 
<a href="?page=Profile&amp;User=' . $lastAuthorID . '">
' . $threads_participant_last . '
</a>
</p>
<p class="smallFont light lastPost_time">
(' . $threads_sub_last_reply_F . ')
</p>
</div>
</td>
</tr>
';
}
$initLastPosts .= '
<tbody>
</table>
';

echo $initLastPosts;
?>
			</div>
		</div>
	</div>
</div>
</div>


<div class="portal_rightSidebar_outer column">
<div class="portal_rightSidebar">
	<div class="portal_userActions portal_container">
		<div class="portal_userActions_inner">
			<div class="portal_ConHeader catHeaderOuter" id="portal_cat1">
				Aktuell Online
			</div>
			<div class="portal_ConMain portal_onlineList">
				<ul style="display:none">
			<?php

$onlineList = '';

$getRanks = $db->query("SELECT id, rank_name FROM $db->table_ranks WHERE id >= 1 ORDER BY id DESC");
while ($ranks = mysqli_fetch_object($getRanks)) {
    $rankID   = $ranks->id;
    $rankName = $ranks->rank_name;
    
    $getOnlineUsers = $db->query("SELECT username, id FROM $db->table_accounts WHERE id= ANY (SELECT id FROM $db->table_sessions WHERE online='1') AND account_level=('" . $rankID . "')");
    if (mysqli_num_rows($getOnlineUsers) >= 1)
        $onlineList .= '<h3 class="team_rankList">' . $rankName . '</h3>';
    while ($onlineUsers = mysqli_fetch_object($getOnlineUsers)) {
        $onlineUsername = $onlineUsers->username;
        $onlineUserid   = $onlineUsers->id;
        
        $onlineList .= '
					<a href="?page=Profile&User=' . $onlineUserid . '">
						<li class="userFrame" id="' . $onlineUserid . '">
							' . $onlineUsername . '
						</li>
					</a>';
    }
}

echo $onlineList;
?>
				</ul>
				<?php
				$getOnlineUsers = $db->query("SELECT id FROM $db->table_accounts WHERE id= ANY (SELECT id FROM $db->table_sessions WHERE online='1')");
				$users = mysqli_num_rows($getOnlineUsers);
				
				if($users >= 1) {
				if($users == 1) $phrase='registrierter';
				if($users >= 2) $phrase='registrierte';
				echo '
				<span class="noOnlineUsers" id="portal_userlist_display">'.$users.' '.$phrase.' User online.<br>[ Mehr Informationen ]</span>';
				}
				if($users <= 0) {
				echo '
				<span class="noOnlineUsers" id="portal_userlist_display_none">Kein registrierter User online.</span>';
				}
				?>
			</div>
		</div>
	</div>
	
	<div class="portal_newUsers portal_container">
		<div class="portal_newUsers_inner">
			<div class="portal_ConHeader catHeaderOuter" id="portal_cat2">
				Geburtstage
			</div>
			<div class="portal_ConMain">
				<ul>
					<li>
					<li>
							<span class="noOnlineUsers">Diese Funktion befindet sich derzeit in Entwicklung.</span>
						</li>
				</ul>
			</div>
		</div>
	</div>
	
		<div class="portal_team portal_container">
		<div class="portal_team_inner">
			<div class="portal_ConHeader catHeaderOuter" id="portal_cat2">
				Foren Team
			</div>
			<div class="portal_ConMain">
				<ul>
			<?php

$team = '';

$getRanks = $db->query("SELECT id, rank_name FROM $db->table_ranks WHERE id >= 2 ORDER BY id DESC");
while ($ranks = mysqli_fetch_object($getRanks)) {
    $rankID   = $ranks->id;
    $rankName = $ranks->rank_name;
    
    $team .= '<h3 class="team_rankList">' . $rankName . '</h3>';
    
    $getTeam = $db->query("SELECT username, id FROM $db->table_accounts WHERE account_level = ('" . $rankID . "')");
    while ($fetch_team = mysqli_fetch_object($getTeam)) {
        $teamName = $fetch_team->username;
        $teamID   = $fetch_team->id;
        
        $team .= '
					<a href="?page=Profile&User=' . $teamID . '">
						<li class="userFrame" id="' . $teamID . '">
							' . $teamName . '
						</li>
					</a>';
    }
    
}

echo $team;
?>
				</ul>
			</div>
		</div>
	</div>
	
		<div class="portal_bestUser portal_container">
		<div class="portal_bestUser_inner">
			<div class="portal_ConHeader catHeaderOuter" id="portal_cat2">
				Die aktivsten Nutzer des Monats
			</div>
			<div class="portal_ConMain">
				<ul>
					<li>
							<span class="noOnlineUsers">Diese Funktion befindet sich derzeit in Entwicklung.</span>
						</li>
				</ul>
			</div>
		</div>
	</div>
</div>
</div>
</div>