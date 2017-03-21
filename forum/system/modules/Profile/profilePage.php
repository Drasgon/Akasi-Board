<?php 
// Re initialize the DB and Runtime Class
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);
	
if (isset($_GET['page']) && $_GET['page'] == 'Profile' && isset($_GET['User']) && !empty($_GET['User'])) {

	$secureUser = mysqli_real_escape_string($GLOBALS['connection'], $_GET['User']);
	
	if(!isset($_SESSION['lastVisitedProfile'][$secureUser]) || (isset($_SESSION['lastVisitedProfile'][$secureUser]) && (time() - $_SESSION['lastVisitedProfile'][$secureUser][0] >= 120)))
	{
      $db->query("UPDATE $db->table_accdata SET profile_views=profile_views+1 WHERE account_id=('".$secureUser."')");
	  $_SESSION['lastVisitedProfile'][$secureUser][0] = time();
	}
	

			$getUser = $db->query("SELECT username,gender,avatar,post_counter,email,user_title,user_rank,signature,login_status FROM $db->table_accdata WHERE account_id=('".$secureUser."')");
			if(mysqli_num_rows($getUser) < 1) {
				require('./system/interface/errorpage_cC.php');
				ThrowError_cC('Der gesuchte User existiert nicht!');
				}
			if(mysqli_num_rows($getUser) > 1) ThrowError_cC('Ungültiger Link! Bitte überprüfen Sie die Schreibweise der URL oder versuchen Sie es später erneut. Sollte dieser Fehler weiterhin auftreten, wenden Sie sich an die Administration.');
			if(mysqli_num_rows($getUser) == 1) {
				$catchData = mysqli_fetch_object($getUser);
					$username = $catchData->username;
					$gender = $catchData->gender;
					$avatar = $catchData->avatar;
					$post_counter = $catchData->post_counter;
					$email = $catchData->email;
					$user_title = $catchData->user_title;
					$signature = $catchData->signature;
					$login_status = $catchData->login_status;
					$accLevel_icon = './images/icons/ranks/rank1.png';
				
				
				$getuserActivity = $db->query("SELECT online FROM $db->table_sessions WHERE id=('".$secureUser."')");
						$useractivity = mysqli_fetch_object($getuserActivity);
							$memberOnline = $useractivity->online;
						
						if($memberOnline == '0')
							$userStatusImg = '<div class="icons_small" id="offline" title="'.$username.' ist grade offline"></div>';
						else
							$userStatusImg = '<div class="icons_small" id="online" title="'.$username.' ist grade online"></div>';
				
				$getProfile = $db->query("SELECT location, hobbies, about, msngr_skype, msngr_icq, sn_facebook, sn_twitter, sn_googleplus, sn_tumblr FROM $db->table_profile WHERE id=('".$secureUser."') LIMIT 1");
					$profile = mysqli_fetch_object($getProfile);
						$location = $profile->location;
						$hobbies = $profile->hobbies;
						$about = $profile->about;
						$msngr_skype = $profile->msngr_skype;
						$msngr_icq = $profile->msngr_icq;
						$sn_facebook = $profile->sn_facebook;
						$sn_twitter = $profile->sn_twitter;
						$sn_googleplus = $profile->sn_googleplus;
						$sn_tumblr = $profile->sn_tumblr;
			}
			
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
					break;
				case 2:
					$authorGenderImg = './images/icons/female.png';
					break;
				case 3:
					$authorGenderImg = './images/icons/male.png';
					break;
				}
				
				
	$userDataString = '

	<div class="profileMainContainer">
	  <div class="mainHeadline">
		<div class="headlineContainer">
		  <h2>
			Profil von »'.$username.'«
		  </h2>
		</div>
	  </div>
	  <div class="profileMain">
		<div class="userInformation">
		  <ul>
			<li class="userAvatar_profile">
				<img src="'.$avatar.'" width=150>
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
					<div>
						<img src="'.$accLevel_icon.'" width="15" height="22">
					</div>
					<div>
						<img src="'.$authorGenderImg.'">
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
?>