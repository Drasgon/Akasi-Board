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

header('Content-type: text/html; charset=UTF-8');


include('../../classes/akb_mysqli.class.php');
include('../../classes/akb_main.class.php');

if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);

$db->query("SET NAMES utf8");

if (isset($_COOKIE['PHPSESSID']) && $_COOKIE['PHPSESSID'] != 'deleted') {
    if ($_COOKIE['PHPSESSID'] == '0') {
			$_SESSION['STATUS'] = false;
			setcookie('PHPSESSID', '', time() - 3600);
        return;
    } else {
        session_start();
        $_SESSION['ID'] = mysqli_real_escape_string($connection, $_COOKIE['PHPSESSID']);
        
        $checkUserbyQuery = $db->query("SELECT sid FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')");
        
        if (mysqli_num_rows($checkUserbyQuery) == 0 || mysqli_num_rows($checkUserbyQuery) > 1) {
            setcookie('PHPSESSID', '', time() - 3600);
            $_SESSION['STATUS'] = false;
        } else {
            $_SESSION['STATUS'] = true;
            $main->useFile('./system/controller/security/permission_system.php');
        }
    }
} else {
    $_SESSION['STATUS'] = false;
}

if(isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == true) {

if (isset($_GET['ajaxSend']) && $_GET['ajaxSend'] == 'saveThread' && isset($_POST['postTitle']) && isset($_POST['postContent']) && isset($_POST['val_token']) && !empty($_POST['val_token']) && isset($_POST['boardID']) && !empty($_POST['boardID'])) {

$title = mysqli_real_escape_string($connection, $_POST['postTitle']);
$content = mysqli_real_escape_string($connection, $_POST['postContent']);
$token = mysqli_real_escape_string($connection, $_POST['val_token']);
$board = mysqli_real_escape_string($connection, $_POST['boardID']);



	$checkEntry = $db->query("SELECT token FROM $db->table_thread_saves WHERE token='".$token."'");
	
	if(mysqli_num_rows($checkEntry) >= 1)
		$db->query("UPDATE $db->table_thread_saves SET title=('".$title."'), content=('".$content."') WHERE token=('".$token."') AND user_id=(SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."'))");
	else
		$createRow = $db->query("INSERT INTO $db->table_thread_saves (token, user_id, board_id, title, content) VALUES ('".$token."', (SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."')), '".$board."', '".$title."', '".$content."')");

}

if (isset($_GET['ajaxSend']) && $_GET['ajaxSend'] == 'deleteAllSavedThreads' && isset($_POST['deletecon']) && $_POST['deletecon'] == 'all') {
		$db->query("DELETE FROM $db->table_thread_saves WHERE user_id = (SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))");
	}
}
?>