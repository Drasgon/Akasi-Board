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

require_once(dirname(__FILE__).'/../../security/callstack_validation.php');

if (!isset($db) || $db == NULL)
{
	$db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
	$main = new Board($db, $connection);

if (!isset($admin) || $admin == NULL)
	$admin = new Admin($db, $connection);

if(!$main->checkSessionAccess('MOD'))
	exit();

$main->UseFile('./system/modules/AdminPage/processors/admin_functions.php');

	//$admin->TestMailFunction('Alexander_Bretzke@gmx.de');
?>

<div class="adminFunctionsRow">
	<form method="POST" action="?page=Admin&Tab=Functions&action=sendTestMail">
		<label for="testmailto">
			Senden einer Testmail an:
		</label>
		<input type="text" name="testmailto" id="testmailto" />
		<button value="Senden" onclick="submit()">Senden</button>
	</form>
</div>