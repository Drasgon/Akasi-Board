<?php

function addThread()
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
		$error         = new Error('Es sind Fehler beim Erstellen ihres Threads aufgetreten!');
	}
	
	if(isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == true || $main->boardConfig($main->getThreadBoardID($_GET['threadID']), "guest_posts"))
	{

		global $threadAddStatusArray;
		global $largestNumber;
		$errorStatus = false;

		$threadAddStatus = false;

		$_SESSION['ID'] = session_id();
		$getAuthorID = $db->query("SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."')");

		while ($authorID = mysqli_fetch_object($getAuthorID)) {
			$authorIDResult = $authorID->id;
		}

		// VARS

		// BOARD ID
		if(isset($_GET['boardview']) && !isset($_GET['threadID'])) {
			$actualBoard = mysqli_real_escape_string($GLOBALS['connection'], $_GET['boardview']);
		} else {
			$getBoard = $db->query("SELECT main_forum_id FROM $db->table_thread WHERE id=('".$actualThread."')");
			
			while($boardIDData = mysqli_fetch_object($getBoard)) {
				$actualBoard = $boardIDData->main_forum_id;
			}
		}

		if ((empty($_POST["threadAddArea"])) || (empty($_POST["threadTitleInput"]))) {
			$error->addError('Sie haben nicht alle benötigten Informationen eingegeben!');
			$error->addError('Der Titel muss mindestens 3 Zeichen besitzen.');
			$error->addError('Ihr Beitrag muss mindestens 30 Zeichen besitzen.');
			$error->addError('Ihr Beitrag darf nicht ausschließlich aus Leerzeichen bestehen!');
			$errorStatus = true;

		} else {

			$newThreadTitle = mysqli_real_escape_string($GLOBALS['connection'], $_POST["threadTitleInput"]);
			$content 		= mysqli_real_escape_string($GLOBALS['connection'], $_POST["threadAddArea"]);

			if (! isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$client_ip = $_SERVER['REMOTE_ADDR'];
			}
			else {
			$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}

			if (strlen($newThreadTitle) 
			< 3)  {
			$error->addError('Der Titel muss mindestens 3 Zeichen besitzen.');
			$errorStatus = true;
			};

			if (strlen($newThreadTitle) >
			200)  {
			$error->addError('Der eingegebene Titel ist zu lang. Er darf maximal 200 Zeichen beinhalten.');
			$errorStatus = true;
			};

			if (strlen($content) 
			< 30)  {
			$error->addError('Ihr Beitrag muss mindestens 30 Zeichen besitzen.');
			$errorStatus = true;
			};

			if (strlen($content) >
			30000) {
			$error->addError('Ihr Beitrag ist zu lang. Er darf maximal 30000 Zeichen beinhalten.');
			$errorStatus = true;
			};

			if (strlen(trim($content)) == 0) {
			$error->addError('Ihr Beitrag darf nicht ausschließlich aus Leerzeichen bestehen!');
			$errorStatus = true;
			};

			$insertTime = time();
			
			if($errorStatus == false)
			{
				$db->query("INSERT INTO $db->table_thread (main_forum_id, title, date_created, last_replyTime,last_post_author_id, posts, author_id) VALUES ('".$actualBoard."', '".$newThreadTitle."', '".$insertTime."', '".$insertTime."','".$authorIDResult."', '0', '".$authorIDResult."')");
				$db->query("INSERT INTO $db->table_thread_posts (thread_id, author_id, date_posted, text) VALUES ((SELECT id FROM $db->table_thread WHERE id=(SELECT max(id) FROM $db->table_thread)), '".$authorIDResult."', '".$insertTime."', '".$content."')");
				$db->query("UPDATE $db->table_accdata SET post_counter=post_counter+1 WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."'))");
				$db->query("UPDATE $db->table_thread SET last_activity=0 WHERE last_activity=1 AND main_forum_id=('".$actualBoard."')");

				$token = mysqli_real_escape_string($GLOBALS['connection'], $_GET['token']);

				$tokenCheck = $db->query("SELECT token FROM $db->table_thread_saves WHERE token = ('".$token."') AND user_id = (SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))");
				if(isset($_GET['token']) && !empty($_GET['token']) && mysqli_num_rows($tokenCheck) >= 1) $db->query("DELETE FROM $db->table_thread_saves WHERE token = ('".$token."') AND user_id = (SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))");

				$rowSQL = $db->query( "SELECT MAX( id ) AS max FROM $db->table_thread" );
				$row = mysqli_fetch_array( $rowSQL );
				$largestNumber = $row['max'];
				$db->query("UPDATE $db->table_thread SET last_activity=1 WHERE last_activity=0 AND id=('".$largestNumber."')");

				$threadAddStatus = true;
			}
		}
			$threadAddStatusArray = array(
			'threadAddStatus' => $threadAddStatus, 
			'newThreadID' => $largestNumber
			);
			
			echo $error->getOutput();
			
				return $threadAddStatusArray;
	}
	else
		return;
}
?>