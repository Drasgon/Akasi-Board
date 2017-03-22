<?php	
function submitPostEdit()
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
	
	$errorStatus = false;

	if ((empty($_POST["postEditArea"]))) {
	$error->addError('Sie haben nicht alle benötigten Informationen eingegeben!');

	} else {

		$content = mysqli_real_escape_string($GLOBALS['connection'], $_POST["postEditArea"]);
		$postID = mysqli_real_escape_string($GLOBALS['connection'], $_GET['postID']);
		$threadID = mysqli_real_escape_string($GLOBALS['connection'], $_GET['threadID']);

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

		if($errorStatus == false)
		{
			$db->query("UPDATE $db->table_thread_posts SET date_edited=(".time()."),text=('".$content."') WHERE id=('".$postID."')") or die(mysql_error());
			echo'<meta http-equiv="refresh" content="2;url=?page=Index&threadID='.$threadID.'">';
			$main->useFile('./system/interface/successpage.php');
			throwSuccess('Beitrag wurde erfolgreich bearbeitet!', '?page=Index&amp;threadID=' . $threadID . '');
		}
			
	}
	
	echo $error->getOutput();
	
}
?>