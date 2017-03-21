<?php
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);


if (!isset($db) || $db == NULL)
{
	$db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
	$main = new Board($db, $connection);

$main->useFile('./system/classes/akb_admin.class.php');
$admin = new Admin($db, $connection);
?>

<link rel=stylesheet href="./css/akb-style-admin.css" media="screen">

<div class="adminPanel">
	<div class="adminSidebar">
		<div class="adminSidebar_header">
			<div class="graphics" id="adminSidebar_header_icon"></div>
		</div>
		<div class="adminSidebar_list">
			<ul>
				<li id="navMembers" class="active">
					<a href="#">
						<p>
							Accounts
						</p>
					</a>
				</li>
				<li id="profiledit">
					<a href="#">
						<p>
							Chat
						</p>
					</a>
				</li>
				<li id="navForum">
					<a href="#">
						<p>
							Board
						</p>
					</a>
				</li>
				<li id="thread_def">
					<a href="#">
						<p>
							Themen
						</p>
					</a>
				</li>
				<li id="refresh">
					<a href="#">
						<p>
							Sitzungen
						</p>
					</a>
				</li>
			</ul>
		</div>
	</div>
	<div class="adminContainer">
		<form method="POST" action="?page=Admin&action=editUsers" id="adminForm">
			<div class="adminNav">
				<ul>
					<li id="close">
						<a onclick="document.forms['adminForm'].submit('ban');">
							<p>
								Nutzer sperren
							</p>
						</a>
					</li>
					<li id="accept">
						<a onclick="document.forms['adminForm'].submit('grant');">
							<p>
								Nutzer freischalten
							</p>
						</a>
					</li>
					<li id="contact">
						<a onclick="document.forms['adminForm'].submit('edit');">
							<p>
								Accounts bearbeiten
							</p>
						</a>
					</li>
					<li id="remove">
						<a onclick="document.forms['adminForm'].submit('remove');">
							<p>
								Accounts entfernen
							</p>
						</a>
					</li>
				</ul>
			</div>
			<div class="adminContent">
				<div class="adminContentHeader">
				  <h2 class="fancy_font">
					Actual Setting
				  </h2>
				</div>
				<table class="akb-thead">
					<thead>
						<tr>
							<th>
							</th>
							<th>
								<div>
									Benutzername
								</div>
							</th>
							<th>
								<div>
									Avatar
								</div>
							</th>
							<th>
								<div>
									Registriert am
								</div>
							</th>
							<th>
								<div>
									Beiträge
								</div>
							</th>
							<th>
								<div>
									Hobbys
								</div>
							</th>
							<th>
								<div>
									Geburtstag
								</div>
							</th>
							<th>
								<div>
									Letzte Aktivität
								</div>
							</th>
							<th>
								<div>
									Wohnort
								</div>
							</th>
						</tr>
					</thead>
			
					<tbody>
					<?php
					$membersRow='';
					
					$getmembers = $db->query("SELECT id, registered_date FROM $db->table_accounts WHERE accepted=1 ORDER BY id ASC LIMIT 0, 30");
						while($members = mysqli_fetch_object($getmembers)) {
							$memberID = $members->id;
							$memberDate = $members->registered_date;
						
						$getuser = $db->query("SELECT username, gender, avatar, post_counter, user_title, user_rank, location FROM $db->table_accdata WHERE account_id=('".$memberID."')");
							$user = mysqli_fetch_object($getuser);
								$memberName = $user->username;
								$memberGender = $user->gender;
								$memberAvatar = $user->avatar;
								$memberPosts = $user->post_counter;
								$memberTitle = $user->user_title;
								$memberRank = $user->user_rank;
								
								
						$get_profile_data = $db->query("SELECT location, hobbies FROM $db->table_profile WHERE id=('".$memberID."')");
							if(mysqli_num_rows($get_profile_data) >= 1)
							{
								$profiledata = mysqli_fetch_object($get_profile_data);
									
									$memberLocation = $profiledata->location;
							}	
								if(!isset($memberLocation) || empty($memberLocation))
									$memberLocation = 'n.a';
							
							
						$getuserActivity = $db->query("SELECT last_activity, online FROM $db->table_sessions WHERE id=('".$memberID."')");
							while($useractivity = mysqli_fetch_object($getuserActivity)) {
								$memberActivity = $useractivity->last_activity;
								$memberOnline = $useractivity->online;
							}
							
					$memberDate 	= $main->convertTime($memberDate);
					$memberActivity = $main->convertTime($memberActivity);
					
					if($memberOnline == '0') { $userStatusImg = '<div class="icons_small" id="offline" title="'.$memberName.' ist grade offline"></div>'; }
					else { $userStatusImg = '<div class="icons_small" id="online" title="'.$memberName.' ist grade online"></div>'; }
					
					$membersRow .='
						<tr class="members_row smoothTransitionFast">
							<td class="columnCheckbox">
								<input type="checkbox" value="'.$memberID.'" name="userSelect">
							</td>
							<td class="columnUsername">
								<div class="user_onlineStatus">
									'.$userStatusImg.'
								</div>
								<div>
									<p>
										<a href="?page=Profile&User='.$memberID.'">'.$memberName.'</a>
									</p>
									<p>
										'.$memberTitle.'
									</p>
								</div>
							</td>
							<td class="columnAvatar">
								<img src="'.$memberAvatar.'">
							</td>
							<td class="columnRegistered">
								'.$memberDate.'
							</td>
							<td class="columnPosts">
								'.$memberPosts.'
							</td>
							<td class="columnHobbies">
								n.A
							</td>
							<td class="columnBirthday">
								n.A
							</td>
							<td class="columnLastActivity">
								'.$memberActivity.'
							</td>
							<td class="columnLocation">
								'.$memberLocation.'
							</td>
						</tr>';
						}
						
						echo $membersRow;
					?>
					</tbody>
				</table>
			</div>
		</form>
	</div>
</div>