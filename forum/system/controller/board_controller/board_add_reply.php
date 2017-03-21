<?php	
function addReply()
{
	// Re initialize the DB
	if(!isset($db) || $db = NULL || !isset($connection) || $connection = NULL)
	{
		$db         = new Database();
		$connection = $db->mysqli_db_connect();
	}

	// Re initialize the Board class
	if(!isset($main) || $main = NULL)
	{
		$main         = new Board($db, $connection);
	}
	
	if(isset($_SESSION['angemeldet']) && $_SESSION['angemeldet'] == true || $main->boardConfig($main->getThreadBoardID($_GET['threadID']), "guest_posts"))
	{

		if(isset($_SESSION['angemeldet']) && $_SESSION['angemeldet'] == TRUE)
		{
			$_SESSION['ID'] = session_id();
			
			$getAuthorID = $db->query("SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."')");
			
			$authorID = mysqli_fetch_object($getAuthorID);
				$authorIDResult = $authorID->id;
		}
		else
			$authorIDResult = 0;
		
			$actualThread = mysqli_real_escape_string($GLOBALS['connection'], $_GET['threadID']);

		if ((empty($_POST["postAddArea"]))) {
		echo 'Sie haben nicht alle benötigten Informationen eingegeben!';

		} else {

		$content = mysqli_real_escape_string($GLOBALS['connection'], $_POST["postAddArea"]);

		if (! isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$client_ip = $_SERVER['REMOTE_ADDR'];
		}
		else {
		$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

		if (strlen($content) < 30)  {
			echo 'Ihr Beitrag muss mindestens 30 Zeichen besitzen.';
				return;
		};

		if (strlen($content) > 30000) {
			echo 'Ihr Beitrag darf maximal 30000 Zeichen besitzen.';
				return;
		};

		if (strlen(trim($content)) == 0) {
			echo 'Ihr Beitrag darf nicht ausschließlich aus Leerzeichen bestehen!';
				return;
		};

		$insertTime = time();


		$db->query("INSERT INTO $db->table_thread_posts (thread_id, author_id, date_posted, text) VALUES ('".$actualThread."', '".$authorIDResult."', '".$insertTime."', '".$content."')") or die(mysql_error());
			$db->query("UPDATE $db->table_thread SET last_activity=0 WHERE last_activity=1 AND id=('".$actualThread."')") or die(mysql_error());
				$db->query("UPDATE $db->table_thread SET posts=posts+1, last_replyTime='".$insertTime."', last_post_author_id=('".$authorIDResult."') WHERE id=('".$actualThread."')") or die(mysql_error());
					if(isset($_SESSION['angemeldet']) && $_SESSION['angemeldet'] == TRUE)
						$db->query("UPDATE $db->table_accdata SET post_counter=post_counter+1 WHERE account_id=('".$authorIDResult."')") or die(mysql_error());

		if(isset($_GET['token']))
			$token = mysqli_real_escape_string($GLOBALS['connection'], $_GET['token']);

			if(!isset($token))
				$db->query("DELETE FROM $db->table_post_saves WHERE user_id = ('".$authorIDResult."') AND thread_id=('".$actualThread."')");
			if(isset($token) && !empty($token))
			{
				$tokenCheck = $db->query("SELECT token FROM $db->table_post_saves WHERE token = ('".$token."') AND user_id = ('".$authorIDResult."')");
			
				if(mysqli_num_rows($tokenCheck) >= 1)
					$db->query("DELETE FROM $db->table_post_saves WHERE token = ('".$token."') AND user_id = ('".$authorIDResult."')");
			}
			
			$db->query("DELETE FROM $db->table_forum_read WHERE thread_id=('".$actualThread."')") or die(mysql_error());
			$db->query("DELETE FROM $db->table_msg_request WHERE thread_id=('".$actualThread."')") or die(mysql_error());

		#echo'<meta http-equiv="refresh" content="2;url=?page=Index&threadID='.$actualThread.'">';

		$main->useFile('./system/interface/successpage.php');
		throwSuccess('Beitrag wurde erfolgreich erstellt!', '?page=Index&amp;threadID=' . $actualThread . '');
		};
	
	}
	else
		return;
}
?>