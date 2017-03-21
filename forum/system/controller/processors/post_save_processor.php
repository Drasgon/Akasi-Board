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
        $_SESSION['angemeldet'] = false;
        setcookie('PHPSESSID', '', time() - 3600);
        return;
    } else {
        session_start();
        $_SESSION['ID'] = mysqli_real_escape_string($connection, $_COOKIE['PHPSESSID']);
        
        $checkUserbyQuery = $db->query("SELECT sid FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')");
        
        if (mysqli_num_rows($checkUserbyQuery) == 0 || mysqli_num_rows($checkUserbyQuery) > 1) {
            setcookie('PHPSESSID', '', time() - 3600);
            $_SESSION['angemeldet'] = false;
        } else {
            $_SESSION['angemeldet'] = true;
            require('../security/permission_system.php');
        }
    }
} else {
    $_SESSION['angemeldet'] = false;
}

if(isset($_SESSION['angemeldet']) && $_SESSION['angemeldet'] == true) {

if (isset($_GET['ajaxSend']) && $_GET['ajaxSend'] == 'savePost' && isset($_POST['postContent']) && isset($_POST['val_token']) && !empty($_POST['val_token']) && isset($_POST['threadID']) && !empty($_POST['threadID'])) {

$content = mysqli_real_escape_string($connection, $_POST['postContent']);
$token = mysqli_real_escape_string($connection, $_POST['val_token']);
$thread = mysqli_real_escape_string($connection, $_POST['threadID']);

	$createRow = $db->query("INSERT INTO $db->table_post_saves (token, user_id, thread_id, content) VALUES ('".$token."', (SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."')), '".$thread."', '".$content."')");
	if(!$createRow) {
	$db->query("UPDATE $db->table_post_saves SET content=('".$content."') WHERE token=('".$token."') AND user_id=(SELECT id FROM $db->table_accounts WHERE sid=('".$_SESSION['ID']."'))");
	}

}

if (isset($_GET['ajaxSend']) && $_GET['ajaxSend'] == 'deleteAllSavedPosts' && isset($_POST['deletecon']) && $_POST['deletecon'] == 'all') {
		$db->query("DELETE FROM $db->table_post_saves WHERE user_id = (SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))");
	}
} else {
	echo 'nli';
}
?>