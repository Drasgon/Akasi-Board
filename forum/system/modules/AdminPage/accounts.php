<?php
/*
Copyright (C) 2016  Alexander Bretzke

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

require_once(dirname(__FILE__).'/../../security/callstack_validation.php');

if (!isset($db) || $db == NULL)
{
	$db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
	$main = new Board($db, $connection);

if (!isset($admin) || $admin == NULL)
	$admin = new Admin($db, $connection);

if(!$main->checkSessionAccess('MOD'))
	exit();
?>


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
									Letzte Aktivität
								</div>
							</th>
							<th>
								<div>
									Wohnort
								</div>
							</th>
							<th>
								<div>
									Freigeschaltet
								</div>
							</th>
						</tr>
					</thead>
			
					<tbody>
					<?php
					$membersRow='';
					
					$getmembers = $db->query("SELECT id, registered_date, accepted FROM $db->table_accounts WHERE id ORDER BY id ASC");
						while($members = mysqli_fetch_object($getmembers)) {
							$memberID = $members->id;
							$memberDate = $members->registered_date;
							$memberAccepted = $members->accepted;
							
							if($memberAccepted == 0)
								$memberAccepted = '<div class="red_circle_small"></div>';
							else if ($memberAccepted == 1)
								$memberAccepted = '<div class="green_circle_small"></div>';
						
						$getuser = $main->getUserdata($memberID, "account_id");
							$memberName = $getuser['name'];
							$memberGender = $getuser['gender'];
							$memberAvatar = $main->checkUserAvatar($getuser['avatar']);
							$memberAvatar_border = $getuser['avatar_border'];
							$memberPosts = $getuser['posts'];
							$memberTitle = $getuser['title'];
							$memberRank = $getuser['rank'];
								
								
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
							<td class="columnAvatar user_avatar_global_border" style="border:5px solid rgba('.$memberAvatar_border.')">
								<img src="'.$memberAvatar.'" class="img-zoom" data-zoom=3>
							</td>
							<td class="columnRegistered">
								'.$memberDate.'
							</td>
							<td class="columnPosts">
								'.$memberPosts.'
							</td>
							<td class="columnLastActivity">
								'.$memberActivity.'
							</td>
							<td class="columnLocation">
								'.$memberLocation.'
							</td>
							<td class="columnAccepted">
								'.$memberAccepted.'
							</td>
						</tr>';
						}
						
						echo $membersRow;
					?>
					</tbody>
				</table>
			</div>
		</form>