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

	ini_set('display_startup_errors',1);
	ini_set('display_errors',1);
	error_reporting(-1);
	
	
	include('includes/classes/botl.mysqli.class.php');
	include('includes/classes/botl.error.class.php');
	include('includes/classes/botl.armory.class.php');
	include('includes/modules/hex2rgba.php');
	include('includes/modules/convertTime.php');
	
		
		if (!isset($error) || $error == NULL)
		{
			$error = NEW ErrorHandler();
		}
		if (!isset($db) || $db == NULL)
		{
			$db = NEW Database();
			$connection = $db->mysqli_db_connect();
		}
		if (!isset($armory) || $armory == NULL)
		{
			$armory = NEW Armory($db, $connection, $error);
		}
	
	
	$current_page = (isset($_GET['pageIndex']) && !empty($_GET['pageIndex']) && $_GET['pageIndex'] > 0) ? mysqli_real_escape_string($connection, $_GET['pageIndex']) : 1;
	$results_per_page = 30;
	$pages_to_list = 5;
	
	$data_type = ARRAY(
		'members' => 1
	);
	$table_rows = ARRAY(
		'id',
		'name',
		'gender',
		'race',
		'class',
		'level',
		'avatar',
		'acmpoints',
		'rank'
	);
	
	$timestamp = $armory->get_cache_time('members');
	$timestamp_type = 1;
	
	if($armory->check_cache_time('members'))
	{
	
		$json = $armory->renew_database_store('members');

		if(isset($json) && $json)
		{
			foreach($json->members as $object => $character)
			{
				$data_row = $character->character;
				
				$name = addslashes($data_row->name);
				$level = $data_row->level;
				$race = $data_row->race;
				$class = $data_row->class;
				$gender = $data_row->gender;
				$achievementPoints = $data_row->achievementPoints;
				$avatar = str_replace("avatar", "profilemain", addslashes($data_row->thumbnail));
				$rank = $character->rank;
				$realm = addslashes($data_row->realm);
				
				$db->query("INSERT INTO ".$db->table_guild_members." (name, gender, race, class, level, avatar, acmpoints, rank, realm) VALUES ('".$name."', '".$gender."', '".$race."', '".$class."', '".$level."', '".$avatar."', '".$achievementPoints."', '".$rank."', '".$realm."')");
				
				$timestamp = "Gerade eben";
				$timestamp_type = 2;
			}
		}
		else
			$error->throwNote("Es konnten keine Daten aus dem Battle.net gelesen werden!");
	}
	
	if($timestamp_type != 2)
		$timestamp = convertTime($timestamp);

	$selected = 'selected';
	$not_selected = '';
	
	/*----------------------------*/
	 /*   ORDER SYSTEM BEGIN     */
	/*----------------------------*/
	
			$nameDir = (isset($_GET['sortBy']) && $_GET['sortBy'] == 'name') ?  $selected : $not_selected;
			$genderDir = (isset($_GET['sortBy']) && $_GET['sortBy'] == 'gender') ?  $selected : $not_selected;
			$raceDir = (isset($_GET['sortBy']) && $_GET['sortBy'] == 'race') ?  $selected : $not_selected;
			$classDir = (isset($_GET['sortBy']) && $_GET['sortBy'] == 'class') ?  $selected : $not_selected;
			$levelDir = (isset($_GET['sortBy']) && $_GET['sortBy'] == 'level') ?  $selected : $not_selected;
			$avatarDir = (isset($_GET['sortBy']) && $_GET['sortBy'] == 'avatar') ?  $selected : $not_selected;
			$acmDir = (isset($_GET['sortBy']) && $_GET['sortBy'] == 'acmpoints') ?  $selected : $not_selected;
			$realmDir = (isset($_GET['sortBy']) && $_GET['sortBy'] == 'realm') ?  $selected : $not_selected;
			$rankDir = (isset($_GET['sortBy']) && $_GET['sortBy'] == 'rank') ?  $selected : $not_selected;
			
			if(isset($_GET['invert_sort']) && !empty($_GET['invert_sort']) && $_GET['invert_sort'] == 'on')
			{
				$invert_value = 'checked';
			}
			else
			{
				$invert_value = '';
			}
	
	/*----------------------------*/
	 /*   ORDER SYSTEM END     */
	/*----------------------------*/
	
	/*----------------------------*/
	 /*   SEARCH SYSTEM BEGIN   */
	/*----------------------------*/
	
	
			$search_key_raw = (isset($_GET['q']) && !empty($_GET['q'])) ? mysqli_real_escape_string($connection,$_GET['q']) : '';

				$search_key = preg_replace('/[^a-zA-Z 0-9]/', '', $search_key_raw);
				$search_key = preg_replace('/[a-z]+|[0-9]+/', '\0 ', $search_key);
				$search_key = preg_replace('/\s\s+/', ' ', $search_key);
				$search_key_last = trim($search_key);
				 
				$search_key = explode(" ",$search_key_last);
				 
				$such_query = "";
										for($a=1; $a<=4; $a++)
										{
											for($i=0; $i<count($search_key); $i++)
												{
														$such_query .= $table_rows[$a]." LIKE CONCAT('%".mysqli_real_escape_string($connection, $search_key[$i])."%')";
				 
														if($i<count($search_key)-1)
															{$such_query .= " AND ";}
												}
											if($a <= 3)
												{$such_query .= " OR ";}
										}
					
				$sortBy = (isset($_GET['sortBy']) && !empty($_GET['sortBy'])) ? mysqli_real_escape_string($connection, $_GET['sortBy']) : 'id';
				$direction = (isset($_GET['invert_sort']) && !empty($_GET['invert_sort']) && $_GET['invert_sort'] == 0) ? 'DESC' : 'ASC';
				$direction = strtoupper($direction);
					
				if(isset($_GET['q']) && !empty($_GET['q']))
					$search_string = $such_query;
				else
					$search_string = $table_rows[0];
				
				$current_offset_first = ($current_page > 0) ? $current_page - 1 : $current_page;
				$current_offset_last = $current_offset_first * $results_per_page;
				$limit_string = (isset($_GET['page'])) ? ' LIMIT '.$results_per_page.' OFFSET '.$current_offset_last : '';
				
				$fields = 'name, gender, race, class, level, avatar, acmpoints, rank, realm';
				$getMembersRaw = $armory->read_database_store('members', $fields, $search_string, $sortBy, $direction);
				$getMembers = $armory->read_database_store('members', $fields, $search_string, $sortBy, $direction, $limit_string);
				
				
				$result_count = $db->get_max_possible_results($getMembers, TRUE);
				$max_pages_possible_results = $db->get_max_possible_results($getMembersRaw, TRUE);
				$max_pages_possible = ceil(($max_pages_possible_results) / $results_per_page);
				
				$search_string_formatted = (!empty($search_key_last)) ? '&q='.$search_key_last : '';
				$search_msg = (!empty($search_key_last)) ? 'Die Suche nach <span class="action_result_data">"'.$search_key_last.'"</span> ergab '.$max_pages_possible_results.' Treffer ('.$result_count.' angezeigt)' : 'Ergebnisse: '.$max_pages_possible_results.' ('.($current_offset_first * $results_per_page).' bis '.(($current_offset_first * $results_per_page) + $result_count).')';
		
		/*----------------------------*/
		 /*   SEARCH SYSTEM END     */
		/*----------------------------*/
		
		
		 /*----------------------------*/
		  /*     PAGE SYSTEM BEGIN    */
		 /*----------------------------*/
		 
				$min_page_number = (round(($current_page - ($pages_to_list / 2)) <= 0)) ? '1' : round(($current_page - ($pages_to_list / 2)));
				$max_page_number = ($results_per_page * $current_page < $max_pages_possible) ? round(($current_page + ($pages_to_list / 2))) : $max_pages_possible;
				$iterations = $max_page_number - $min_page_number;
				$page_count = $min_page_number;
				
				$page_html = '<div class="page_navigation clearfix">
								<ul>';
								
				for($i=0; $i<=$iterations; $i++)
				{
					if($page_count == $current_page)
					{
						$class = ' class="active"';
						$link = '';
					}
					else
					{
						$class = '';
						$additional_param = '';
						if(isset($_GET['sortBy']))
							$additional_param .= '&sortBy='.$_GET['sortBy'];
						if(isset($_GET['invert_sort']))
							$additional_param .= '&invert_sort='.$_GET['invert_sort'];
							
						$link = ' href="?page=members&pageIndex='.$page_count.$search_string_formatted.$additional_param.'"';
					}
				
					$page_html .= '
						<a'.$link.'>
							<li'.$class.'>
								'.$page_count.'
							</li>
						</a>
					';
					
					$page_count++;
				}
				
				$page_html .= '
								</ul>
							</div>';
		
		 /*----------------------------*/
		  /*     PAGE SYSTEM END     */
		 /*----------------------------*/
	
	
	
	$html = '';
	
	$html .= '
		<span class="members_last_update fancy_font">
			Zuletzt aktualisiert: '.$timestamp.'<br>
		</span>
		<span class="fancy_font tiny_font members_last_update_note">
			(Wird jede Stunde aktualisiert)
		</span>
		<p class="search_count action_result clearfix">'.$search_msg.'</p>
		<div class="member_search clearfix">
			<form action="?page=members&action=search" method="GET">
				<input type="hidden" value="members" name="page">
				<input type="text" name="q" value="'.$search_key_raw.'" placeholder="Mitgliedersuche">
				<input type="submit">
			</form>
		</div>
				'.$page_html;
				
		if(mysqli_num_rows($getMembers) >= 1)
		{
			$html .= '
			<div class="members">
				<form>
					<input type="hidden" name="page" value="members">
					<select name="sortBy" onchange="this.form.submit()">
							<option value="name" '.$nameDir.'>
								<div>
									<a href="?page=members&sortBy=name&direction='.$nameDir.'">
										Name
									</a>
								</div>
							</option>
							<option value="gender" '.$genderDir.'>
								<div>
									<a href="?page=members&sortBy=gender&direction='.$genderDir.'">
										Geschlecht
									</a>
								</div>
							</option>	
							<option value="race" '.$raceDir.'>
								<div>
									<a href="?page=members&sortBy=race&direction='.$raceDir.'">
										Rasse
									</a>
								</div>
							</option>
							<option value="class" '.$classDir.'>
								<div>
									<a href="?page=members&sortBy=class&direction='.$classDir.'">
										Klasse
									</a>
								</div>
							</option>
							<option value="level" '.$levelDir.'>
								<div>
									<a href="?page=members&sortBy=level&direction='.$levelDir.'">
										Level
									</a>
								</div>
							</option>
							<option value="avatar" '.$avatarDir.'>
								<div>
									<a href="?page=members&sortBy=avatar&direction='.$avatarDir.'">
										Avatar
									</a>
								</div>
							</option>
							<option value="acmpoints" '.$acmDir.'>
								<div>
									<a href="?page=members&sortBy=acmpoints&direction='.$acmDir.'">
										Erfolgspunkte
									</a>
								</div>
							</option>
							<option value="realm" '.$realmDir.'>
								<div>
									<a href="?page=members&sortBy=realm&direction='.$realmDir.'">
										Realm
									</a>
								</div>
							</option>
							<option value="rank" '.$rankDir.'>
								<div>
									<a href="?page=members&sortBy=rank&direction='.$rankDir.'">
										Rang
									</a>
								</div>
							</option>
					</select>
					<div class="sort_invert_container">
						<input type="checkbox" name="invert_sort" id="invert_sort" '.$invert_value.' class="sort_box" onchange="this.form.submit()"> <label for="invert_sort">Suche umkehren</label>
					</div>
				</form>
				<center>
					<ul>';
				
		
			while($member = mysqli_fetch_object($getMembers))
			{
			
				$name = $member->name;
				$level = $member->level;
				$race = $member->race;
				$class = $member->class;
				$gender = $member->gender;
				$achievementPoints = $member->acmpoints;
				$avatar = $member->avatar;
				$rank = $member->rank;
				$realm = $member->realm;
				
				/* -------------------------------- GENDER BEGIN -------------------------------- */
				
					if($gender == '0')
					{
						 // Male
						$genderName = 'Männlich';
						$genderIcon = 'male.png';
						$prefix = 'ein';
						$prefix_end = '';
					}
					elseif($gender == '1')
					{
						// Female
						$genderName = 'Weiblich'; 
						$genderIcon = 'female.png';
						$prefix = 'eine';
						$prefix_end = 'in';
					}
				
				/* -------------------------------- GENDER END -------------------------------- */
				/* -------------------------------- CLASS BEGIN -------------------------------- */

					 $classData = $armory->getClassData($class);
					 $className = $classData[0];
					 $classColor = $classData[1];
					 
					 $bgcolor = hex2rgba($classColor, '0.1');
				 
				 /* -------------------------------- CLASS END -------------------------------- */
				 /* -------------------------------- GUILD RANK BEGIN -------------------------------- */
				 
					$rankName = $armory->get_rank($rank);
				 
				 /* -------------------------------- GUILD RANK END -------------------------------- */
				
				$html .= '
							<li class="members_row smoothTransitionFast clearfix" style="background-color:'.$bgcolor.'">
								<div class="columnAvatar clearfix">
									<img style="background-image: url(\'http://render-eu.worldofwarcraft.com/character/'.$avatar.'?alt=wow/static/images/2d/avatar/'.$race.'-'.$gender.'.jpg\')">
								</div>
								<div class="charSheet">
									<div class="columnName">
										<a href="http://eu.battle.net/wow/de/character/'.$realm.'/'.$name.'/advanced" style="color:'.$classColor.'" target="_blank">
											'.$name.' - '.$realm.'
										</a>
									</div>
									<div class="columnLevel">
										Level '.$level.'  <img src="./img/gfx/icons/'.$genderIcon.'" title="'.$genderName.'"> <img src="http://media.blizzard.com/wow/icons/18/race_'.$race.'_'.$gender.'.jpg"> <img src="http://media.blizzard.com/wow/icons/18/class_'.$class.'.jpg" title="'.$className.'">
									</div>
									<div class="columnAchievementPoints">
										<img src="http://eu.battle.net/wow/static/images/icons/achievements.gif"> '.$achievementPoints.'
									</div>
									<div class="columnRank">
										'.$rankName.'
									</div>
								</div>
							</li>';
			}
		
			$html .= '
						</ul>
					</center>
					'.$page_html.'
				</div>';
		
	}
	else
	{
		$html .= '
			<div class="empty_search_result">
				<span>Es liegen keine Suchergebnisse vor</span>
			</div>
			
		';
	}
	
	
	echo $html;
?>