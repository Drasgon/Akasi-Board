<?php
/*
Copyright (C) 2016  Alexander Bretzke

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

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
	if(!$main->checkSessionAccess('MOD'))
		exit();
	
	
	// On command: Send testmail
	
	if(isset($_GET['action']) && $_GET['action'] == 'moveThread')
	{
		if(isset($_POST['moveThreadID']) && !empty($_POST['moveThreadID'])
			&& isset($_POST['moveThreadIDTarget']) && !empty($_POST['moveThreadIDTarget']))
		{
			$admin->MoveThread($_POST['moveThreadID'], $_POST['moveThreadIDTarget']);
		}
	}

?>