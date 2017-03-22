<?php

	session_start();
	
	ini_set('display_startup_errors',0);
	ini_set('display_errors',0);
	error_reporting(0);

	// Re initialize the Board class
	if(!isset($db) || $db = NULL)
	{
		require('../../classes/akb_mysqli.class.php');
		$db         = new Database();
		$connection = $db->mysqli_db_connect();
	}
	
	// Re initialize the Board class
	if(!isset($main) || $main = NULL)
	{
		require('../../classes/akb_main.class.php');
		$main         = new Board($db, $connection);
	}
	
	$ajaxStatus = true;
	
	$main->useFile('./system/auth/auth.php');


	$posts_in_last_minutes = 5;
	$last_acTime = time();
	$multiplier = 60;
	$time_string = $last_acTime - ($multiplier * $posts_in_last_minutes);
	$getMsg = $db->query("SELECT id,thread_id,author_id,text FROM $db->table_thread_posts WHERE date_posted >= ('".$time_string."') ORDER BY id DESC");
	
	
	// PROCESSING START

		// $db->query("UPDATE $db->table_sessions SET last_activity=('".$last_acTime."') WHERE sid = ('".$_SESSION['ID']."')");

		while ($messages = mysqli_fetch_object($getMsg)) {
			
			$id        = $messages->id;
			$thread_id = $messages->thread_id;
			$author_id = $messages->author_id;
			$text      = preg_replace("/<.*?>/", " ", $messages->text);
			
			$checksubStatus = $db->query("SELECT sub_id,user_id,thread_id FROM $db->table_subdata WHERE thread_id=('" . $thread_id . "') AND user_id=('" . $_SESSION['USERID'] . "')");
			
			if (mysqli_num_rows($checksubStatus) != 0) {
				$checkRequestStatus = $db->query("SELECT request_id,user_id,thread_id, last_post FROM $db->table_msg_request WHERE thread_id=('" . $thread_id . "') AND user_id=('" . $_SESSION['USERID'] . "')");
				
					if($data = mysqli_fetch_object($checkRequestStatus))
					{
						$last_post = $data->last_post;
					}
					else
						$last_post = -1;

						if (mysqli_num_rows($checkRequestStatus) == 0 || (mysqli_num_rows($checkRequestStatus) == 1 && $last_post != $id))
						{
							
							$get_authorInfo = $db->query("SELECT username,avatar FROM $db->table_accdata WHERE account_id=('" . $author_id . "')");
							
							while ($authorInfo = mysqli_fetch_object($get_authorInfo)) {
								$author       = $authorInfo->username;
								$authorAvatar = $main->checkUserAvatar($authorInfo->avatar);
								
								
								$get_threadData = $db->query("SELECT title FROM $db->table_thread WHERE id=('" . $thread_id . "')");
								
								while ($threadData = mysqli_fetch_object($get_threadData)) {
									$threadTitle = $threadData->title;
									
									$msgResult = array(
										
										'id' => $id,
										'thread_id' => $thread_id,
										'author' => $author,
										'authorAvatar' => $authorAvatar,
										'text' => $text,
										'threadTitle' => $threadTitle
										
									);
								}
							}
							echo json_encode($msgResult);
							
							if(mysqli_num_rows($checkRequestStatus) == 0)
								$db->query("INSERT INTO $db->table_msg_request (user_id, thread_id, last_post) VALUES (('" . $_SESSION['USERID'] . "'),('" . $thread_id . "'), ('" . $id . "'))");
							else
								$db->query("UPDATE $db->table_msg_request SET last_post = ('" . $id . "') WHERE thread_id=('" . $thread_id . "') AND user_id=('" . $_SESSION['USERID'] . "')");
						}

			}
		}
	
	// PROCESSING END
	?>