<?php
// Start output buffering for working session cookies
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
    ob_start("ob_gzhandler");
else
    ob_start();
	
function login()
{
	global $langGlobal;

	ini_set('display_startup_errors',1);
	ini_set('display_errors',1);
	error_reporting(-1);

	include('../../classes/akb_mysqli.class.php');
	include('../../classes/akb_main.class.php');

	if (!isset($db) || $db == NULL)
	{
		$db = new Database();
		$connection = $db->mysqli_db_connect();
	}
	if (!isset($main) || $main == NULL)
		$main = new Board($db, $connection);

	$main->useFile('./system/controller/security/login_security.php');

	
	$lang = $main->useFile('./system/controller/processors/lang_processor.php');
	if(!$lang) echo 'Locale not found.';
	$main->useFile('./system/auth/auth.php');
	
	if((isset($_SESSION['angemeldet']) && $_SESSION['angemeldet'] == true) && isset($_COOKIE['PHPSESSID'])) {
		echo $_COOKIE['PHPSESSID'].'Sie sind bereits eingeloggt!
		<meta http-equiv="refresh" content="3;url=/">';
		
		return;
	}
	
	// GET USER IP
	if (!isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$client_ip = $_SERVER['REMOTE_ADDR'];
	} //!isset( $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] )
	else {
		$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}

	$checkExpiration = $main->checkBadLoginExpiration($client_ip);
	$check_login = checkLogin($client_ip);
	
    // LOGIN BAN SYSTEM
	// If potential account hack or bruteforce was detected
	if($checkExpiration == true)
		{
		if(isset($check_login['bad_logins']) && isset($check_login['max_attempts_allowed']) && ($check_login['bad_logins'] >= $check_login['max_attempts_allowed']))
		{
			echo $langGlobal['ip_ban_temp'];
			return;
		}
		else
		{
		
		$username_dec = $_POST["username"];
		$password_dec = $_POST["password"];
		
		$user_chars = "/[\#$%^&\*\(\)\+=\-\[\]'\";,\.\/\{\}\|:<>\?~]/";
		if ((empty($username_dec)) || (empty($password_dec))) {
			$db->query("INSERT INTO $db->table_accountlogs (message_fail, user_ip) VALUES ('Es wurden nicht alle erforderlichen Informationen eingegeben', ('" . $client_ip . "'))") or die(mysqli_error($connection));
			echo $data_missed.'<br />';
			return;
		} //( empty( $username_dec ) ) || ( empty( $password_dec ) )
		else {
			
			// SCRIPT VARIABLES
			$username           = $username_dec;
			$password           = $password_dec;
			$username_validated = (mysqli_real_escape_string($connection, $username));
			$password_validated = (mysqli_real_escape_string($connection, $password));
			
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
		   
			
			// GET CRYPT LEVEL
			$getCrypt = $db->query("SELECT extra_val,crypt_level FROM $db->table_accounts WHERE username=('" . $username_validated . "')");
			while($row_c    = mysqli_fetch_object($getCrypt)) {
			
				$extra_val = $row_c->extra_val;
				$crypted  = $row_c->crypt_level;
			
			}
			
			// MORE DATA
			if(isset($crypted) && isset($extra_val)) {
			
			$sec         = $password_validated . $password_validated;
			$rand_val    = $crypted;
			$time_visual = $extra_val;
			
			for ($i = $rand_val; $i <= $rand_val; $i++) {
				
				$password_new = $password_validated . $i;
				
				$var = "$password_validated.$sec.$time_visual.$password_new";
				
				$pass_hash_      = md5(strtoupper($password_validated) . ":" . strtoupper($var));
				$pass_hash_final = md5(strtoupper($pass_hash_) . ":" . strtoupper($password_validated));
			} //$i = $rand_val; $i <= $rand_val; $i++
			
			}
			
			// CAPACITY FUNCTION
			
			$max_users           = $main->serverConfig("max_users");
			$user_counter        = $db->query("SELECT id FROM $db->table_sessions WHERE active= '1'") or die(mysqli_error($connection));
			$userLimitation		 = $main->serverConfig("user_capacity_system");
			
			// DB DATA
			$user_db = $db->query("SELECT username FROM $db->table_accounts WHERE username = ('" . $username_validated . "')") or die(mysqli_error($connection));
			
			if(isset($crypted) && isset($extra_val)) {
			
			$password_db = $db->query("SELECT pass_hash FROM $db->table_accounts WHERE pass_hash = ('" . $pass_hash_final . "') AND username = ('" . $username_validated . "') ") or die(mysqli_error($connection));
			$login_ip_update = ("UPDATE $db->table_accounts SET last_login_ip= ('" . $client_ip . "') WHERE username= ('" . $username_validated . "') AND pass_hash=('" . $pass_hash_final . "')") or die(mysqli_error($connection));
			$login_sql = $db->query("SELECT username, pass_hash FROM $db->table_accounts WHERE username= ('" . $username_validated . "') AND pass_hash= ('" . $pass_hash_final . "')") or die(mysqli_error($connection));
			$ban_check = $db->query("SELECT id, ban_type, ban_duration, banned_by FROM $db->table_ban WHERE id= ( SELECT id from $db->table_accounts WHERE username=('" . $username_validated . "') AND pass_hash= ('" . $pass_hash_final . "') )") or die(mysqli_error($connection));
			
			}
			
			// PROCESSING START //
			
			// CHECK FOR CAPACITY //
			if ($userLimitation >= 1) {
				if ($max_users <= mysqli_num_rows($user_counter)) {
					$db->query("INSERT INTO $db->table_accountlogs (message_fail, user_ip) VALUES ('Server Kapazität wurde überschritten. User wurde nicht eingeloggt.', ('" . $client_ip . "'))") or die(mysqli_error($connection));
					echo $langGlobal['max_users_reached_string_first'] .  $langGlobal['max_users']  . $langGlobal['max_users_reached_string_sec'].'<br />';
					return;
				} //$max_users <= mysqli_num_rows( $user_counter )
			}
			
			// ILLEGAL CHARACTERS
			if (preg_match($user_chars, $username_validated)) {
				$db->query("INSERT INTO $db->table_accountlogs (message_fail, user_ip) VALUES ('Username enthält ungueltige Zeichen', ('" . $client_ip . "'))") or die(mysqli_error($connection));
				$main->increaseBadLogins($client_ip);
				echo $langGlobal['username_illegal_chars'].'<br />';
				return;
			} //preg_match( $user_chars, $username_validated )
			
			// IF ACCOUNT DONT EXISTS
			if (mysqli_num_rows($user_db) == 0 && ((isset($password_db) && mysqli_num_rows($password_db) == 0) || !isset($password_db))) {
				$db->query("INSERT INTO $db->table_accountlogs (message_fail, user_ip) VALUES ('Ungültiger Username und Passwort!', ('" . $client_ip . "'))") or die(mysqli_error($connection));
				$main->increaseBadLogins($client_ip);
				echo $langGlobal['invalid_pass_or_username'].'<br />';
				return;
			} //mysqli_num_rows( $user_db ) == 0 and mysqli_num_rows( $password_db ) == 0
			
			// CHECK IF PASSWORD IS CORRECT        
			if (mysqli_num_rows($user_db) == 1 and mysqli_num_rows($password_db) == 0) {
				$db->query("INSERT INTO $db->table_accountlogs (message_fail, account_id, user_ip) VALUES ('Ungültiges Passwort', (SELECT id FROM $db->table_accounts WHERE username= ('" . $username_validated . "')), ('" . $client_ip . "'))") or die(mysqli_error($connection));
				$main->increaseBadLogins($client_ip);
				sleep(2);
				echo $langGlobal['invalid_pass_or_username'].'<br />';
				return;
			} //mysqli_num_rows( $user_db ) == 1 and mysqli_num_rows( $password_db ) == 0
			
			// Protection-system
			$get_active    = "SELECT accepted FROM $db->table_accounts WHERE username=('" . $username_validated . "')";
			$active_result = $db->query($get_active);
			while ($active = mysqli_fetch_object($active_result)) {
				$accepted = $active->accepted;
				if ($accepted == 0) {
					$db->query("INSERT INTO protection_system_logs (message, account_id, user_ip) VALUES ('Versuchter User Login während Account mit Verifizierungssperre versehen ist!', (SELECT id FROM $db->table_accounts WHERE username= ('" . $username_validated . "')), ('" . $client_ip . "'))") or die(mysqli_error($connection));
					echo $langGlobal['account_not_validated'].'<br />';
					return;
				} //$accepted == 0
			} //$active = mysqli_fetch_object( $active_result )
			
			// IP BAN SYSTEM START
			if (mysqli_num_rows($login_sql) == 1 && ($accepted) == 1 && mysqli_num_rows($ban_check) >= 1 && $main->serverConfig("login_ban_system") == TRUE) {
			
			while($ban_data = mysqli_fetch_object($ban_check)) {
				$id 			= $ban_data->id;
				$ban_type		= $ban_data->ban_type;
				$ban_duration 	= $ban_data->ban_duration;
				$banned_by 		= $ban_data->banned_by;
			}
			
			switch($ban_type) {
				case 1:
					if($ban_duration >= 1) $string = $langGlobal['account_ban_temp_first'] . $langGlobal['realtime_duration'] . $langGlobal['account_ban_temp_second'];
					break;
				
				case 2:
					$string = $langGlobal['account_ban_perm'];
					break;
			}
			
				$db->query("INSERT INTO $db->table_accountlogs (message_fail, account_id, sid, user_ip) VALUES ('Loginversuch während Accountsperre', (SELECT id FROM $db->table_accounts WHERE username= ('" . $username_validated . "')), ('" . $_SESSION['ID'] . "'), ('" . $client_ip . "'))") or die(mysqli_error($connection));
				echo $string;
				return;
			}
			// IP BAN SYSTEM END
			
			// LOGIN ERFOLGREICH
			if (mysqli_num_rows($login_sql) == 1 && ($accepted) == 1 && mysqli_num_rows($ban_check) == 0) {
				
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
				
				
				$db->query("UPDATE $db->table_accounts SET logged_in= '1', sid= ('" . $_SESSION['ID'] . "'), last_login= NOW(), last_login_ip= ('" . $client_ip . "'), persistent_session_status= ('" . $persistentSess . "') WHERE username= ('" . $username_validated . "') AND pass_hash=('" . $pass_hash_final . "')") or die(mysqli_error($connection));
				$db->query("UPDATE $db->table_accdata SET login_status='1' WHERE account_id=(SELECT id FROM $db->table_accounts WHERE username=('" . $username_validated . "') AND pass_hash=('" . $pass_hash_final . "'))");
				
				$main->useFile('./system/controller/security/permission_system.php');
				
				$set_user_session_check = $db->query("SELECT sid FROM $db->table_sessions WHERE id=(SELECT id FROM $db->table_accounts WHERE username= ('" . $username_validated . "'))");
				if (mysqli_num_rows($set_user_session_check) != 0) {
					$start_session = ("UPDATE $db->table_sessions SET active=1, sid=('" . $_SESSION['ID'] . "'), current_user_ip=('" . $client_ip . "'), session_started=NOW(), persistent_session_status= ('" . $persistentSess . "') WHERE id=(SELECT id FROM $db->table_accounts WHERE username= ('" . $username_validated . "'))") or die(mysqli_error($connection));
					$db->query($start_session);
					if (!$start_session) {
						$db->query("INSERT INTO $db->table_accountlogs (message_fail, account_id, sid, user_ip, user_agent) VALUES ('Query Error! Konnte Sitzungsdaten nicht aktualisieren!', (SELECT id FROM $db->table_accounts WHERE username= ('" . $username_validated . "')), ('" . $_SESSION['ID'] . "'), ('" . $client_ip . "'), ('" . $userAgent . "'))") or die(mysqli_error($connection));
						echo $langGlobal['login_critical_error'];
						return;
					} //!$start_session
					else {
						$db->query("INSERT INTO $db->table_accountlogs (message, account_id, sid, user_ip, user_agent) VALUES ('User erfolgreich eingeloggt', (SELECT id FROM $db->table_accounts WHERE username= ('" . $username_validated . "')), ('" . $_SESSION['ID'] . "'), ('" . $client_ip . "'), ('" . $userAgent . "'))") or die(mysqli_error($connection));
					}
				} //mysqli_num_rows( $set_user_session_check ) != 0
				else {
					$set_user_session = ("INSERT INTO $db->table_sessions (id, active, current_user_ip, sid, session_started) VALUES ((SELECT id FROM $db->table_accounts WHERE username= ('" . $username_validated . "')), '1', ('" . $client_ip . "'), ('" . $_SESSION['ID'] . "'), NOW())");
					$db->query($set_user_session);
					if ($set_user_session) {
						$db->query("INSERT INTO $db->table_accountlogs (message, account_id, sid, user_ip, user_agent) VALUES ('User erfolgreich eingeloggt', (SELECT id FROM $db->table_accounts WHERE username= ('" . $username_validated . "')), ('" . $_SESSION['ID'] . "'), ('" . $client_ip . "'), ('" . $userAgent . "'))") or die(mysqli_error($connection));
					} //$set_user_session
					else {
						$db->query("INSERT INTO $db->table_accountlogs (message, account_id, sid, user_ip, user_agent) VALUES ('Query Error! Konnte Sitzungsdaten nicht aktualisieren!', (SELECT id FROM $db->table_accounts WHERE username= ('" . $username_validated . "')), ('" . $_SESSION['ID'] . "'), ('" . $client_ip . "'), ('" . $userAgent . "'))") or die(mysqli_error($connection));
						echo $langGlobal['login_critical_error'];
						return;
					}
				}
				echo '
					<span id="response_success" class="responseSuccess">
					'.$langGlobal['login_success'].'
					</span>
					<meta http-equiv="refresh" content="3;url=?page=Portal">';

				
			} //mysqli_num_rows( $login_sql ) == 1 && ( $accepted ) == 1
		}
		
		}
	
  }
  else {
	echo $ip_ban_temp;
    return;
  }
}

if(isset($_POST["username"]) && isset($_POST["password"])) login();

// End of the output buffering. Also the end of all content. Cookies can't be set after this.
	ob_end_flush();
?>