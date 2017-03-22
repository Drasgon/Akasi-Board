<?php
global $totalQueries, $totalQueryTime;



// Check the session cookie for a valid value

if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board();

if (!isset($session) || $session == NULL)
{
	$main->useFile('./system/classes/akb_session.class.php', 1);
    $session = new Session();
}
	
	
/*if (isset($_COOKIE['PHPSESSID']) && $_COOKIE['PHPSESSID'] != 'deleted') {
    if ($_COOKIE['PHPSESSID'] == '0') {
        $_SESSION['STATUS'] = false;
        setcookie('PHPSESSID', '', time() - 3600);
        return;
    } else {
		if(!isset($_SESSION))
			session_start();
        
        $_SESSION['ID'] = mysqli_real_escape_string($connection, $_COOKIE['PHPSESSID']);
        

			$checkUserbyQuery = $db->query("SELECT id FROM $db->table_sessions WHERE sid=('" . $_SESSION['ID'] . "')");
		

        if (mysqli_num_rows($checkUserbyQuery) == 0 || mysqli_num_rows($checkUserbyQuery) > 1) {
            setcookie('PHPSESSID', '', time() - 3600);
            $_SESSION['STATUS'] = false;
        } else {
            $_SESSION['STATUS'] = true;
            $main->useFile('./system/controller/security/permission_system.php');
            
            $activity_time = 600;
            
            if (!isset($_COOKIE['akb_last_activity']) || empty($_COOKIE['akb_lastActitivty']) || time() - $_COOKIE['akb_last_activity'] >= $activity_time) {
                setcookie("akb_last_activity", time(), time() + $activity_time);
				
				$_SESSION['USERNAME'] = $main->getUsername();
				$_SESSION['USERID']   = $main->getUserId();
				
				if(!isset($_SESSION['lastActivity']) || (isset($_SESSION['lastActivity']) && (time() - $_SESSION['lastActivity'] >= $activity_time)) && (!isset($GLOBALS['ajaxStatus']) || (isset($GLOBALS['ajaxStatus']) && $GLOBALS['ajaxStatus'] == false)))
				{
					$queryReslt = $db->query("UPDATE $db->table_sessions SET online='1', last_activity=('" . time() . "') WHERE id=('" . $_SESSION['USERID'] . "')");
					
					$_SESSION['lastActivity'] = time();
				}
					
            }
            
            
        }
    }
} else {
    $_SESSION['STATUS'] = false;
}*/

$session->initializeSession();

$main->updateUserStatus();

// If an unauthorized session usage was detected
if((!isset($_COOKIE['PHPSESSID']) || empty($_COOKIE['PHPSESSID'])) && (isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == true))
{
	$_SESSION['STATUS'] = false;
	session_unset();
}
?>
