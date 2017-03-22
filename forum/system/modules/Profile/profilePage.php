<?php 
// Re initialize the DB and Runtime Class
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);

	$main->UseFile('./system/interface/errorpage_cc.php');
	
	if(!isset($_SESSION) || (isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == FALSE))
	{
		ThrowError_cC('Sie haben nicht die notwendigen Berechtigungen, um auf diese Seite zuzugreifen!');
		
		return;
	}
	
if (isset($_GET['page']) && $_GET['page'] == 'Profile' && isset($_GET['User']) && !empty($_GET['User'])) {

	$secureUser = mysqli_real_escape_string($GLOBALS['connection'], $_GET['User']);
	
	if(!isset($_SESSION['lastVisitedProfile'][$secureUser]) || (isset($_SESSION['lastVisitedProfile'][$secureUser]) && (time() - $_SESSION['lastVisitedProfile'][$secureUser][0] >= 120)))
	{
      $db->query("UPDATE $db->table_accdata SET profile_views=profile_views+1 WHERE account_id=('".$secureUser."')");
	  $_SESSION['lastVisitedProfile'][$secureUser][0] = time();
	}
	

			// $getUser = $db->query("SELECT username,gender,avatar,post_counter,email,user_title,user_rank,signature,login_status FROM $db->table_accdata WHERE account_id=('".$secureUser."')");
			$userData = $main->getUserdata($secureUser, "account_id");
			if(!$userData || $userData['accepted'] == 0) {
				ThrowError_cC('Der gesuchte User existiert nicht!');
			}
			else
			{
			// if(mysqli_num_rows($userData) > 1) ThrowError_cC('Ungültiger Link! Bitte überprüfen Sie die Schreibweise der URL oder versuchen Sie es später erneut. Sollte dieser Fehler weiterhin auftreten, wenden Sie sich an die Administration.');
				
					$accountID    		= $userData['account_id'];
					$username 			= $userData['name'];
					$avatar  			= $userData['avatar'];
					$avatarBorder 		= $userData['avatar_border'];
					$gender 			= $userData['gender'];
					$post_counter 		= $userData['posts'];
					$email 				= $userData['email'];
					$user_title 		= $userData['title'];
					$signature 			= $userData['signature'];
					$character_name 	= $userData['character_name'];
					$character_realm 	= $userData['character_realm'];
					$accLevel_icon 		= '';
					$rank 				= $main->calculateRank(0, 0, $accountID);
					
					$armory_link = $main->buildArmoryLink($character_realm, $character_name);
					// $accLevel_icon 	= '<img src="./images/icons/ranks/rank1.png" width="15" height="22">';
				
				$getuserActivity = $db->query("SELECT online FROM $db->table_sessions WHERE id=('".$secureUser."')");
						$useractivity = mysqli_fetch_object($getuserActivity);
							$memberOnline = $useractivity->online;
						
						$userStatusImg = $main->buildOnlineStatus($memberOnline, $username);
				
				$getProfile = $db->query("SELECT location, hobbies, about, msngr_skype, msngr_icq, sn_facebook, sn_twitter, sn_googleplus, sn_tumblr FROM $db->table_profile WHERE id=('".$secureUser."') LIMIT 1");
					$profile = mysqli_fetch_object($getProfile);
						$location 		= $profile->location;
						$hobbies 		= $profile->hobbies;
						$about 			= $profile->about;
						$msngr_skype 	= $profile->msngr_skype;
						$msngr_icq 		= $profile->msngr_icq;
						$sn_facebook 	= $profile->sn_facebook;
						$sn_twitter 	= $profile->sn_twitter;
						$sn_googleplus 	= $profile->sn_googleplus;
						$sn_tumblr 		= $profile->sn_tumblr;
			
			
			if(empty($about) || $about == '')
				$about = '<span class="profile_about_no_informations">Dieser Nutzer hat noch keine Informationen über sich angegeben.</span>';
		
		if(isset($msngr_skype) && $msngr_skype != NULL) {
			$messenger_skype = 'Skype: '.$msngr_skype.'<br><a href="skype:'.$msngr_skype.'?userinfo" title="Profil aufrufen"><div class="icons_small" id="skype"></div></a>';
		} else {
			if(!isset($msngr_skype) || empty($msngr_skype))
			{
				$messenger_skype = '';
			}
		}
		
		
		switch($gender) {
				
				case 1:
					$authorGenderImg = './images/icons/undefinedGender.png';
					$authorGenderText = ' hat noch kein Geschlecht angegeben.';
					break;
				case 2:
					$authorGenderImg = './images/icons/female.png';
					$authorGenderText = ' ist Weiblich.';
					break;
				case 3:
					$authorGenderImg = './images/icons/male.png';
					$authorGenderText = ' ist Männlich.';
					break;
				}
				
				
	$userDataString = '

	<div class="profileMainContainer">
	  <div class="mainHeadline">
		<div class="headlineContainer">
			'.$armory_link.'
		  <h2>
			Profil von »'.$username.'«
		  </h2>
		</div>
	  </div>
	  <div class="profileMain">
		<div class="userInformation">
		  <ul>
			<li class="userAvatar_profile">
				<img src="' . $avatar . '"  height="120px" class="UserImage user_avatar_global_border img-zoom" style="border:5px solid rgba('.$avatarBorder.')">
			</li>
			<li class="DetailUserInfo">
				<div class="userCredits">
					<div>
						'.$userStatusImg.'
						'.$username.'
					</div>
					<div>
						'.$user_title.'
					</div>
					<div class="userRank" title="'.$rank[2].', Rang '.$rank[0].'">
						'.$rank[1].'
					</div>
					<div>
						<img src="'.$authorGenderImg.'" title="'.$username.' '.$authorGenderText.'">
					</div>
					<div>
						'.$messenger_skype.'
					</div>
				</div>
			</li>
		  </ul>
		</div>
		<div class="Detailed_userInformation">
			<div class="Detailed_userInformation_header">
				Über '.$username.'
			</div>
			<div class="Detailed_userInformation_content">
				'.$about.'
			</div>
		</div>
	  </div>
	</div>';

	echo $userDataString;
			}
}
?>