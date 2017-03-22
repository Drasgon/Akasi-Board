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


// Re initialize the DB and Runtime Class
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);
?>
<div class="membersMainContainer">
  <div class="mainHeadline">
    <div class="icons" id="forumiconMain"></div>
    <div class="headlineContainer">
      <h1>
        Mitglieder
      </h1>
    </div>
	<p>Sämtliche Mitglieder, welche bereits von der Administration zur Foren-Nutzung autorisiert wurden!</p>
</div>
  <div class="membersMain">
  
    <table>
		<thead>
			<tr>
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
			
			$getuser = $main->getUserdata($memberID, "account_id");
					$memberName = $getuser['name'];
					$memberGender = $getuser['gender'];
					$memberAvatar = $main->checkUserAvatar($getuser['avatar']);
					$memberAvatar_border = $getuser['avatar_border'];
					$memberPosts = $getuser['posts'];
					$memberTitle = $getuser['title'];
					$memberRank = $getuser['rank'];
					
					$level_bg = '';
					
					if($main->getAccountSecurity($memberID) == 2)
					{
						$level_bg = 'accLevelBackgroundBlue';
					}
					if($main->getAccountSecurity($memberID) == 3)
					{
						$level_bg = 'accLevelBackgroundRed';
					}
					
					
			$get_profile_data = $db->query("SELECT location, hobbies FROM $db->table_profile WHERE id=('".$memberID."')");
				if(mysqli_num_rows($get_profile_data) >= 1)
				{
					$profiledata = mysqli_fetch_object($get_profile_data);
						
						$memberLocation = $profiledata->location;
				}	
					if(!isset($memberLocation) || empty($memberLocation))
						$memberLocation = 'n.a';
				
				
			$getuserActivity = $db->query("SELECT last_activity, online FROM $db->table_sessions WHERE id=('".$memberID."')");
				$useractivity = mysqli_fetch_object($getuserActivity);
					$memberActivity = $useractivity->last_activity;
					$memberOnline = $useractivity->online;
				
		$memberDate 	= $main->convertTime($memberDate);
		$memberActivity = $main->convertTime($memberActivity);
		
		if($memberOnline == '0') { $userStatusImg = '<div class="red_circle_small" title="'.$memberName.' ist grade offline"></div>'; }
		else { $userStatusImg = '<div class="green_circle_small" title="'.$memberName.' ist grade online"></div>'; }
		
		$membersRow .='
			<tr class="members_row smoothTransitionFast '.$level_bg.'">
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
					<img src="'.$memberAvatar.'" class="user_avatar_global_border img-zoom" style="border:5px solid rgba('.$memberAvatar_border.')!important">
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
</div>