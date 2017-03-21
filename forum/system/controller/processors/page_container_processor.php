<?php
/*
Copyright (C) 2015  Alexander Bretzke

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

// Re initialize the DB and Runtime Class
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);

/*
	Process the actual URI parameter to include the 
	correct files, for building the rest of the page
*/


/*  <---- Portal ---->  */
(!isset($_GET['page']) || $_GET['page'] == "Portal" && !isset($_GET['threadID']) && !isset($_GET['boardview'])) ? 
$main->useFile('./portal.php') : '';
/*  <---- Portal END ---->  */



/*  <---- Board ---->  */
(isset($_GET['page']) && $_GET['page'] == "Index" && !isset($_GET['threadID'])) ? 
$main->useFile('./system/interface/forum/forum.php') : '';
/*  <---- Board END ---->  */



/*  <---- Thread ---->  */
(isset($_GET['page']) && $_GET['page'] == "Index" && isset($_GET['threadID']) && !empty($_GET['threadID'])) ? 
$main->useFile('./system/interface/forum/thread.php') : '';
/*  <---- Thread END ---->  */



/*  <---- Members ---->  */
(isset($_GET['page']) && $_GET['page'] == "Members" && !isset($_GET['threadID']) && !isset($_GET['boardview'])) ? 
$main->useFile('./members.php') : '';
/*  <---- Members END ---->  */



/*  <---- Gallery ---->  */
(isset($_GET['page']) && $_GET['page'] == "Gallery") ? 
$main->useFile('./gallery.php') : '';
/*  <---- Gallery END ---->  */



/*  <---- Account Settings ---->  */
(isset($_GET['page']) && $_GET['page'] == "Account") ? 
$main->useFile('./system/account_settings/account_settings_main.php') : '';
/*  <---- Account END ---->  */



/*  <---- Register ---->  */
(isset($_GET['page']) && $_GET['page'] == "Register" && !isset($_GET['threadID']) && !isset($_GET['boardview'])) ? 
$main->useFile('./system/interface/register.php') : '';/*  <---- Login END ---->  */
/*  <---- Register END ---->  */



/*  <---- Login ---->  */
(isset($_GET['page']) && $_GET['page'] == "Login" && !isset($_GET['threadID']) && !isset($_GET['boardview'])) ? 
$main->useFile('./system/interface/login.php') : '';
/*  <---- Login END ---->  */



/*  <---- Search ---->  */
(isset($_GET['page']) && $_GET['page'] == "Search" && isset($_GET['q']) && !empty($_GET['q'])) ? 
$main->useFile('./search.php') : '';
/*  <---- Search END ---->  */



/*  <---- Notes ---->  */
(isset($_GET['page']) && $_GET['page'] == "Notes") ? 
$main->useFile('./system/interface/notes.php') : '';
/*  <---- Notes END ---->  */

?>