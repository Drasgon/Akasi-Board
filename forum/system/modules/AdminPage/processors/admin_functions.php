<?php
if (!isset($db) || $db == NULL)
{
	$db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
	$main = new Board($db, $connection);
if (!isset($admin) || $admin == NULL)
	$admin = new Admin($db, $connection, $main);

	$main->UseFile('./system/auth/auth.php');
	if(!isset($_SESSION['STATUS']) || (isset($_SESSION['STATUS']) && $_SESSION['STATUS'] != true))
	{
		exit();
	}
	
	
	// On command: Send testmail
	
	if(isset($_GET['action']) && $_GET['action'] == 'sendTestMail')
	{
		if(isset($_POST['testmailto']) && !empty($_POST['testmailto']))
		{
			$admin->TestMailFunction($_POST['testmailto']);
		}
	}

?>