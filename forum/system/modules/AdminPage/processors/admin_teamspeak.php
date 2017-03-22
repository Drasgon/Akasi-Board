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

echo 'aaaa';

if (!isset($db) || $db == NULL)
{
	include_once('../../../classes/akb_mysqli.class.php');
	
	$db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
{
	include_once('../../../classes/akb_main.class.php');

	$main = new Board($db, $connection);
}

if (!isset($admin) || $admin == NULL)
{
	$main->useFile('./system/classes/akb_admin.class.php');
	
	$admin = new Admin($db, $connection);
}

	$main->UseFile('./system/auth/auth.php');
	if(!$main->checkSessionAccess('MOD'))
		exit();

	$main->useFile('./system/classes/akb_ssh.class.php');
	

	$ssh = new SSH();

	$ssh->controlTeamSpeak($_POST['actionID']);
	
	echo($ssh->data);
?>