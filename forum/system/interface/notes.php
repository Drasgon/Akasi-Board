<?php
	global $langGlobal;
	
	if(isset($_SESSION['angemeldet']) && $_SESSION['angemeldet'] == true)
	{

		// Re initialize the DB and Runtime Class
		if (!isset($db) || $db == NULL)
		{
			$db = NEW Database();
			$connection = $db->mysqli_db_connect();
		}
		if (!isset($main) || $main == NULL)
			$main = new Board($db, $connection);
			
			
			$indexURI 		= '?page=Index';
			$profileURI 	= '?page=Profile';
			$galleryURI 	= '?page=Gallery';
			$messageURI 	= '?page=Message';
			
			$note_types = ARRAY(
				#0; Posts
				$langGlobal['notes_posted'],
				#1; Private Messages
				$langGlobal['notes_pm'],
				#2; Gallery uploads
				$langGlobal['notes_gallery'],
				#3; Profile View
				$langGlobal['notes_profile_view']
			);
			
			
		$html = '';
		
		$html .= '
			<div class="mainHeadline">
				<div class="icons" id="forumiconMain"></div>
				<div class="headlineContainer">
				  <h1>
					'.$langGlobal['notes'].'
				  </h1>
				</div>
			</div>';
			
			$data_query = $db->query("SELECT id, sender, receiver, priority, note_type, refer_to, time_sent, read_state FROM ".$db->table_notes." WHERE receiver='".$_SESSION['userid']."' AND priority<=(SELECT account_level FROM (".$db->table_accounts.") WHERE sid=('".$_SESSION['ID']."')) ORDER BY read_state ASC");
				while($data = mysqli_fetch_object($data_query))
				{
					$id 			= $data->id;
					$sender 		= $data->sender;
					$receiver 		= $data->receiver;
					$priority 		= $data->priority;
					$note_type 		= $data->note_type;
					$refer_to 		= $data->refer_to;
					$time_sent 		= $main->convertTime($data->time_sent);
					$read_state 	= $data->read_state;
					
					// Convert the string to array keys & values and assign it to the "refer_parts" array
					
						$refer_parts = ARRAY();
						$refer_parts_temp = explode(';', $refer_to);
						
						foreach($refer_parts_temp as $key => $value)
						{
							$temp_value = explode('=', $value);
							$refer_parts[$temp_value[0]] = $temp_value[1];
						}
						
						$initialURI = ${$refer_parts['type'] . 'URI'};
						$secondaryURI = '';
						foreach($refer_parts as $key => $value)
						{
							// Skip the type key
							if($key == 'type')
								continue;
							
							$secondaryURI .= '&'.$key.'='.$value;
						}
						
						#echo $initialURI;
						
					// CONVERT END
					
						#print_r($refer_parts);
					
					if($sender == -1)
					{
						$user_name = 'System';
						$user_img = './images/avatars/default.png';
						$user_link = '	<div>
											<img src="'.$user_img.'" height="20px" class="UserImage">
											<span class="search_row_author_name">'.$user_name.'</span>
										</div>';
					}
					
					if($sender >= 1)
					{
						$user_name = $main->getUsername($sender);
						$user_img = $main->getUseravatar($sender);
						$user_link = '	<a href="?page=Profile&amp;User=0">
											<img src="'.$user_img.'" height="20px" class="UserImage">
											<span class="search_row_author_name">'.$user_name.'</span>
										</a>';
					}
					
					switch($priority)
					{
						case 1:
							$user_priority = '<div class="priority user_priority"><span>USER</span></div>';
						break;
						case 2:
							$user_priority = '<div class="priority mod_priority"><span>MODERATOR</span></div>';
						break;
						case 3:
							$user_priority = '<div class="priority admin_priority"><span>ADMINISTRATOR</span></div>';
						break;
						default:
							case 1;
						break;
					}
					
					$html .= '
						<div class="search_row smoothTransitionFast">
							'.$user_priority.'
							<div class="search_row_header clearfix">
								<div class="search_row_header_left">
									<a href="'.$initialURI.$secondaryURI.'">'.$user_name.$note_types[$note_type].'</a>
								</div>
								<div class="search_row_header_right">
									'.$time_sent.'
								</div>
							</div>
							<div class="search_row_body">
								<div class="search_row_body_author">
									'.$user_link.'
								</div>
								INSERT MSG HERE
							</div>
						</div>
					';
				}
		
		echo $html;
		
	}
	else
		throwError($langGlobal['permission_denied']);
?>