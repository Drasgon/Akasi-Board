<?php

function checkLogin($client_ip)
{
/*  
	<-- SETUP BEGIN -->
*/

	if (!isset($db) || $db == NULL)
	{
		$db = NEW Database();
		$connection = $db->mysqli_db_connect();
	}
	if (!isset($main) || $main == NULL)
		$main = new Board($db, $connection);

/*  
	<-- SETUP END -->
*/	
	
	// Ask the main class if the ip already got punished
	return $checkIP = $main->checkBadLogins($client_ip);
	
}
?>