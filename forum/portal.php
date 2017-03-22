<?php

global $langGlobal;

// Re initialize the DB and Runtime Class
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);

if (!isset($survey) || $survey == NULL)
{
	$main->useFile('./system/classes/akb_survey.class.php', 1);
    $survey = new Survey($db, $connection, $main);
}

	$survey->initializeSurvey($main->serverConfig('active_survey'));
	//$surveyResult = $survey->outputSurvey();
	$surveyTitle = $survey->name;
	$surveyDescription = $survey->description;
	$surveyData = $survey->html;

	if(isset($_GET['action']) && ($_GET['action'] == 'submitToSurvey' || $_GET['action'] == 'submitToSurveyReverse') && isset($_POST['verify']) && $_POST['verify'] == '12098')
	{
		$main->useFile('./system/controller/processors/survey_processor.php');
	}



if (!isset($_COOKIE['akb_last_visit'])) {
    echo '
<div class="userInfobox">
	<div class="userInfobox_inner">
		<div class="userInfobox_img">
			<img src="./images/3Dart/information.png">
		</div>
		<div>
			'.$langGlobal['portal_lang_welcome'].'
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

echo '
	<div class="portal_base">
	<div class="portal_leftSidebar_outer column">
	<div class="portal_leftSidebar">
		<div class="portal_userActions portal_container">
			<div class="portal_userActions_inner">
				<div class="portal_ConHeader fancy_font" id="portal_cat1">
					'.$langGlobal['about_you'].'
				</div>
				<div class="portal_ConMain">';		


if((isset($_GET['action']) && $_GET['action'] == 'validuser') && (isset($_GET['token']) && !empty($_GET['token']) && strlen(trim($_GET['token'])) == 64))
{
	$token = addslashes($_GET['token']);
	$token = mysqli_real_escape_string($connection, $token);
	
	$token_state = $db->query("SELECT uid, token FROM ".$db->table_account_token." WHERE token='".$token."'");
	if(mysqli_num_rows($token_state) >= 1)
	{
		// If token exists
		
		// Unlock user
			// Get UID
				$data = mysqli_fetch_object($token_state);
				$uid = $data->uid;
			
			// Execute query
				$validate = $db->query("UPDATE ".$db->table_accounts." SET accepted=1 WHERE id='".$uid."'");
				$db->query("DELETE FROM ".$db->table_account_token." WHERE token='".$token."'");
				
				if($validate)
					echo '
						<div class="screen_note">
							<div>
								<p>
									'.$langGlobal['account_verification_success'].'
								</p>
								<p>
									<a href="?page=Portal">WEITER</a>
								</p>
							</div>
						</div>
					';
	}
}

if (!isset($_SESSION['STATUS']) || $_SESSION['STATUS'] == false) {
    echo '
		<ul class="portal_infoList">
			<li class="visitorPanel">
				<h3 class="portal_information_container">'.$langGlobal['portal_lang_functions'].'</h3>
				<ul>
				 <a href="?page=Login">
					<li id="openLogout" title="'.$langGlobal['sPLogin'].'">
						<div class="img_noRepeat img_20x20" id="login"></div>
						'.$langGlobal['sPLogin'].'
					</li>
				 </a>
				 <a href="?page=Register">
					<li title="'.$langGlobal['sPRegister'].'">
						<div class="img_noRepeat img_20x20" id="security"></div>
						'.$langGlobal['sPRegister'].'
					</li>
				 </a>
				 <a href="?page=Index&threadID=2">
					<li title="'.$langGlobal['sPHelp'].'">
						<div class="img_noRepeat img_20x20" id="userInfobox_img"></div>
						'.$langGlobal['sPHelp'].'
					</li>
				 </a>
				</ul>
			</li>
		</ul>';
} elseif (isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == true) {
    
    $userActionBar = '';
    
    $userData = $main->getUserdata($_SESSION['ID'], "sid");
	
        $id           			= $userData['account_id'];
        $username     			= $userData['name'];
        $avatar       			= $userData['avatar'];
		$memberAvatar_border 	= $userData['avatar_border'];
        $post_counter 			= $userData['posts'];
		$profile_views			= $userData['profile_views'];
        
    
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
<a href="?page=Profile&amp;User=' . $id . '" title="Profil von '.$username.' aufrufen.">
<span>
' . $username . '
</span>
</a>
</p>
</div>
<div class="userAvatar">
<div class="UserAvatarMsg">
<img src="' . $avatar . '" class="'.$pngClass.' img-zoom" style="border:5px solid rgba('.$memberAvatar_border.')">
</div>
</div>
<div class="userMessenger">
</div>
</div>
		<ul class="portal_infoList">
			<li>
				<h3 class="portal_information_container">'.$langGlobal["string_informations"].'</h3>
				<ul>
				  <li title="'.$registered.'">
					<div class="img_noRepeat img_20x20" id="profiledit"></div>
					' . $langGlobal["portal_lang_registered_at"] . $registered . '
				  </li>
				  <li title="'.$profile_views.'">
					<div class="img_noRepeat img_20x20" id="contact"></div>
					' . $langGlobal["portal_lang_profile_views"] . $profile_views . '
				  </li>
				  <li title="'.$post_counter.'">
					<div class="img_noRepeat img_20x20" id="thread_def"></div>
					' . $langGlobal["portal_lang_profile_posts"] . $post_counter . '
				  </li>
				  <li title="n.A">
					<div class="img_noRepeat img_20x20" id="thread_new"></div>
					' . $langGlobal["portal_lang_profile_activity"].'n.A
				  </li>
				  <li title="'.$client_ip.'">
					<div class="img_noRepeat img_20x20" id="network"></div>
					' . $langGlobal["portal_lang_profile_ip"] . $client_ip . '
				  </li>
				</ul>
			</li>
			<li>
				<h3 class="portal_information_container">'.$langGlobal['portal_lang_functions'].'</h3>
				<ul>
				 <a href>
					<li id="openLogout" title="'.$langGlobal['sPLogout'].'">
					<div class="img_noRepeat img_20x20" id="close"></div>
					'.$langGlobal['sPLogout'].'
					</li>
				 </a>
				 <a href="?page=Profile&Tab=Edit">
					<li title="Profil '.$langGlobal['sPProfileEdit'].'">
					<div class="img_noRepeat img_20x20" id="profiledit"></div>
					'.$langGlobal['sPProfileEdit'].'
					</li>
				 </a>
				 <a href="?page=Message&Tab=Inbox">
					<li title="'.$langGlobal['sPMessages'].'">
					<div class="img_noRepeat img_20x20" id="messages"></div>
					'.$langGlobal['sPMessages'].'
					</li>
				 </a>
				 <a href="?page=Account&Tab=Edit">
					<li title="'.$langGlobal['sPControlCenter'].'">
					<div class="img_noRepeat img_20x20" id="accountpanel"></div>
					'.$langGlobal['sPControlCenter'].'
					</li>
				 </a>
				</ul>
			</li>
		</ul>
					';
    
    echo $userActionBar;
}

echo '
					
			</div>
		</div>
	</div>
	
	<div class="portal_newUsers portal_container">
		<div class="portal_newUsers_inner">
			<div class="portal_ConHeader fancy_font" id="portal_cat2">
				'.$langGlobal['portal_lang_new_members'].'
			</div>
			<div class="portal_ConMain">
				<ul>';
				
				
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
if (isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == true) {
    echo '
		<div class="portal_userActions portal_container">
		<div class="portal_userActions_inner">
			<div class="portal_ConHeader fancy_font" id="portal_cat1">
				'.$langGlobal['portal_lang_friends'].'
			</div>
			<div class="portal_ConMain">
				<ul>
				 
					<li>
							<span class="noOnlineUsers">'.$langGlobal['function_in_development'].'</span>
						</li>
				 
				</ul>
			</div>
		</div>
	</div>
	';
	
	$survey = '
	<div class="portal_userActions_inner akb_survey">
		<div class="portal_ConHeader fancy_font" id="portal_cat1">
			'.$langGlobal['portal_survey_headline'].'
		</div>
		<div class="survey_main">
			<h3>'.$surveyTitle.'</h3>
			<p>'.$surveyDescription.'</p>
			'.$surveyData.'
		</div>
	</div>
	';
}
else
	$survey = '';

echo '
</div>
</div>

<div class="portal_center column">
<div class="portal_userActions portal_container">
	'.$survey.'
   <div class="portal_userActions_inner">
      <div class="portal_ConHeader fancy_font" id="portal_cat1">
         '.$langGlobal['portal_lang_news'].'
      </div>
      <div class="portal_ConMain">
         <div class="portal_news">';

										
				$getNewsID = $db->query("SELECT news_id FROM $db->table_portal_news LIMIT 1");
					$newsID = mysqli_fetch_object($getNewsID);
						$id = $newsID->news_id;
					
					
			if(isset($id))
			{
				$getNewsContent = $db->query("SELECT title, date_created, author_id FROM $db->table_thread WHERE id=('".$id."') ORDER BY id DESC LIMIT 1");
					while($newsContent = mysqli_fetch_object($getNewsContent)) {
						$title = $newsContent->title;
						$date_created = $newsContent->date_created;
						$author_id = $newsContent->author_id;
					}
					
					if(isset($date_created))
						$date_created = $main->convertTime($date_created);
				
					if(isset($author_id))
					{
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
						<div class="portal_newsheader_right">
							<h3 class="portal_news_subheader"><div class="icons" id="newsicon"></div>'.$title.'</h3>
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
								'.$langGlobal['reply'].'
							</span>
						</a>
						</li>
					</ul>
					</div>';
					
					
					echo $postcontent;
				}
				else
				{
					echo '<p class="empty_newsticker">'.$langGlobal['no_thread_display'].' <img src="./images/emoticons/Smiley12.png"></p>';
				}
			} else echo '<p class="empty_newsticker">'.$langGlobal['no_thread_display'].' <img src="./images/emoticons/Smiley12.png"></p>';
			?>
      </div>
   </div>
</div>
	
	<?php
if (isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == true) {
    
    require('./system/interface/ajaxChat.php');
    
}

	echo '
	<div class="portal_userActions portal_container">
		<div class="portal_userActions_inner">
			<div class="portal_ConHeader fancy_font" id="portal_cat1">
				'.$langGlobal['most_recent_replies'].'
			</div>
			<div class="portal_ConMain">';

	$getLatestPosts = $db->query("SELECT id, main_forum_id, title, date_created, last_replyTime, author_id, last_post_author_id, rating, rating_votes, posts, views FROM $db->table_thread WHERE id ORDER BY last_replyTime DESC LIMIT 10");
	if(mysqli_num_rows($getLatestPosts) >= 1)
	{

		$initLastPosts = '
						<table class="tableList">
		<thead>
		<tr class="tableHead">
		<th colspan="2">
		<div>
		<a href="#" disabled="disabled">
		'.$langGlobal['thread'].'		  	  
		<div class="lP_descSort">
		</div>

		</a>
		</div>
		</th>
		<th>
		<div>
		<a href="#" disabled="disabled">
		'.$langGlobal['thread_rating'].'	  
		<div class="lP_descSort">
		</div>

		</a>
		</div>
		</th>
		<th>
		<div>
		<a href="#" disabled="disabled">
		'.$langGlobal['thread_replies'].'	  	  
		<div class="lP_descSort">
		</div>

		</a>
		</div>
		</th>
		<th>
		<div>
		<a href="#" disabled="disabled">
		'.$langGlobal['thread_views'].'
		<div class="lP_descSort">
		</div>

		</a>
		</div>
		</th>
		<th>
		<div>
		<a href="#" disabled="disabled">
		'.$langGlobal['thread_last_reply'].' 
		<div class="lP_descSort">
		</div>

		</a>
		</div>
		</th>
		</tr>
		</thead>
		<tbody>';

		while ($latestPosts = mysqli_fetch_object($getLatestPosts)) {
			$latestID				= $latestPosts->id;
			$latestMainID			= $latestPosts->main_forum_id;
			$latestTitle			= $latestPosts->title;
			$latestUrl				= $main->buildThreadUrl($latestID,$latestTitle);
			$latestCreated			= $latestPosts->date_created;
			$lastReply				= $latestPosts->last_replyTime;
			$authorID				= $latestPosts->author_id;
			$lastAuthorID			= $latestPosts->last_post_author_id;
			$threads_rating			= $latestPosts->last_post_author_id;
			$threads_rating_votes	= $latestPosts->last_post_author_id;
			$threads_sub_views		= $latestPosts->views;
			$threads_sub_posts		= $latestPosts->posts;
			
			$latestCreated = $main->convertTime($latestCreated);
			$lastReply = $main->convertTime($lastReply);
			
			if($main->checkBoardPermission($latestMainID, 1) == false)
				continue;
			
			
			$get_author    = "SELECT username FROM $db->table_accdata WHERE account_id='" . $authorID . "'";
			$author_result = $db->query($get_author);
			$threads_author_fetch = mysqli_fetch_object($author_result);
				$threads_author = $threads_author_fetch->username;
			
			if($lastAuthorID != 0)
			{
				$get_last_author    = "SELECT username FROM $db->table_accdata WHERE account_id='" . $lastAuthorID . "'";
				$last_author_result = $db->query($get_last_author);
				$last_author_fetch = mysqli_fetch_object($last_author_result);
					$threads_participant_last = $last_author_fetch->username;
			}
			else
				$threads_participant_last = 'Gast';
			
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
				
				if (isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == true) {
					
					$_SESSION['ID'] = session_id();
					$threadID       = $latestID;
					
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
				<div class="threadicons" id="thread_def"></div>
				</td>
				<td class="columnTopic">
				<div class="topic">
				<p>
				<span class="Title">
				<strong>
				</strong>
				</span>
				<a href="'.$latestUrl.'" title="Zum Thema '.$latestTitle.' springen">
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
				(' . $latestCreated . ')
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
				(' . $lastReply . ')
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
	}
	else
	{
		echo '<p class="empty_newsticker">'.$langGlobal['no_thread_display'].' <img src="./images/emoticons/Smiley12.png"></p>';
	}
?>
			</div>
		</div>
	</div>
</div>
</div>

</div>