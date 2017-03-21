<?php
// Re initialize the DB and Runtime Class
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);
	
	
if (isset($_SESSION['angemeldet']) || $_SESSION['angemeldet'] = true) {
    function Logout()
    {
        
        // Build MySqli Connection
		$db         = new Database();
		$connection = $db->mysqli_db_connect();

		$main = new Board($db, $connection);
        
        // GET USER IP
        if (!isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $client_ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        
        
        // USER AGENT
        
        function getBrowser()
        {
            $u_agent  = $_SERVER['HTTP_USER_AGENT'];
            $bname    = 'Unknown';
            $platform = 'Unknown';
            $version  = "";
            
            
            if (preg_match('/linux/i', $u_agent)) {
                $platform = 'Linux';
            } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
                $platform = 'Mac';
            } elseif (preg_match('/windows|win32/i', $u_agent)) {
                $platform = 'Windows';
            }
            
            
            if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
                $bname = 'Internet Explorer';
                $ub    = "MSIE";
            } elseif (preg_match('/Firefox/i', $u_agent)) {
                $bname = 'Mozilla Firefox';
                $ub    = "Firefox";
            } elseif (preg_match('/Chrome/i', $u_agent)) {
                $bname = 'Google Chrome';
                $ub    = "Chrome";
            } elseif (preg_match('/Safari/i', $u_agent)) {
                $bname = 'Apple Safari';
                $ub    = "Safari";
            } elseif (preg_match('/Opera/i', $u_agent)) {
                $bname = 'Opera';
                $ub    = "Opera";
            } elseif (preg_match('/Netscape/i', $u_agent)) {
                $bname = 'Netscape';
                $ub    = "Netscape";
            }
            
            
            $known   = array(
                'Version',
                $ub,
                'other'
            );
            $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
            if (!preg_match_all($pattern, $u_agent, $matches)) {
                
            }
            
            
            $i = count($matches['browser']);
            if ($i != 1) {
                
                if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                    $version = $matches['version'][0];
                } else {
                    $version = $matches['version'][1];
                }
            } else {
                $version = $matches['version'][0];
            }
            
            
            if ($version == null || $version == "") {
                $version = "?";
            }
            
            return array(
                'userAgent' => $u_agent,
                'name' => $bname,
                'version' => $version,
                'platform' => $platform,
                'pattern' => $pattern
            );
        }
        
        
        $ua        = getBrowser();
        $userAgent = $ua['name'] . ", Version: " . $ua['version'] . " auf " . $ua['platform'] . "";
        
        
        session_start();
        $_SESSION['ID'] = session_id();
        $unsetSession   = $db->query("UPDATE $db->table_sessions SET last_user_ip=current_user_ip, current_user_ip=0 WHERE sid=('" . $_SESSION['ID'] . "')");
        $unsetSession   = $db->query("UPDATE $db->table_sessions SET active=0, last_sid=sid, sid=NULL, online=0 WHERE sid=('" . $_SESSION['ID'] . "')");
        $unsetSession   = $db->query("UPDATE $db->table_accdata SET login_status=0 WHERE account_id=(SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "'))");
        $unsetSession2  = "UPDATE $db->table_accounts SET logged_in=0, last_sid=sid, sid=NULL WHERE sid=('" . $_SESSION['ID'] . "')";
        
        if (!$unsetSession) {
            $db->query("INSERT INTO $db->table_accountlogs (account_id, message_fail, user_agent, time, sid, user_ip) VALUES ((SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')), 'Status der User Sitzung konnte nicht geändert werden.', ('" . $userAgent . "'), NOW(), ('" . $_SESSION['ID'] . "'), ('" . $client_ip . "'))");
            return;
        } else {
            
            $db->query("INSERT INTO $db->table_accountlogs (account_id, message, user_agent, time, sid, user_ip) VALUES ((SELECT id FROM $db->table_accounts WHERE sid=('" . $_SESSION['ID'] . "')), 'User wurde erfolgreich ausgeloggt.', ('" . $userAgent . "'), NOW(), ('" . $_SESSION['ID'] . "'), ('" . $client_ip . "'))");
            
            setcookie('PHPSESSID', '', time() - (3600 * 24 * 364 * 1000));
            session_unset();
            session_destroy();
            $_SESSION = array();
            
            
            header("refresh:0;url=?page=Portal");
        }
        $db->query($unsetSession2);
    }
}
?>