<?php
global $totalQueries, $totalQueryTime;



// Check the session cookie for a valid value

if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);

	
	
if (isset($_COOKIE['PHPSESSID']) && $_COOKIE['PHPSESSID'] != 'deleted') {
    if ($_COOKIE['PHPSESSID'] == '0') {
        $_SESSION['angemeldet'] = false;
        setcookie('PHPSESSID', '', time() - 3600);
        return;
    } else {
        session_start();
        
        $_SESSION['ID'] = mysqli_real_escape_string($connection, $_COOKIE['PHPSESSID']);
        

			$checkUserbyQuery = $db->query("SELECT id FROM $db->table_sessions WHERE sid=('" . $_SESSION['ID'] . "')");
		

        if (mysqli_num_rows($checkUserbyQuery) == 0 || mysqli_num_rows($checkUserbyQuery) > 1) {
            setcookie('PHPSESSID', '', time() - 3600);
            $_SESSION['angemeldet'] = false;
        } else {
            $_SESSION['angemeldet'] = true;
            $main->useFile('./system/controller/security/permission_system.php');
            
            $activity_time = 600;
            
            if (!isset($_COOKIE['akb_last_activity']) || empty($_COOKIE['akb_lastActitivty']) || time() - $_COOKIE['akb_last_activity'] >= $activity_time) {
                setcookie("akb_last_activity", time(), time() + $activity_time);
				
				$_SESSION['username'] = $main->getUsername();
				$_SESSION['userid']   = $main->getUserId();
				
				if(!isset($_SESSION['lastActivity']) || (isset($_SESSION['lastActivity']) && (time() - $_SESSION['lastActivity'] >= 600)))
				{
					$queryReslt = $db->query("UPDATE $db->table_sessions SET online='1', last_activity=('" . time() . "') WHERE id=('" . $_SESSION['userid'] . "')");
					
					$_SESSION['lastActivity'] = time();
				}
					
            }
            
            
        }
    }
} else {
    $_SESSION['angemeldet'] = false;
}

$main->updateUserStatus();

// If an unauthorized session usage was detected
if((!isset($_COOKIE['PHPSESSID']) || empty($_COOKIE['PHPSESSID'])) && (isset($_SESSION['angemeldet']) && $_SESSION['angemeldet'] == true))
{
	$_SESSION['angemeldet'] = false;
	session_unset();
}
?>
