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

	if ((empty($_POST["postEditArea"]))) {
	echo 'Sie haben nicht alle benötigten Informationen eingegeben!';

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


	$db->query("UPDATE $db->table_thread_posts SET date_edited=NOW(),text=('".$content."') WHERE id=('".$postID."')") or die(mysql_error());
	echo'<meta http-equiv="refresh" content="2;url=?page=Index&threadID='.$threadID.'">';
	echo '<span id="PostAddResponse_Success" class="responseSuccess">Beitrag wurde erfolgreich bearbeitet!</span>';
	};
}
?>