<?php
function login_extended()
{

		global $login_dataArray;

        $everythingEmpty 	= '';
		
        $errorStatus 		= '';
		$criticalLoginError = '';
		$successStatus 		= '0';
		
		$externalMsg		= '';

    if(!isset($db)) {
		$db = NEW Database();
		$connection = $db->mysqli_db_connect();
	}
	
	if(!isset($main)) {
		$main = NEW Board($db, $connection);
	}
    
    $username_dec = $_POST["username_login"];
    $password_dec = $_POST["password_login"];
    
    $user_chars = "/[\#$%^&\*\(\)\+=\-\[\]'\";,\.\/\{\}\|:<>\?~]/";
    if ((empty($username_dec)) || (empty($password_dec))) {
        $db->query("INSERT INTO $db->table_accountlogs (message_fail, user_ip) VALUES ('Es wurden nicht alle erforderlichen Informationen eingegeben', ('" . $client_ip . "'))") or die(mysqli_error($GLOBALS['connection']));
        $externalMsg = $GLOBALS['data_missed'].'<br />';
		$errorStatus = true;

    } //( empty( $username_dec ) ) || ( empty( $password_dec ) )
    else {
        
        // SCRIPT VARIABLES
        $username           = $username_dec;
        $password           = $password_dec;
        $username_validated = (mysqli_real_escape_string($GLOBALS['connection'], $username));
        $password_validated = (mysqli_real_escape_string($GLOBALS['connection'], $password));
        
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
        
        // GET USER IP
        if (!isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $client_ip = $_SERVER['REMOTE_ADDR'];
        } //!isset( $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] )
        else {
            $client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        
        // GET CRYPT LEVEL
        $getCrypt = $db->query('SELECT extra_val,crypt_level FROM '.$db->table_accounts.' WHERE username="' . $username_validated . '"');
		if(mysqli_num_rows($getCrypt) == 1)
		{
			$row_c    = mysqli_fetch_object($getCrypt);
			
			$extra_val = $row_c->extra_val;
			$crypted  = $row_c->crypt_level;
			
			// MORE DATA
			
			$sec         = $password_validated . $password_validated;
			$rand_val    = $crypted;
			$time_visual = $extra_val;
			
			for ($i = $rand_val; $i <= $rand_val; $i++) {
				
				$password_new = $password_validated . $i;
				
				$var = "$password_validated.$sec.$time_visual.$password_new";
				
				$pass_hash_      = md5(strtoupper($password_validated) . ":" . strtoupper($var));
				$pass_hash_final = md5(strtoupper($pass_hash_) . ":" . strtoupper($password_validated));
			} //$i = $rand_val; $i <= $rand_val; $i++
			
			// CAPACITY FUNCTION
			
			$max_users           = $main->serverConfig("max_users");
			$user_counter        = $db->query("SELECT id FROM $db->table_sessions WHERE active= '1'") or die(mysqli_error($GLOBALS['connection']));
			$userLimitation 	 = $main->serverConfig("user_capacity_system");
			
			// DB DATA
			$user_db = $db->query("SELECT username FROM $db->table_accounts WHERE username = ('" . $username_validated . "')") or die(mysqli_error($GLOBALS['connection']));
			$password_db = $db->query("SELECT pass_hash FROM $db->table_accounts WHERE pass_hash = ('" . $pass_hash_final . "') AND username = ('" . $username_validated . "') ") or die(mysqli_error($GLOBALS['connection']));
			$login_ip_update = ("UPDATE $db->table_accounts SET last_login_ip= ('" . $client_ip . "') WHERE username= ('" . $username_validated . "') AND pass_hash=('" . $pass_hash_final . "')") or die(mysqli_error($GLOBALS['connection']));
			$login_sql = $db->query("SELECT username, pass_hash FROM $db->table_accounts WHERE username= ('" . $username_validated . "') AND pass_hash= ('" . $pass_hash_final . "')") or die(mysqli_error($GLOBALS['connection']));
			$ban_check = $db->query("SELECT id, ban_type, ban_duration, banned_by, banned_at FROM $db->table_ban WHERE id= ( SELECT id from $db->table_accounts WHERE username=('" . $username_validated . "') AND pass_hash= ('" . $pass_hash_final . "') )") or die(mysqli_error($GLOBALS['connection']));
			
			// PROCESSING START //
			
			// CHECK FOR CAPACITY //
			if ($userLimitation >= 1) {
				if ($max_users <= mysqli_num_rows($user_counter)) {
					$db->query("INSERT INTO $db->table_accountlogs (message_fail, user_ip) VALUES ('Server Kapazität wurde überschritten. User wurde nicht eingeloggt.', ('" . $client_ip . "'))") or die(mysqli_error($GLOBALS['connection']));
					$externalMsg = $GLOBALS['max_users_reached_string_first'] .  $max_users  . $GLOBALS['max_users_reached_string_sec'].'<br />';
					$errorStatus = true;
				} //$max_users <= mysqli_num_rows( $user_counter )
			}
			
			// ILLEGAL CHARACTERS
			if (preg_match($user_chars, $username_validated)) {
				$db->query("INSERT INTO $db->table_accountlogs (message_fail, user_ip) VALUES ('Username enthält ungueltige Zeichen', ('" . $client_ip . "'))") or die(mysqli_error($GLOBALS['connection']));
					$externalMsg = $GLOBALS['username_illegal_chars'].'<br />';
					$errorStatus = true;
			} //preg_match( $user_chars, $username_validated )
			
			// IF ACCOUNT DONT EXISTS
			if (mysqli_num_rows($user_db) == 0 && mysqli_num_rows($password_db) == 0) {
				$db->query("INSERT INTO $db->table_accountlogs (message_fail, user_ip) VALUES ('Ungültiger Username und Passwort!', ('" . $client_ip . "'))") or die(mysqli_error($GLOBALS['connection']));
					$externalMsg = $GLOBALS['invalid_pass_or_username'].'<br />';
					$errorStatus = true;
			} //mysqli_num_rows( $user_db ) == 0 and mysqli_num_rows( $password_db ) == 0
			
			// CHECK IF PASSWORD IS CORRECT        
			if (mysqli_num_rows($user_db) == 1 and mysqli_num_rows($password_db) == 0) {
				$db->query("INSERT INTO $db->table_accountlogs (message_fail, account_id, sid, user_ip) VALUES ('Ungültiges Passwort', (SELECT id FROM $db->table_accounts WHERE username= ('" . $username_validated . "')), ('" . $_SESSION['ID'] . "'), ('" . $client_ip . "'))") or die(mysqli_error($GLOBALS['connection']));
					sleep(2);
					$externalMsg = $GLOBALS['invalid_pass_or_username'].'<br />';
					$errorStatus = true;
			} //mysqli_num_rows( $user_db ) == 1 and mysqli_num_rows( $password_db ) == 0
			
			// Protection-system
			$get_active    = "SELECT accepted FROM $db->table_accounts WHERE username=('" . $username_validated . "')";
			$active_result = $db->query($get_active);
			while ($active = mysqli_fetch_object($active_result)) {
				$accepted = $active->accepted;
				if ($accepted == 0) {
					$db->query("INSERT INTO protection_system_logs (message, account_id, user_ip) VALUES ('Versuchter User Login während Account mit Verifizierungssperre versehen ist!', (SELECT id FROM $db->table_accounts WHERE username= ('" . $username_validated . "')), ('" . $client_ip . "'))") or die(mysqli_error($GLOBALS['connection']));
					$externalMsg = $GLOBALS['account_not_validated'].'<br />';
					$errorStatus = true;
				} //$accepted == 0
			} //$active = mysqli_fetch_object( $active_result )
			
			// If username and password were correct + Account is verified + Ban exists
			if (mysqli_num_rows($login_sql) == 1 && ($accepted) == 1 && mysqli_num_rows($ban_check) >= 1) {
			
			while($ban_data = mysqli_fetch_object($ban_check)) {
				$id 			= $ban_data->id;
				$ban_type		= $ban_data->ban_type;
				$ban_duration 	= $ban_data->ban_duration;
				$banned_by 		= $ban_data->banned_by;
				$banned_at		= $ban_data->banned_at;
			}
			
			switch($ban_type) {
				case 1:
						$realtime = time();
						$realtime_duration = ($banned_at + $ban_duration);
						$time_remaining	= ($realtime_duration - $realtime);
						
				if (date('Y-m-d', $realtime_duration) == date('Y-m-d')) {
					$realtime_duration = strftime('Heute, %H:%M', $realtime_duration);
				} elseif (date('Y-m-d', $realtime_duration) == date('Y-m-d', strtotime("Tomorrow"))) {
					$realtime_duration = strftime('Morgen, %H:%M', $realtime_duration);
				} elseif (date('Y-m-d', $realtime_duration) > date('Y-m-d', strtotime("Tomorrow"))) {
					$realtime_duration = strftime($GLOBALS['account_ban_temp_time_first']."%A, %d %B %Y %H:%M", $realtime_duration);
				}
						
					if($time_remaining >= 1) $string = $GLOBALS['account_ban_temp_first'] . $realtime_duration . $GLOBALS['account_ban_temp_second'];
					break;
					
				case 2:
					$string = $GLOBALS['account_ban_perm'];
					break;
			}
			
				$db->query("INSERT INTO $db->table_accountlogs (message_fail, account_id, sid, user_ip) VALUES ('Loginversuch während Accountsperre', (SELECT id FROM $db->table_accounts WHERE username= ('" . $username_validated . "')), ('" . $_SESSION['ID'] . "'), ('" . $client_ip . "'))") or die(mysqli_error($GLOBALS['connection']));
				$externalMsg = $string;
				$errorStatus = true;
			}
		
			// LOGIN ERFOLGREICH
			if (mysqli_num_rows($login_sql) == 1 && ($accepted) == 1 && (mysqli_num_rows($ban_check) == 0 || $time_remaining <= 0))
			{
				
				if (isset($_POST['StayLoggedIn']) && $_POST['StayLoggedIn']) {
					ini_set('session.gc_maxlifetime', time() + (3600 * 24 * 182));
					ini_set('session.gc_probability', 1);
					
					session_set_cookie_params(3600 * 24 * 182);
					
					session_cache_limiter('private');
					$cache_limiter = session_cache_limiter();
					session_cache_expire(60 * 24 * 182);
					$cache_expire = session_cache_expire();
					
					$persistentSess = '1';
				} //!isset( $_POST[ 'StayLoggedIn[]' ] )
				else {
					ini_set('session.gc_maxlifetime', time() + (3600 * 24));
					ini_set('session.gc_probability', 1);
					
					session_set_cookie_params(3600 * 24);
					session_cache_limiter('private');
					$cache_limiter = session_cache_limiter();
					
					session_cache_expire(60 * 24);
					$cache_expire = session_cache_expire();
					
					$persistentSess = '0';
				}
				
				// Prevent attempt to create multiple sessions
				if(!isset($_SESSION))
					session_start();
						
				$db->query($login_ip_update);
				$_SESSION['angemeldet'] = true;
				
				session_regenerate_id(true);
				$_SESSION['ID'] = session_id();
				
				// Force a session id to get generated
				if(!isset($_SESSION['ID']) || empty($_SESSION['ID']))
				{
					$_SESSION['ID'] = md5(uniqid(). ':' .time());
				}
				if(!isset($_COOKIE['PHPSESSID']))
					setcookie("PHPSESSID", $_SESSION['ID'], ini_get("session.gc_maxlifetime"), '/');
				
				
				$db->query("UPDATE $db->table_accounts SET logged_in= '1', sid= ('" . $_SESSION['ID'] . "'), last_login= NOW(), last_login_ip= ('" . $client_ip . "'), persistent_session_status= ('" . $persistentSess . "') WHERE username= ('" . $username_validated . "') AND pass_hash=('" . $pass_hash_final . "')") or die(mysqli_error($GLOBALS['connection']));
				$db->query("UPDATE $db->table_accdata SET login_status='1' WHERE account_id=(SELECT id FROM $db->table_accounts WHERE username=('" . $username_validated . "') AND pass_hash=('" . $pass_hash_final . "'))");
				
				require('./system/controller/security/permission_system.php');
				
				$set_user_session_check = $db->query("SELECT sid FROM $db->table_sessions WHERE id=(SELECT id FROM $db->table_accounts WHERE username= ('" . $username_validated . "'))");
				if (mysqli_num_rows($set_user_session_check) != 0) {
					$start_session = ("UPDATE $db->table_sessions SET active=1, sid=('" . $_SESSION['ID'] . "'), current_user_ip=('" . $client_ip . "'), session_started=NOW(), persistent_session_status= ('" . $persistentSess . "') WHERE id=(SELECT id FROM $db->table_accounts WHERE username= ('" . $username_validated . "'))") or die(mysqli_error($GLOBALS['connection']));
					$db->query($start_session);
					if (!$start_session) {
						$db->query("INSERT INTO $db->table_accountlogs (message_fail, account_id, sid, user_ip, user_agent) VALUES ('Query Error! Konnte Sitzungsdaten nicht aktualisieren!', (SELECT id FROM $db->table_accounts WHERE username= ('" . $username_validated . "')), ('" . $_SESSION['ID'] . "'), ('" . $client_ip . "'), ('" . $userAgent . "'))") or die(mysqli_error($GLOBALS['connection']));
						$errorStatus = true;
						$criticalLoginError = true;
					} //!$start_session
					else {
						$db->query("INSERT INTO $db->table_accountlogs (message, account_id, sid, user_ip, user_agent) VALUES ('User erfolgreich eingeloggt', (SELECT id FROM $db->table_accounts WHERE username= ('" . $username_validated . "')), ('" . $_SESSION['ID'] . "'), ('" . $client_ip . "'), ('" . $userAgent . "'))") or die(mysqli_error($GLOBALS['connection']));
						$errorStatus = false;
						$successStatus = true;
					}
				} //mysqli_num_rows( $set_user_session_check ) != 0
				else {
					$set_user_session = ("INSERT INTO $db->table_sessions (id, active, current_user_ip, sid, session_started) VALUES ((SELECT id FROM $db->table_accounts WHERE username= ('" . $username_validated . "')), '1', ('" . $client_ip . "'), ('" . $_SESSION['ID'] . "'), NOW())");
					$db->query($set_user_session);
					if ($set_user_session) {
						$db->query("INSERT INTO $db->table_accountlogs (message, account_id, sid, user_ip, user_agent) VALUES ('User erfolgreich eingeloggt', (SELECT id FROM $db->table_accounts WHERE username= ('" . $username_validated . "')), ('" . $_SESSION['ID'] . "'), ('" . $client_ip . "'), ('" . $userAgent . "'))") or die(mysqli_error($GLOBALS['connection']));
						$errorStatus = false;
						$successStatus = true;
						$externalMsg = $GLOBALS["login_success"];
					} //$set_user_session
					else {
						$db->query("INSERT INTO $db->table_accountlogs (message, account_id, sid, user_ip, user_agent) VALUES ('Query Error! Konnte Sitzungsdaten nicht aktualisieren!', (SELECT id FROM $db->table_accounts WHERE username= ('" . $username_validated . "')), ('" . $_SESSION['ID'] . "'), ('" . $client_ip . "'), ('" . $userAgent . "'))") or die(mysqli_error($GLOBALS['connection']));
						$errorStatus = true;
						$criticalLoginError = true;
					}
				}
				
			} //mysqli_num_rows( $login_sql ) == 1 && ( $accepted ) == 1
		}
			else
		{
			$errorStatus = true;
			$successStatus = false;
			$externalMsg = 'Ungültiger Username oder Passwort';
		}
    }
    
	$login_dataArray = array(
        'everythingEmpty' 	=> $everythingEmpty,

		'criticalLoginError'=> $criticalLoginError,
        'errorStatus' 		=> $errorStatus,
		'successStatus' 	=> $successStatus, 
		
		'externalMsg'		=> $externalMsg
    );
    return $login_dataArray;
	
}
?>