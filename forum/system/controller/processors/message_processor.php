<?php
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);

	# Temporary auto display of "0 Messages"
		echo '0';

	$main->useFile('./system/classes/akb_note.class.php');
		$message = new Message($db, $connection);

	#$newMessageCount = $message->getNewMessages();
	
	#echo $newMessageCount;
?>