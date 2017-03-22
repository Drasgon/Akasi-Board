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
	
	if(!isset($error) || $error = NULL)
	{
		$main->UseFile('./system/classes/akb_error.class.php', 1);
		$error         = new Error('Es sind Fehler beim Bearbeiten des Beitrags aufgetreten!');
	}
	
	$main->useFile('./system/interface/successpage.php');
	$errorStatus = false;
	
	if((isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == true && $main->checkSessionAccess('USER')) || $main->boardConfig($main->getThreadBoardID($_GET['threadID']), "guest_posts"))
	{

		if(isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == TRUE)
		{
			$_SESSION['ID'] = session_id();
			
			$getAuthorID = $db->query("SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."')");
			
			$authorID = mysqli_fetch_object($getAuthorID);
				$authorIDResult = $authorID->id;
		}
		else
			$authorIDResult = 0;
		
			$actualThread = mysqli_real_escape_string($GLOBALS['connection'], $_GET['threadID']);

		$content = '';
			
		if ((empty($_POST["postAddArea"]))) {
			$error->addError('Sie haben nicht alle benötigten Informationen eingegeben!');
			$errorStatus = true;
		} else {

		$content = mysqli_real_escape_string($GLOBALS['connection'], $_POST["postAddArea"]);

		if (! isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$client_ip = $_SERVER['REMOTE_ADDR'];
		}
		else {
		$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

		if (strlen($content) < 30)  {
			$error->addError('Ihr Beitrag muss mindestens 30 Zeichen besitzen.');
				$errorStatus = true;
		};

		if (strlen($content) > 30000) {
			$error->addError('Ihr Beitrag darf maximal 30000 Zeichen besitzen.');
				$errorStatus = true;
		};

		if (strlen(trim($content)) == 0) {
			$error->addError('Ihr Beitrag darf nicht ausschließlich aus Leerzeichen bestehen!');
				$errorStatus = true;
		};

		$insertTime = time();

		if($errorStatus == false)
		{
			$db->query("INSERT INTO $db->table_thread_posts (thread_id, author_id, date_posted, text) VALUES ('".$actualThread."', '".$authorIDResult."', '".$insertTime."', '".$content."')") or die(mysql_error());
				$db->query("UPDATE $db->table_thread SET last_activity=0 WHERE last_activity=1 AND id=('".$actualThread."')") or die(mysql_error());
					$db->query("UPDATE $db->table_thread SET posts=posts+1, last_replyTime='".$insertTime."', last_post_author_id=('".$authorIDResult."') WHERE id=('".$actualThread."')") or die(mysql_error());
						if(isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == TRUE)
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

			throwSuccess('Beitrag wurde erfolgreich erstellt!', '?page=Index&amp;threadID=' . $actualThread . '');
		}
		};
		
		if($errorStatus == true)
		{
			echo $error->getOutput();
			
			return $content;
		}
	
	}
	else
		return;
}
?>